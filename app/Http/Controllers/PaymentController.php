<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function checkout($bookingId)
    {
        $booking = Booking::with(['show', 'show.venue'])
                         ->where('user_id', Auth::id())
                         ->where('status', 'pending')
                         ->findOrFail($bookingId);

        // Here you would typically integrate with your payment gateway
        // Example with Stripe:
        /*
        \Stripe\Stripe::setApiKey(config('services.stripe.secret'));

        $session = \Stripe\Checkout\Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'usd',
                    'product_data' => [
                        'name' => $booking->show->title,
                        'description' => 'Tickets: ' . $booking->number_of_tickets,
                    ],
                    'unit_amount' => $booking->show->price * 100, // Stripe expects amount in cents
                ],
                'quantity' => $booking->number_of_tickets,
            ]],
            'mode' => 'payment',
            'success_url' => route('payment.success', ['booking_id' => $booking->id]),
            'cancel_url' => route('payment.cancel', ['booking_id' => $booking->id]),
        ]);

        return view('payments.checkout', [
            'booking' => $booking,
            'session_id' => $session->id,
            'stripe_key' => config('services.stripe.key'),
        ]);
        */

        // For now, we'll just return a simple view
        return view('payments.checkout', compact('booking'));
    }

    public function success(Request $request, $bookingId)
    {
        $booking = Booking::where('user_id', Auth::id())
                         ->where('status', 'pending')
                         ->findOrFail($bookingId);

        // Update booking status
        $booking->update([
            'status' => 'confirmed',
            'payment_method' => 'Credit Card', // This would come from the payment gateway
            'payment_id' => 'PAYMENT-' . time(), // This would come from the payment gateway
        ]);

        // Redirect to confirmation page
        return redirect()->route('bookings.confirmation', $booking->id)
                       ->with('success', 'Payment successful! Your booking is confirmed.');
    }

    public function cancel($bookingId)
    {
        $booking = Booking::where('user_id', Auth::id())
                         ->where('status', 'pending')
                         ->findOrFail($bookingId);

        return redirect()->route('bookings.checkout', $booking->id)
                       ->with('error', 'Payment was cancelled. Please try again.');
    }
}
