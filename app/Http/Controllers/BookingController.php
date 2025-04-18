<?php

namespace App\Http\Controllers;

use App\Models\Show;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $bookings = Booking::with(['show', 'show.venue'])
                          ->where('user_id', Auth::id())
                          ->orderBy('booking_date', 'desc')
                          ->paginate(10);

        return view('bookings.index', compact('bookings'));
    }

    public function show($id)
    {
        $booking = Booking::with(['show', 'show.venue'])
                         ->where('user_id', Auth::id())
                         ->findOrFail($id);

        return view('bookings.show', compact('booking'));
    }

    public function create($showSlug)
    {
        $show = Show::with('venue')
                   ->where('slug', $showSlug)
                   ->where('is_active', true)
                   ->where('status', '!=', 'past')
                   ->firstOrFail();

        // Check if show is sold out
        if ($show->sold_out) {
            return redirect()->route('shows.show', $show->slug)
                           ->with('error', 'Sorry, this show is sold out.');
        }

        return view('bookings.create', compact('show'));
    }

    public function store(Request $request, $showSlug)
    {
        $show = Show::where('slug', $showSlug)
                   ->where('is_active', true)
                   ->where('status', '!=', 'past')
                   ->firstOrFail();

        // Validate request
        $validated = $request->validate([
            'number_of_tickets' => 'required|integer|min:1',
            'attendee_details' => 'required|array',
            'attendee_details.*.name' => 'required|string|max:255',
            'attendee_details.*.email' => 'required|email|max:255',
            'seat_info' => 'nullable|string|max:255',
        ]);

        // Check if enough tickets are available
        if ($show->available_tickets !== null &&
            $validated['number_of_tickets'] > ($show->available_tickets - $show->sold_tickets)) {
            return redirect()->back()
                           ->with('error', 'Sorry, not enough tickets available.')
                           ->withInput();
        }

        // Calculate total amount
        $totalAmount = $show->price * $validated['number_of_tickets'];

        // Create booking
        $booking = Booking::create([
            'user_id' => Auth::id(),
            'show_id' => $show->id,
            'number_of_tickets' => $validated['number_of_tickets'],
            'total_amount' => $totalAmount,
            'attendee_details' => $validated['attendee_details'],
            'seat_info' => $request->seat_info,
            'status' => 'pending',
            'booking_date' => now(),
        ]);

        // Redirect to checkout
        return redirect()->route('bookings.checkout', $booking->id);
    }

    public function checkout($id)
    {
        $booking = Booking::with(['show', 'show.venue'])
                         ->where('user_id', Auth::id())
                         ->where('status', 'pending')
                         ->findOrFail($id);

        return view('bookings.checkout', compact('booking'));
    }

    public function processPayment(Request $request, $id)
    {
        $booking = Booking::where('user_id', Auth::id())
                         ->where('status', 'pending')
                         ->findOrFail($id);

        // Here you would implement payment gateway integration
        // For now, we'll just simulate a successful payment

        // Update booking
        $booking->update([
            'status' => 'confirmed',
            'payment_method' => 'Credit Card', // This would come from the payment gateway
            'payment_id' => 'PAYMENT-' . time(), // This would come from the payment gateway
        ]);

        // Redirect to confirmation
        return redirect()->route('bookings.confirmation', $booking->id);
    }

    public function confirmation($id)
    {
        $booking = Booking::with(['show', 'show.venue'])
                         ->where('user_id', Auth::id())
                         ->findOrFail($id);

        return view('bookings.confirmation', compact('booking'));
    }
}
