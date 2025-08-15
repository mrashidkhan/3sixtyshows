<?php

namespace App\Http\Controllers;

use App\Models\Show;
use App\Models\TicketHold;
use Illuminate\Http\Request;

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
        $bookingData = session('booking_data');
        $customerData = session('customer_data');

        // Validate session data
        if (!$bookingData || !$customerData) {
            return redirect()->route('ga-booking.tickets', $slug)
                ->with('error', 'Booking session expired. Please start again.');
        }

        // Check expiry
        if (now() > \Carbon\Carbon::parse($bookingData['expires_at'])) {
            TicketHold::where('session_id', $bookingData['session_id'])->delete();
            session()->forget(['booking_data', 'customer_data']);

            return redirect()->route('ga-booking.tickets', $slug)
                ->with('error', 'Booking session expired. Please select tickets again.');
        }

        // Validate payment information
        $request->validate([
            'payment_method' => 'required|in:card,paypal',
            // Add card validation for card payments
            'card_number' => 'required_if:payment_method,card|string|min:13|max:19',
            'card_holder_name' => 'required_if:payment_method,card|string|min:2',
            'card_expiry' => 'required_if:payment_method,card|string|size:5',
            'card_cvv' => 'required_if:payment_method,card|string|min:3|max:4',
        ]);

        try {
            \DB::beginTransaction();

            // 1. Create or find USER
            $user = \App\Models\User::firstOrCreate(
                ['email' => $customerData['email']],
                [
                    'name' => $customerData['name'],
                    'phone' => $customerData['phone'],
                    'newsletter_subscribed' => $customerData['newsletter'],
                    'email_verified_at' => now(),
                    'password' => bcrypt('temp_' . \Illuminate\Support\Str::random(8)),
                    'role' => 'customer',
                    'is_active' => true
                ]
            );

            // 2. Calculate final amounts
            $subtotal = $bookingData['subtotal'];
            $serviceFee = max($subtotal * 0.03, 2.00);
            $processingFee = $bookingData['total_tickets'] * 1.50;
            $grandTotal = $subtotal + $serviceFee + $processingFee;

            // 3. Create booking with USER_ID
            $booking = \App\Models\Booking::create([
                'user_id' => $user->id,
                'show_id' => $show->id,
                'total_amount' => $subtotal,
                'service_fee' => $serviceFee,
                'processing_fee' => $processingFee,
                'grand_total' => $grandTotal,
                'number_of_tickets' => $bookingData['total_tickets'],
                'ticket_breakdown' => $bookingData['ticket_breakdown'],
                'status' => \App\Models\Booking::STATUS_PENDING,
                'payment_status' => \App\Models\Booking::PAYMENT_PENDING,
                'payment_method' => $request->payment_method,
                'booking_date' => now(),
                'expires_at' => now()->addMinutes(30)
            ]);

            // 4. Create booking items
            foreach ($bookingData['ticket_breakdown'] as $ticketData) {
                \App\Models\BookingItem::create([
                    'booking_id' => $booking->id,
                    'ticket_type_id' => $ticketData['ticket_type_id'],
                    'quantity' => $ticketData['quantity'],
                    'unit_price' => $ticketData['unit_price'],
                    'total_price' => $ticketData['total_price']
                ]);
            }

            // 5. Process payment
            $paymentResult = $this->processPaymentMethod($request, $grandTotal, $booking);

            // Handle PayPal redirect
            if (isset($paymentResult['success']) && $paymentResult['success'] === 'paypal_redirect') {
                // Store booking ID in session for webhook verification
                session(['paypal_booking_id' => $booking->id]);

                // Clean up session data as we're redirecting
                TicketHold::where('session_id', $bookingData['session_id'])->delete();

                \DB::commit();

                // Redirect to PayPal for payment
                // Use approval URL from payment result
                $approvalUrl = $paymentResult['approval_url'] ?? null;

                if ($approvalUrl) {
                    return redirect($approvalUrl);
                } else {
                    \DB::rollback();
                    return redirect()->route('ga-booking.failed', $slug)
                        ->with('error', 'Could not redirect to PayPal. Please try again.');
                }
            }

            if ($paymentResult['success'] === true) {
                // Payment successful
                $booking->update([
                    'status' => \App\Models\Booking::STATUS_CONFIRMED,
                    'payment_status' => \App\Models\Booking::PAYMENT_COMPLETED,
                    'payment_reference' => $paymentResult['transaction_id'],
                    'confirmed_at' => now()
                ]);

                // Generate tickets
                foreach ($booking->bookingItems as $item) {
                    $item->generateTickets($user->id);
                }

                // Clean up
                TicketHold::where('session_id', $bookingData['session_id'])->delete();
                session()->forget(['booking_data', 'customer_data']);

                \DB::commit();

                return redirect()->route('ga-booking.success', [
                    'slug' => $slug,
                    'bookingNumber' => $booking->booking_number
                ]);
            } else {
                // Payment failed
                $booking->update([
                    'status' => \App\Models\Booking::STATUS_CANCELLED,
                    'payment_status' => \App\Models\Booking::PAYMENT_FAILED
                ]);

                \DB::rollback();

                return redirect()->route('ga-booking.failed', $slug)
                    ->with('error', $paymentResult['message']);
            }

        } catch (\Exception $e) {
            \DB::rollback();

            return back()->withErrors([
                'error' => 'Payment processing failed: ' . $e->getMessage()
            ]);
        }
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
}
