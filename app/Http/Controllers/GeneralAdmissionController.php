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
        // return redirect()->route('ga-booking.payment', $slug);
        return redirect()->route('ga-booking.process-payment', $slug);
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
    $bookingData = session('booking_data');
    $customerData = session('customer_data');

    if (!$bookingData || !$customerData) {
        return redirect()->route('ga-booking.tickets', $slug)
            ->with('error', 'Booking session expired. Please start again.');
    }

    // Validation based on payment method
    $this->validatePaymentData($request);

    try {
        DB::beginTransaction();

        // Create booking record
        $booking = Booking::create([
            'user_id' => auth()->id(),
            'show_id' => $show->id,
            'booking_number' => 'BK-' . strtoupper(Str::random(8)),
            'customer_name' => $customerData['name'],
            'customer_email' => $customerData['email'],
            'customer_phone' => $customerData['phone'],
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
            BookingItem::create([
                'booking_id' => $booking->id,
                'ticket_type_id' => $ticketData['ticket_type_id'],
                'quantity' => $ticketData['quantity'],
                'unit_price' => $ticketData['unit_price'],
                'total_price' => $ticketData['total_price']
            ]);
        }

        DB::commit();

        // Process payment based on method - both through PayPal gateway
        if ($request->payment_method === 'card') {
            return $this->processPayPalCreditCard($booking, $request);
        } elseif ($request->payment_method === 'paypal') {
            return $this->processPayPalAccount($booking);
        }

        throw new \Exception('Invalid payment method selected');

    } catch (\Exception $e) {
        DB::rollback();
        Log::error('Booking creation error', [
            'error' => $e->getMessage(),
            'line' => $e->getLine(),
            'payment_method' => $request->payment_method
        ]);

        return back()->with('error', 'Booking failed: ' . $e->getMessage());
    }
}
/**
 * Process PayPal payment
 */
// In GeneralAdmissionController.php, replace processPayPalPayment method:

private function processPayPalPayment($booking)
    {
        try {
            $paypal = new PayPalService();

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
                $booking->grand_total,
                "Ticket Purchase - {$booking->show->title}",
                $booking->booking_number,
                $returnUrl,
                $cancelUrl
            );

            if (!isset($order['id'])) {
                throw new \Exception('PayPal order creation failed - no order ID returned');
            }

            $booking->update([
                'payment_reference' => $order['id']
            ]);

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

            return redirect($approvalUrl);

        } catch (\Exception $e) {
            Log::error('PayPal payment error', [
                'booking_id' => $booking->id,
                'error' => $e->getMessage(),
            ]);

            $booking->update([
                'status' => 'cancelled',
                'payment_status' => 'failed'
            ]);

            return back()->with('error', 'PayPal payment failed: ' . $e->getMessage());
        }
    }

