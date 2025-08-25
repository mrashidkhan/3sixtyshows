<?php

namespace App\Http\Controllers;

use App\Models\Show;
use App\Models\TicketHold;
use App\Models\Booking;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // Add this line
use Illuminate\Support\Str; // Add this line if Str is also missing
use App\Models\BookingItem;
use App\Services\PayPalService;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class GeneralAdmissionController extends Controller
{
    // Add to GeneralAdmissionController
public function paypalSuccess(Request $request, $slug)
{
    $token = $request->query('token');
    $payerId = $request->query('PayerID');

    if (!$token || !$payerId) {
        return redirect()->route('ga-booking.failed', $slug)
            ->with('error', 'PayPal payment verification failed.');
    }

    try {
        // Find booking by payment reference
        $booking = Booking::where('payment_reference', $token)->firstOrFail();

        $paypal = new \App\Services\PayPalService();
        $captureResult = $paypal->capturePayment($token);

        if ($captureResult['status'] === 'COMPLETED') {
            $booking->update([
                'status' => 'confirmed',
                'payment_status' => 'paid',
                'paypal_payer_id' => $payerId,
                'paid_at' => now()
            ]);

            return redirect()->route('ga-booking.success', [
                'slug' => $slug,
                'bookingNumber' => $booking->booking_number
            ])->with('success', 'Payment completed successfully!');
        }

    } catch (\Exception $e) {
        Log::error('PayPal success error: ' . $e->getMessage());
    }

    return redirect()->route('ga-booking.failed', $slug)
        ->with('error', 'Payment verification failed.');
}

public function paypalCancel(Request $request, $slug)
{
    return redirect()->route('ga-booking.failed', $slug)
        ->with('warning', 'Payment was cancelled.');
}

    // Show ticket selection page
    public function showTicketSelection($slug)
    {
        $show = Show::where('slug', $slug)
            ->with([
                'venue',
                'ticketTypes' => function ($query) {
                    $query->where('is_active', true)->orderBy('display_order');
                },
            ])
            ->firstOrFail();

        // Check if show is bookable
        if ($show->start_date->isPast()) {
            return redirect()->route('show.details', $slug)->with('error', 'This show has already passed.');
        }

        if ($show->redirect && $show->redirect_url) {
            return redirect()->away($show->redirect_url);
        }

        // Clean up expired holds
        TicketHold::cleanupExpired();

        $availableTicketTypes = collect();
        foreach ($show->ticketTypes as $ticketType) {
            $available = $show->getAvailableCapacityForTicketType($ticketType->id);
            if ($available > 0) {
                $ticketType->available_quantity = min($available, 10);
                $availableTicketTypes->push($ticketType);
            }
        }

        if ($availableTicketTypes->isEmpty()) {
            return response()->json([
                'error' => 'Sorry, this show is sold out.',
                'show' => $show->title,
                'debug_info' => [
                    'total_ticket_types' => $show->ticketTypes->count(),
                    'active_ticket_types' => $show->ticketTypes->where('is_active', true)->count(),
                    'ticket_types_details' => $show->ticketTypes->map(function ($type) use ($show) {
                        return [
                            'name' => $type->name,
                            'is_active' => $type->is_active,
                            'capacity' => $type->capacity,
                            'available' => $show->getAvailableCapacityForTicketType($type->id),
                        ];
                    }),
                ],
            ]);
        }

        return view('pages.ticket-selection', [
            'show' => $show,
            'availableTicketTypes' => $availableTicketTypes,
        ]);
    }

    public function selectTickets(Request $request, $slug)
    {
        $show = Show::where('slug', $slug)->firstOrFail();

        $request->validate([
            'tickets' => 'required|array|min:1',
            'tickets.*.ticket_type_id' => 'required|exists:ticket_types,id',
            'tickets.*.quantity' => 'required|integer|min:0|max:10',
        ]);

        $sessionId = session()->getId();
        $totalTickets = 0;
        $totalAmount = 0;
        $ticketBreakdown = [];

        // Clean up expired holds
        TicketHold::cleanupExpired();

        try {
            // Clean up any existing holds for this session
            TicketHold::where('session_id', $sessionId)->delete();

            foreach ($request->tickets as $ticketData) {
                $quantity = (int) $ticketData['quantity'];

                if ($quantity <= 0) continue;

                $ticketType = $show->ticketTypes()->findOrFail($ticketData['ticket_type_id']);

                // Check availability
                $available = $show->getAvailableCapacityForTicketType($ticketType->id);
                if ($quantity > $available) {
                    return back()
                        ->withErrors(['error' => "Only {$available} tickets available for {$ticketType->name}"])
                        ->withInput();
                }

                // Create hold
                TicketHold::holdTickets($show->id, $ticketType->id, $quantity, $sessionId, 15);

                $ticketBreakdown[] = [
                    'ticket_type_id' => $ticketType->id,
                    'ticket_type_name' => $ticketType->name,
                    'quantity' => $quantity,
                    'unit_price' => $ticketType->price,
                    'total_price' => $ticketType->price * $quantity,
                ];

                $totalTickets += $quantity;
                $totalAmount += $ticketType->price * $quantity;
            }

            if ($totalTickets === 0) {
                return back()
                    ->withErrors(['error' => 'Please select at least one ticket with quantity greater than 0'])
                    ->withInput();
            }

            // Store in session
            session([
                'booking_data' => [
                    'show_id' => $show->id,
                    'ticket_breakdown' => $ticketBreakdown,
                    'total_tickets' => $totalTickets,
                    'subtotal' => $totalAmount,
                    'expires_at' => now()->addMinutes(15)->toISOString(),
                    'session_id' => $sessionId,
                ],
            ]);

            return redirect()->route('ga-booking.customer-details', $slug);
        } catch (\Exception $e) {
            return back()
                ->withErrors(['error' => 'Booking failed: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function showCustomerDetails($slug)
    {
        $show = Show::where('slug', $slug)->firstOrFail();
        $bookingData = session('booking_data');

        if (!$bookingData) {
            return redirect()->route('ga-booking.tickets', $slug)
                ->with('error', 'Booking session expired. Please select tickets again.');
        }

        // Calculate fees
        $subtotal = $bookingData['subtotal'];
        $serviceFee = max($subtotal * 0.03, 2.0);
        $processingFee = $bookingData['total_tickets'] * 1.5;
        $grandTotal = $subtotal + $serviceFee + $processingFee;

        return view('pages.customer-details', [
            'show' => $show,
            'bookingData' => $bookingData,
            'subtotal' => $subtotal,
            'serviceFee' => $serviceFee,
            'processingFee' => $processingFee,
            'grandTotal' => $grandTotal,
        ]);
    }

    // In GeneralAdmissionController.php processCustomerDetails method:

public function processCustomerDetails(Request $request, $slug)
{
    $show = Show::where('slug', $slug)->firstOrFail();
    $bookingData = session('booking_data');

    if (!$bookingData) {
        return redirect()->route('ga-booking.tickets', $slug)
            ->with('error', 'Booking session expired. Please select tickets again.');
    }

    // Validate customer information
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|max:255',
        'phone' => 'required|string|max:20',
        'terms' => 'required|accepted',
        'newsletter' => 'nullable|boolean',
    ]);

    // Store customer data in session
    session([
        'customer_data' => [
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'newsletter' => $request->has('newsletter')
        ]
    ]);

    // Check if user is logged in
    if (auth()->check()) {
        // User already logged in, redirect to payment
        return redirect()->route('ga-booking.payment', $slug);
    }

    // Store intended URL for after login
    session(['intended_booking_url' => route('ga-booking.payment', $slug)]);
    session(['booking_message' => 'Please login or register to complete your secure booking for ' . $show->title]);


    return redirect()->route('user_login')
        ->with('info', 'Please login or register to complete your booking securely.');
}

    public function showPayment($slug)
    {
        $show = Show::where('slug', $slug)->firstOrFail();
        $bookingData = session('booking_data');
        $customerData = session('customer_data');

        // Check if required session data exists
        if (!$bookingData || !$customerData) {
            return redirect()->route('ga-booking.tickets', $slug)
                ->with('error', 'Booking session expired. Please start again.');
        }

        // Check if booking hasn't expired
        if (now() > \Carbon\Carbon::parse($bookingData['expires_at'])) {
            TicketHold::where('session_id', $bookingData['session_id'])->delete();
            session()->forget(['booking_data', 'customer_data']);

            return redirect()->route('ga-booking.tickets', $slug)
                ->with('error', 'Booking session expired. Please select tickets again.');
        }

        // Calculate fees
        $subtotal = $bookingData['subtotal'];
        $serviceFee = max($subtotal * 0.03, 2.0);
        $processingFee = $bookingData['total_tickets'] * 1.5;
        $grandTotal = $subtotal + $serviceFee + $processingFee;

        // FIXED: Change view name to booking-payment
        return view('pages.booking-payment', [
            'show' => $show,
            'bookingData' => $bookingData,
            'customerData' => $customerData,
            'subtotal' => $subtotal,
            'serviceFee' => $serviceFee,
            'processingFee' => $processingFee,
            'grandTotal' => $grandTotal,
        ]);
    }

    public function processPayment(Request $request, $slug)
{
    $show = Show::where('slug', $slug)->firstOrFail();

    // Get booking data from session
    $bookingData = session('booking_data');
    $customerData = session('customer_data');
    if (!$bookingData || !$customerData) {
        return redirect()->route('ga-booking.tickets', $slug)
            ->with('error', 'Booking session expired. Please start again.');
    }


//    $request->validate([
//     'payment_method' => 'required|in:card,paypal',
//     'card_number' => [
//         'required_if:payment_method,card',
//         'string',
//         'regex:/^[0-9\s]{13,19}$/', // Allow digits and spaces, 13-19 characters
//         function ($attribute, $value, $fail) {
//             // Remove spaces and validate
//             $cleanNumber = preg_replace('/\s+/', '', $value);

//             // Check if it's exactly 16 digits after cleaning
//             if (!preg_match('/^\d{16}$/', $cleanNumber)) {
//                 $fail('The card number must be exactly 16 digits.');
//                 return;
//             }

//             // Validate using Luhn algorithm
//             if (!$this->validateLuhn($cleanNumber)) {
//                 $fail('The card number is not valid.');
//             }
//         }
//     ],
//     'card_expiry' => 'required_if:payment_method,card|string|size:4|regex:/^(0[1-9]|1[0-2])([0-9]{2})$/',
//     'card_cvv' => 'required_if:payment_method,card|numeric|digits_between:3,4',
//     'card_holder_name' => 'required_if:payment_method,card|string|max:255|regex:/^[a-zA-Z\s]+$/',
//     'billing_address' => 'required_if:payment_method,card|string|max:255',
//     'billing_city' => 'required_if:payment_method,card|string|max:100',
//     'billing_state' => 'required_if:payment_method,card|string|max:100',
//     'billing_zip' => 'required_if:payment_method,card|string|max:20',
// ]);

// $request->validate([
//     'payment_method' => 'required|in:card,paypal',
//     'card_number' => [
//         'required_if:payment_method,card',
//         'string',
//         'regex:/^[0-9\s]{13,19}$/',
//         function ($attribute, $value, $fail) {
//             $cleanNumber = preg_replace('/\s+/', '', $value);

//             // Validate length
//             if (!preg_match('/^\d{13,19}$/', $cleanNumber)) {
//                 $fail('The card number must be between 13-19 digits.');
//                 return;
//             }

//             // Detect card type
//             $cardType = $this->detectCardType($cleanNumber);
//             if (!$cardType) {
//                 $fail('The card number format is not recognized.');
//                 return;
//             }

//             // Validate Luhn
//             if (!$this->validateLuhn($cleanNumber)) {
//                 $fail('The card number is not valid.');
//             }
//         }
//     ],
// 'card_holder_name' => 'required_if:payment_method,card|string|max:255|regex:/^[a-zA-Z\s]+$/',
// 'card_cvv' => 'required_if:payment_method,card|numeric|digits_between:3,4',
// 'card_holder_name' => 'required_if:payment_method,card|string|max:255|regex:/^[a-zA-Z\s]+$/',
// 'billing_address' => 'required_if:payment_method,card|string|max:255',
// 'billing_city' => 'required_if:payment_method,card|string|max:100',
// 'billing_state' => 'required_if:payment_method,card|string|max:100',
// 'billing_zip' => 'required_if:payment_method,card|string|max:20',
// ]);

$request->validate([
    'payment_method' => 'required|in:card,paypal',
    'card_number' => [
        'required_if:payment_method,card',
        'string',
        'regex:/^[0-9\s]{13,19}$/',
        function ($attribute, $value, $fail) {
            $cleanNumber = preg_replace('/\s+/', '', $value);

            if (!preg_match('/^\d{13,19}$/', $cleanNumber)) {
                $fail('The card number must be between 13-19 digits.');
                return;
            }

            $cardType = $this->detectCardType($cleanNumber);
            if (!$cardType) {
                $fail('The card number format is not recognized.');
                return;
            }

            if (!$this->validateLuhn($cleanNumber)) {
                $fail('The card number is not valid.');
            }
        }
    ],
    'card_expiry' => 'required_if:payment_method,card|string|size:4',
    'card_cvv' => 'required_if:payment_method,card|numeric|digits_between:3,4',
    'card_holder_name' => 'required_if:payment_method,card|string|max:255',
    'billing_address' => 'required_if:payment_method,card|string|max:255',
    'billing_city' => 'required_if:payment_method,card|string|max:100',
    'billing_state' => 'required_if:payment_method,card|string|max:100',
    'billing_zip' => 'required_if:payment_method,card|string|max:20',
]);
    try {
        DB::beginTransaction();

        // Clean card data if needed
        $cardData = $this->cleanCardData($request);

        // Create the booking
        $booking = Booking::create([
            'user_id' => auth()->id(),
            'show_id' => $show->id,
            'booking_number' => 'BK-' . strtoupper(Str::random(8)),
            'customer_name' => $customerData['name'],
            'customer_email' => $customerData['email'],
            'customer_phone' => $customerData['phone'],
            // 'total_tickets' => $bookingData['total_tickets'],
            'booking_date' => now(),
            'total_amount' => $bookingData['subtotal'],
            'number_of_tickets' => $bookingData['total_tickets'],
            'service_fee' => max($bookingData['subtotal'] * 0.03, 2.0),
            'processing_fee' => $bookingData['total_tickets'] * 1.5,
            'grand_total' => $bookingData['subtotal'] + max($bookingData['subtotal'] * 0.03, 2.0) + ($bookingData['total_tickets'] * 1.5),
            'status' => 'pending',
            'payment_method' => $request->payment_method,
            'payment_status' => 'pending'
        ]);

        // Create booking items
        foreach ($bookingData['ticket_breakdown'] as $ticketData) {
            \App\Models\BookingItem::create([
                'booking_id' => $booking->id,
                'ticket_type_id' => $ticketData['ticket_type_id'],
                'quantity' => $ticketData['quantity'],
                'unit_price' => $ticketData['unit_price'],
                'total_price' => $ticketData['total_price']
            ]);
        }

        DB::commit();

        // Clear session data
        session()->forget(['booking_data', 'customer_data']);

        // Process payment based on method
        if ($request->payment_method === 'paypal') {
            return $this->processPayPalPayment($booking);
        } elseif ($request->payment_method === 'card') {
            // For card payments, simulate success for testing
            // In production, integrate with Stripe or another card processor
            $booking->update([
                'status' => 'confirmed',
                'payment_status' => 'paid',
                'paid_at' => now()
            ]);

            // Generate tickets
            $booking->generateTickets();

            return redirect()->route('ga-booking.success', [
                'slug' => $slug,
                'bookingNumber' => $booking->booking_number
            ])->with('success', 'Payment completed successfully!');
        }

        return redirect()->route('ga-booking.success', [
            'slug' => $slug,
            'bookingNumber' => $booking->booking_number
        ])->with('success', 'Booking created successfully!');

    } catch (\Exception $e) {
        // DB::rollback();
        // Log::error('Booking creation error: ' . $e->getMessage());
        // return back()->with('error', 'Booking failed. Please try again.');

        DB::rollback();
    Log::error('Booking creation error: ' . $e->getMessage());
    Log::error('Error line: ' . $e->getLine());
    Log::error('Error file: ' . $e->getFile());
    Log::error('Stack trace: ' . $e->getTraceAsString());

    // Temporarily show actual error (remove in production)
    return back()->with('error', 'Booking failed: ' . $e->getMessage() . ' on line ' . $e->getLine());
    }
}

/**
 * Process PayPal payment
 */
// In GeneralAdmissionController.php, replace processPayPalPayment method:

private function processPayPalPayment($booking)
{
    try {
        $paypal = new \App\Services\PayPalService();

        // Create proper return URLs with the booking's show slug
        $returnUrl = route('ga-booking.paypal-success', ['slug' => $booking->show->slug]);
        $cancelUrl = route('ga-booking.paypal-cancel', ['slug' => $booking->show->slug]);

        Log::info('Creating PayPal order for booking', [
            'booking_id' => $booking->id,
            'booking_number' => $booking->booking_number,
            'amount' => $booking->grand_total,
            'return_url' => $returnUrl,
            'cancel_url' => $cancelUrl
        ]);

        $order = $paypal->createOrder(
            $booking->grand_total, // Use grand_total which includes fees
            "Ticket Purchase - {$booking->show->title}",
            $booking->booking_number,
            $returnUrl,
            $cancelUrl
        );

        if (!isset($order['id'])) {
            throw new \Exception('PayPal order creation failed - no order ID returned');
        }

        // Store PayPal order ID in payment_reference field
        $booking->update([
            'payment_reference' => $order['id']
        ]);

        // Get approval URL
        $approvalUrl = collect($order['links'])
            ->firstWhere('rel', 'approve')['href'] ?? null;

        if (!$approvalUrl) {
            throw new \Exception('PayPal approval URL not found in order response');
        }

        Log::info('PayPal order created successfully', [
            'booking_id' => $booking->id,
            'paypal_order_id' => $order['id'],
            'approval_url' => $approvalUrl
        ]);

        // Redirect to PayPal
        return redirect($approvalUrl);

    } catch (\Exception $e) {
        Log::error('PayPal payment error', [
            'booking_id' => $booking->id,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);

        // Update booking status to failed
        $booking->update([
            'status' => 'cancelled',
            'payment_status' => 'failed'
        ]);

        return back()->with('error', 'PayPal payment failed: ' . $e->getMessage());
    }
}

// Also update your paypalSuccess method:
public function paypalSuccess(Request $request, $slug)
{
    $token = $request->query('token');        // PayPal order ID
    $payerId = $request->query('PayerID');    // PayPal payer ID

    Log::info('PayPal success callback received', [
        'slug' => $slug,
        'token' => $token,
        'payer_id' => $payerId,
        'all_params' => $request->all()
    ]);

    if (!$token || !$payerId) {
        Log::error('PayPal success callback missing required parameters', [
            'token' => $token,
            'payer_id' => $payerId
        ]);

        return redirect()->route('ga-booking.failed', $slug)
            ->with('error', 'PayPal payment verification failed - missing parameters.');
    }

    try {
        // Find booking by payment reference (PayPal order ID)
        $booking = Booking::where('payment_reference', $token)->first();

        if (!$booking) {
            Log::error('Booking not found for PayPal token', ['token' => $token]);
            throw new \Exception('Booking not found for this PayPal transaction');
        }

        Log::info('Found booking for PayPal payment', [
            'booking_id' => $booking->id,
            'booking_number' => $booking->booking_number,
            'token' => $token
        ]);

        $paypal = new \App\Services\PayPalService();

        // Capture the payment
        $captureResult = $paypal->capturePayment($token);

        Log::info('PayPal capture result', [
            'booking_id' => $booking->id,
            'capture_result' => $captureResult
        ]);

        if ($captureResult['status'] === 'COMPLETED') {
            // Payment was successfully captured
            $booking->update([
                'status' => 'confirmed',
                'payment_status' => 'paid',
                'paypal_payer_id' => $payerId,
                'paid_at' => now(),
                'confirmed_at' => now()
            ]);

            // Generate tickets
            $booking->generateTickets();

            Log::info('PayPal payment completed successfully', [
                'booking_id' => $booking->id,
                'booking_number' => $booking->booking_number
            ]);

            return redirect()->route('ga-booking.success', [
                'slug' => $slug,
                'bookingNumber' => $booking->booking_number
            ])->with('success', 'Payment completed successfully!');
        } else {
            Log::error('PayPal capture failed', [
                'booking_id' => $booking->id,
                'capture_status' => $captureResult['status'] ?? 'unknown',
                'capture_result' => $captureResult
            ]);

            throw new \Exception('PayPal payment capture failed with status: ' . ($captureResult['status'] ?? 'unknown'));
        }

    } catch (\Exception $e) {
        Log::error('PayPal success processing error', [
            'token' => $token,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);

        // Update booking if found
        if (isset($booking)) {
            $booking->update([
                'status' => 'cancelled',
                'payment_status' => 'failed'
            ]);
        }
    }

    return redirect()->route('ga-booking.failed', $slug)
        ->with('error', 'Payment verification failed: ' . ($e->getMessage() ?? 'Unknown error'));
}

    // ADDED: Missing processPaymentMethod function
    private function processPaymentMethod($request, $amount, $booking)
    {
        $method = $request->payment_method;

        switch ($method) {
            case 'card':
                // Simulate card payment (replace with actual Stripe integration)
                $success = true; // Simulate successful payment
                $transactionId = 'TXN_' . strtoupper(\Illuminate\Support\Str::random(12));

                if ($success) {
                    return [
                        'success' => true,
                        'transaction_id' => $transactionId,
                        'message' => 'Payment processed successfully'
                    ];
                } else {
                    return [
                        'success' => false,
                        'message' => 'Card payment failed. Please try again.'
                    ];
                }

            case 'paypal':
                // Create PayPal order
                try {
                    $paypal = new \App\Services\PayPalService();

                    // Create order with booking reference and custom return URLs
                    $returnUrl = route('ga-booking.paypal-return', ['slug' => $booking->show->slug]);
                    $cancelUrl = route('ga-booking.paypal-return', ['slug' => $booking->show->slug, 'cancel' => 'true']);

                    $order = $paypal->createOrder(
                        $amount,
                        'Ticket Purchase for ' . $booking->show->title,
                        $booking->booking_number,
                        $returnUrl,
                        $cancelUrl
                    );

                    // Store PayPal order ID in booking for later reference
                    $booking->update([
                        'payment_reference' => $order['id']
                    ]);

                    // Find approval URL
                    $approvalUrl = null;
                    foreach ($order['links'] as $link) {
                        if ($link['rel'] === 'approve') {
                            $approvalUrl = $link['href'];
                            break;
                        }
                    }

                    // Return special indicator for PayPal payment
                    return [
                        'success' => 'paypal_redirect',
                        'paypal_order_id' => $order['id'],
                        'approval_url' => $approvalUrl
                    ];
                } catch (\Exception $e) {
                    \Log::error('PayPal payment error: ' . $e->getMessage());
                    return [
                        'success' => false,
                        'message' => 'PayPal payment failed: ' . $e->getMessage()
                    ];
                }

            default:
                return [
                    'success' => false,
                    'message' => 'Invalid payment method.'
                ];
        }
    }

    public function bookingSuccess($slug, $bookingNumber)
    {
        $show = Show::where('slug', $slug)->firstOrFail();
        $booking = \App\Models\Booking::where('booking_number', $bookingNumber)
            ->with(['user', 'bookingItems.ticketType', 'tickets'])
            ->firstOrFail();

        return view('pages.booking-success', [
            'show' => $show,
            'booking' => $booking,
        ]);
    }

    public function bookingFailed($slug)
    {
        $show = Show::where('slug', $slug)->firstOrFail();

        return view('pages.booking-failed', [
            'show' => $show,
        ]);
    }

    /**
     * Handle PayPal return (success or cancel)
     */
    public function paypalReturn(Request $request, $slug)
    {
        $bookingId = session('paypal_booking_id');

        if (!$bookingId) {
            return redirect()->route('ga-booking.tickets', $slug)
                ->with('error', 'Booking session expired. Please start again.');
        }

        $booking = \App\Models\Booking::findOrFail($bookingId);

        // Check if user cancelled
        if ($request->has('cancel')) {
            $booking->update([
                'status' => \App\Models\Booking::STATUS_CANCELLED,
                'payment_status' => \App\Models\Booking::PAYMENT_FAILED
            ]);

            return redirect()->route('ga-booking.failed', $slug)
                ->with('error', 'Payment was cancelled. Please try again.');
        }

        // Process successful payment
        try {
            $paypal = new \App\Services\PayPalService();
            $order = $paypal->getOrderDetails($booking->payment_reference);

            // Check if order is approved
            if ($order['status'] === 'APPROVED') {
                // Capture payment
                $capture = $paypal->capturePayment($booking->payment_reference);

                if ($capture['status'] === 'COMPLETED') {
                    // Update booking
                    $booking->update([
                        'status' => \App\Models\Booking::STATUS_CONFIRMED,
                        'payment_status' => \App\Models\Booking::PAYMENT_COMPLETED,
                        'confirmed_at' => now()
                    ]);

                    // Generate tickets
                    foreach ($booking->bookingItems as $item) {
                        $item->generateTickets($booking->user_id);
                    }

                    // Clean up session
                    session()->forget('paypal_booking_id');

                    return redirect()->route('ga-booking.success', [
                        'slug' => $slug,
                        'bookingNumber' => $booking->booking_number
                    ]);
                }
            }

            // If we get here, payment wasn't completed
            $booking->update([
                'status' => \App\Models\Booking::STATUS_CANCELLED,
                'payment_status' => \App\Models\Booking::PAYMENT_FAILED
            ]);

            return redirect()->route('ga-booking.failed', $slug)
                ->with('error', 'Payment was not completed. Please try again.');

        } catch (\Exception $e) {
            \Log::error('PayPal return error: ' . $e->getMessage());

            $booking->update([
                'status' => \App\Models\Booking::STATUS_CANCELLED,
                'payment_status' => \App\Models\Booking::PAYMENT_FAILED
            ]);

            return redirect()->route('ga-booking.failed', $slug)
                ->with('error', 'Payment processing failed. Please try again.');
        }
    }

    /**
     * Handle PayPal webhook notifications
     */
    public function paypalWebhook(Request $request)
    {
        // Verify webhook signature (simplified for now)
        $paypal = new \App\Services\PayPalService();
        $verified = $paypal->verifyWebhookSignature($request->getContent(), $request->headers->all());

        if (!$verified) {
            \Log::warning('PayPal webhook signature verification failed');
            return response('Webhook verification failed', 400);
        }

        // Process webhook data
        $payload = json_decode($request->getContent(), true);
        $eventType = $payload['event_type'] ?? null;
        $resource = $payload['resource'] ?? null;

        if (!$eventType || !$resource) {
            \Log::warning('Invalid PayPal webhook payload');
            return response('Invalid payload', 400);
        }

        // Handle different event types
        switch ($eventType) {
            case 'CHECKOUT.ORDER.APPROVED':
                // Order approved, capture payment
                $orderId = $resource['id'] ?? null;
                if ($orderId) {
                    try {
                        // Find booking by payment reference
                        $booking = \App\Models\Booking::where('payment_reference', $orderId)->first();
                        if ($booking) {
                            $capture = $paypal->capturePayment($orderId);
                            if ($capture['status'] === 'COMPLETED') {
                                $booking->update([
                                    'status' => \App\Models\Booking::STATUS_CONFIRMED,
                                    'payment_status' => \App\Models\Booking::PAYMENT_COMPLETED,
                                    'confirmed_at' => now()
                                ]);

                                // Generate tickets
                                foreach ($booking->bookingItems as $item) {
                                    $item->generateTickets($booking->user_id);
                                }
                            }
                        }
                    } catch (\Exception $e) {
                        \Log::error('PayPal webhook capture error: ' . $e->getMessage());
                    }
                }
                break;

            case 'PAYMENT.CAPTURE.COMPLETED':
                // Payment completed
                $captureId = $resource['id'] ?? null;
                if ($captureId) {
                    // You might want to update your records here
                    \Log::info('PayPal payment captured: ' . $captureId);
                }
                break;

            case 'PAYMENT.CAPTURE.DENIED':
                // Payment denied
                $captureId = $resource['id'] ?? null;
                if ($captureId) {
                    // Update booking status to failed
                    $booking = \App\Models\Booking::where('payment_reference', $captureId)->first();
                    if ($booking) {
                        $booking->update([
                            'status' => \App\Models\Booking::STATUS_CANCELLED,
                            'payment_status' => \App\Models\Booking::PAYMENT_FAILED
                        ]);
                    }
                    \Log::info('PayPal payment denied: ' . $captureId);
                }
                break;
        }

        return response('Webhook processed', 200);
    }

    /**
 * Validate credit card number using Luhn algorithm
 */
private function validateLuhn($number)
{
    $number = strrev($number);
    $sum = 0;

    for ($i = 0; $i < strlen($number); $i++) {
        $digit = intval($number[$i]);

        if ($i % 2 == 1) {
            $digit *= 2;
            if ($digit > 9) {
                $digit = ($digit % 10) + 1;
            }
        }

        $sum += $digit;
    }

    return ($sum % 10) == 0;
}

/**
 * Detect and validate card type
 */
private function detectCardType($number)
{
    $cleanNumber = preg_replace('/\s+/', '', $number);

    $cardTypes = [
        'visa' => '/^4[0-9]{15}$/',
        'mastercard' => '/^5[1-5][0-9]{14}$/',
        'amex' => '/^3[47][0-9]{13}$/',
        'discover' => '/^6(?:011|5[0-9]{2})[0-9]{12}$/',
    ];

    foreach ($cardTypes as $type => $pattern) {
        if (preg_match($pattern, $cleanNumber)) {
            return $type;
        }
    }

    return null;
}

/**
 * Clean and format card data for processing
 */
private function cleanCardData($request)
{
    if ($request->payment_method === 'card') {
        // Clean card number (remove spaces)
        $cleanCardNumber = preg_replace('/\s+/', '', $request->card_number);

        return [
            'card_number' => $cleanCardNumber,
            'card_expiry' => $request->card_expiry,
            'card_cvv' => $request->card_cvv,
            'card_holder_name' => trim($request->card_holder_name),
            'card_type' => $this->detectCardType($cleanCardNumber),
        ];
    }

    return null;
}

}