public function paypalSuccess(Request $request, $slug)
{
    $token = $request->query('token');
    $payerId = $request->query('PayerID');

    Log::info('PayPal success callback received', [
        'slug' => $slug,
        'token' => $token,
        'payer_id' => $payerId,
        'request_params' => $request->all()
    ]);

    // PayPal account payments will have both token and payerId
    // Credit card payments processed directly may not have payerId
    if (!$token) {
        Log::error('Missing PayPal token in success callback');
        return redirect()->route('ga-booking.failed', $slug)
            ->with('error', 'PayPal payment verification failed - missing transaction ID.');
    }

    try {
        // Find booking by PayPal order ID
        $booking = Booking::where('payment_reference', $token)->first();

        if (!$booking) {
            Log::error('Booking not found for PayPal token', [
                'token' => $token,
                'recent_bookings' => Booking::whereIn('payment_method', ['paypal', 'card'])
                    ->where('created_at', '>=', now()->subHours(2))
                    ->pluck('payment_reference', 'id')
                    ->toArray()
            ]);

            throw new \Exception("Booking not found for PayPal transaction: {$token}");
        }

        Log::info('Processing PayPal payment completion', [
            'booking_id' => $booking->id,
            'booking_number' => $booking->booking_number,
            'payment_method' => $booking->payment_method,
            'current_status' => $booking->payment_status
        ]);

        $paypal = new PayPalService();

        // Get current order status first
        $orderDetails = $paypal->getOrderDetails($token);

        Log::info('PayPal order status check', [
            'booking_id' => $booking->id,
            'order_status' => $orderDetails['status'] ?? 'unknown',
            'order_details' => $orderDetails
        ]);

        // Handle different order states
        if ($orderDetails['status'] === 'COMPLETED') {
            // Payment already captured (common with credit cards)
            $captureDetails = $orderDetails['purchase_units'][0]['payments']['captures'][0] ?? null;

            if ($captureDetails && $captureDetails['status'] === 'COMPLETED') {
                return $this->completeBookingPayment($booking, $captureDetails, $payerId);
            }
        } elseif ($orderDetails['status'] === 'APPROVED') {
            // Payment approved but needs capture (common with PayPal account)
            $captureResult = $paypal->capturePayment($token);

            if ($captureResult['status'] === 'COMPLETED') {
                $captureDetails = $captureResult['purchase_units'][0]['payments']['captures'][0] ?? null;
                return $this->completeBookingPayment($booking, $captureDetails, $payerId);
            } else {
                throw new \Exception('Payment capture failed with status: ' . $captureResult['status']);
            }
        }

        // If we get here, payment wasn't successful
        Log::error('PayPal payment not in successful state', [
            'booking_id' => $booking->id,
            'order_status' => $orderDetails['status'] ?? 'unknown'
        ]);

        throw new \Exception('Payment was not completed successfully');

    } catch (\Exception $e) {
        Log::error('PayPal success processing error', [
            'token' => $token,
            'payer_id' => $payerId,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);

        // Mark booking as failed if it exists
        if (isset($booking)) {
            $booking->update([
                'status' => 'cancelled',
                'payment_status' => 'failed',
                'paypal_transaction_data' => [
                    'error' => $e->getMessage(),
                    'failed_at' => now()->toISOString()
                ]
            ]);
        }

        return redirect()->route('ga-booking.failed', $slug)
            ->with('error', 'Payment processing failed: ' . $e->getMessage());
    }
}


    /**
 * Enhanced PayPal cancel handler
 */
public function paypalCancel(Request $request, $slug)
{
    $token = $request->query('token');

    Log::info('PayPal payment cancelled', [
        'slug' => $slug,
        'token' => $token
    ]);

    // Try to find and cancel the booking if possible
    if ($token) {
        $booking = Booking::where('payment_reference', $token)->first();
        if ($booking) {
            $booking->update([
                'status' => 'cancelled',
                'payment_status' => 'cancelled'
            ]);

            Log::info('Booking cancelled due to PayPal cancellation', [
                'booking_id' => $booking->id
            ]);
        }
    }

    return redirect()->route('ga-booking.failed', $slug)
        ->with('warning', 'Payment was cancelled. You can try again anytime.');
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
 * Detect and validate card type - FIXED VERSION
 */
private function detectCardType($number)
{
    $cleanNumber = preg_replace('/\s+/', '', $number);

    $cardTypes = [
        'visa' => '/^4[0-9]{12}(?:[0-9]{3})?$/',
        'mastercard' => '/^5[1-5][0-9]{14}$|^2(?:2(?:2[1-9]|[3-9][0-9])|[3-6][0-9][0-9]|7(?:[0-1][0-9]|20))[0-9]{12}$/',
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

/**
 * Validate payment data based on method - FIXED VERSION
 */
private function validatePaymentData($request)
{
    if ($request->payment_method === 'card') {
        $request->validate([
            'payment_method' => 'required|in:card,paypal',
            'card_number' => [
                'required',
                'string',
                'regex:/^[0-9\s]{13,19}$/',
                function ($attribute, $value, $fail) {
                    $cleanNumber = preg_replace('/\s+/', '', $value);
                    if (!$this->validateLuhn($cleanNumber)) {
                        $fail('The card number is invalid.');
                    }
                }
            ],
            'card_expiry' => [
                'required',
                'string',
                'size:4',
                'regex:/^(0[1-9]|1[0-2])[0-9]{2}$/'
            ],
            'card_cvv' => 'required|numeric|digits_between:3,4',
            'card_holder_name' => 'required|string|max:255|regex:/^[a-zA-Z\s]+$/',
            'billing_address' => 'required|string|max:255',
            'billing_city' => 'required|string|max:100',
            'billing_state' => 'required|string|max:100',
            'billing_zip' => 'required|string|max:20',
        ]);
    } else {
        $request->validate([
            'payment_method' => 'required|in:card,paypal',
        ]);
    }
}

/**
 * Process Credit Card Payment via PayPal Gateway
 */
private function processPayPalCreditCard($booking, $request)
{
    try {
        // Attempt direct card processing
        $paypal = new PayPalService();
        $cardData = $paypal->formatCardData(
            $request->card_number,
            $request->card_expiry,
            $request->card_cvv,
            $request->card_holder_name
        );

        $billingData = [
            'address' => $request->billing_address,
            'city' => $request->billing_city,
            'state' => $request->billing_state,
            'zip' => $request->billing_zip
        ];

        $order = $paypal->createCreditCardOrder(
            $booking->grand_total,
            "Tickets for {$booking->show->title}",
            $booking->booking_number,
            $cardData,
            $billingData
        );

        // If successful, continue with normal flow
        $booking->update([
            'payment_reference' => $order['id'],
            'payment_status' => 'processing',
            'card_last_four' => substr($cardData['card_number'], -4),
            'card_type' => $this->detectCardType($cardData['card_number'])
        ]);

        return $this->handlePayPalSuccess($booking, $order);

    } catch (\Exception $e) {
        Log::warning('Direct card processing failed, falling back to PayPal account method', [
            'booking_id' => $booking->id,
            'error' => $e->getMessage()
        ]);

        // Fallback to PayPal account payment (redirect)
        return $this->processPayPalAccount($booking);
    }
}

/**
 * Process PayPal Account Payment (redirect flow)
 */
private function processPayPalAccount($booking)
{
    try {
        $paypal = new PayPalService();

        $returnUrl = route('ga-booking.paypal-success', ['slug' => $booking->show->slug]);
        $cancelUrl = route('ga-booking.paypal-cancel', ['slug' => $booking->show->slug]);

        // Create PayPal order for account payment
        $order = $paypal->createPayPalOrder(
            $booking->grand_total,
            "Tickets for {$booking->show->title}",
            $booking->booking_number,
            $returnUrl,
            $cancelUrl
        );

        // Update booking with PayPal order ID
        $booking->update([
            'payment_reference' => $order['id'],
            'payment_status' => 'processing'
        ]);

        // Get approval URL for redirect
        $approvalUrl = collect($order['links'])
            ->firstWhere('rel', 'approve')['href'] ?? null;

        if (!$approvalUrl) {
            throw new \Exception('PayPal approval URL not found');
        }

        Log::info('Redirecting to PayPal account login', [
            'booking_id' => $booking->id,
            'paypal_order_id' => $order['id']
        ]);

        // Redirect to PayPal
        return redirect($approvalUrl);

    } catch (\Exception $e) {
        Log::error('PayPal Account Payment Error', [
            'booking_id' => $booking->id,
            'error' => $e->getMessage()
        ]);

        $booking->update([
            'status' => 'cancelled',
            'payment_status' => 'failed'
        ]);

        return back()->with('error', 'PayPal payment setup failed: ' . $e->getMessage());
    }
}

/**
 * Handle successful PayPal payment (both credit card and account)
 */
private function handlePayPalSuccess($booking, $paypalResponse)
{
    try {
        // Extract capture details
        $purchaseUnit = $paypalResponse['purchase_units'][0] ?? null;
        $captureDetails = $purchaseUnit['payments']['captures'][0] ?? null;

        if (!$captureDetails || $captureDetails['status'] !== 'COMPLETED') {
            throw new \Exception('Payment capture was not completed');
        }

        $captureId = $captureDetails['id'];
        $paypalFee = $captureDetails['seller_receivable_breakdown']['paypal_fee']['value'] ?? 0;

        // Update booking as completed
        $booking->update([
            'status' => 'confirmed',
            'payment_status' => 'paid',
            'transaction_id' => $captureId,
            'paid_at' => now(),
            'confirmed_at' => now(),
            'paypal_capture_id' => $captureId,
            'paypal_fee' => $paypalFee,
            'paypal_transaction_data' => $captureDetails
        ]);

        // Generate tickets
        $booking->generateTickets();

        // Clear session data
        session()->forget(['booking_data', 'customer_data']);

        Log::info('PayPal payment completed', [
            'booking_id' => $booking->id,
            'payment_method' => $booking->payment_method,
            'capture_id' => $captureId
        ]);

        return redirect()->route('ga-booking.success', [
            'slug' => $booking->show->slug,
            'bookingNumber' => $booking->booking_number
        ])->with('success', 'Payment completed successfully!');

    } catch (\Exception $e) {
        Log::error('PayPal success handling error', [
            'booking_id' => $booking->id,
            'error' => $e->getMessage()
        ]);

        $booking->update([
            'status' => 'cancelled',
            'payment_status' => 'failed'
        ]);

        throw $e;
    }
}

/**
 * Complete booking payment for both credit card and PayPal account methods
 */
private function completeBookingPayment($booking, $captureDetails, $payerId = null)
{
    if (!$captureDetails || !isset($captureDetails['id'])) {
        throw new \Exception('Invalid capture details received from PayPal');
    }

    $captureId = $captureDetails['id'];
    $paypalFee = $captureDetails['seller_receivable_breakdown']['paypal_fee']['value'] ?? 0;
    $netAmount = $captureDetails['seller_receivable_breakdown']['net_amount']['value'] ?? $booking->grand_total;

    // Update booking with complete transaction data
    $updateData = [
        'status' => 'confirmed',
        'payment_status' => 'paid',
        'transaction_id' => $captureId,
        'paid_at' => now(),
        'confirmed_at' => now(),
        'paypal_capture_id' => $captureId,
        'paypal_fee' => $paypalFee,
        'paypal_transaction_data' => $captureDetails,
        'paypal_processed_at' => now()
    ];

    // Add payer ID if available (PayPal account payments)
    if ($payerId) {
        $updateData['paypal_payer_id'] = $payerId;
    }

    $booking->update($updateData);

    // Generate tickets
    $booking->generateTickets();

    // Clear session data
    session()->forget(['booking_data', 'customer_data']);

    Log::info('PayPal payment completed successfully', [
        'booking_id' => $booking->id,
        'booking_number' => $booking->booking_number,
        'payment_method' => $booking->payment_method,
        'capture_id' => $captureId,
        'paypal_fee' => $paypalFee,
        'net_amount' => $netAmount,
        'has_payer_id' => !empty($payerId)
    ]);

    return redirect()->route('ga-booking.success', [
        'slug' => $booking->show->slug,
        'bookingNumber' => $booking->booking_number
    ])->with('success', 'Payment completed successfully! Your tickets have been confirmed.');
}

// Add these methods to your PayPalService class

/**
 * Create Order for PayPal Account Payment (redirect flow)
 */
public function createPayPalOrder($amount, $description, $invoiceId, $returnUrl, $cancelUrl)
{
    return $this->createOrder($amount, $description, $invoiceId, $returnUrl, $cancelUrl);
}

/**
 * Create Order for Credit Card Payment (direct processing)
 */
public function createCreditCardOrder($amount, $description, $invoiceId, $cardData, $billingData)
{
    try {
        $token = $this->getAccessToken();

        // PayPal's correct structure for credit card orders
        $orderData = [
            'intent' => 'CAPTURE',
            'purchase_units' => [
                [
                    'reference_id' => 'default',
                    'amount' => [
                        'currency_code' => 'USD',
                        'value' => number_format($amount, 2, '.', '')
                    ],
                    'description' => $description,
                    'custom_id' => $invoiceId,
                ]
            ],
            'payment_source' => [
                'card' => [
                    'number' => $cardData['card_number'],
                    'expiry' => $cardData['card_expiry_month'] . '/' . substr($cardData['card_expiry_year'], -2), // MM/YY format
                    'security_code' => $cardData['card_cvv'],
                    'name' => $cardData['card_holder_name'],
                    'billing_address' => [
                        'address_line_1' => $billingData['address'],
                        'admin_area_2' => $billingData['city'],
                        'admin_area_1' => $billingData['state'],
                        'postal_code' => $billingData['zip'],
                        'country_code' => 'US'
                    ],
                    'attributes' => [
                        'verification' => [
                            'method' => 'SCA_WHEN_REQUIRED'
                        ]
                    ]
                ]
            ]
        ];

        Log::info('PayPal Credit Card Order Request', [
            'amount' => $amount,
            'card_last_four' => substr($cardData['card_number'], -4),
            'request_structure' => $orderData
        ]);

        $response = $this->getHttpClient()
            ->withToken($token)
            ->withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'PayPal-Request-Id' => uniqid('card-payment-')
            ])
            ->post("{$this->apiUrl}/v2/checkout/orders", $orderData);

        if ($response->successful()) {
            $order = $response->json();
            Log::info('PayPal credit card order created successfully', [
                'order_id' => $order['id'],
                'status' => $order['status']
            ]);
            return $order;
        }

        $errorBody = $response->json();
        Log::error('PayPal credit card order failed', [
            'status' => $response->status(),
            'error_body' => $errorBody,
            'request_data' => $orderData
        ]);

        throw new \Exception('Credit card processing failed: ' . ($errorBody['message'] ?? $response->body()));

    } catch (\Exception $e) {
        Log::error('PayPal Credit Card Order Error', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        throw $e;
    }
}

public function formatCardData($cardNumber, $cardExpiry, $cardCvv, $cardHolderName)
{
    // Clean card number
    $cleanNumber = preg_replace('/\s+/', '', $cardNumber);

    // Parse expiry (handle both MM/YY and MMYY formats)
    $expiry = str_replace('/', '', $cardExpiry);
    if (strlen($expiry) === 4) {
        $month = substr($expiry, 0, 2);
        $year = '20' . substr($expiry, 2, 2);
    } else {
        throw new \Exception('Invalid card expiry format. Expected MMYY, got: ' . $cardExpiry);
    }

    // Validate month
    if ($month < 1 || $month > 12) {
        throw new \Exception('Invalid month in card expiry: ' . $month);
    }

    return [
        'card_number' => $cleanNumber,
        'card_expiry_month' => str_pad($month, 2, '0', STR_PAD_LEFT),
        'card_expiry_year' => $year,
        'card_cvv' => $cardCvv,
        'card_holder_name' => trim($cardHolderName)
    ];
}

}
