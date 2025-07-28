<?php
// app/Http/Controllers/BookingController.php

namespace App\Http\Controllers;

use App\Models\Show;
use App\Models\Booking;
use App\Models\BookingItem;
use App\Models\SeatReservation;
use App\Models\Seat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BookingController extends Controller
{
    /**
     * Show seat selection page
     */
    public function selectSeats(Show $show)
    {
        // Check if show is bookable
        if ($show->status === 'past' || !$show->is_active) {
            return redirect()->route('show.details', $show->slug)
                ->with('error', 'This show is no longer available for booking.');
        }

        // Get available seats with their categories and prices
        $availableSeats = $show->getAvailableSeatsAttribute();
        $ticketTypes = $show->ticketTypes()->where('is_active', true)->get();

        return view('booking.select-seats', compact('show', 'availableSeats', 'ticketTypes'));
    }

    /**
     * Reserve selected seats temporarily
     */
    public function reserveSeats(Request $request, Show $show)
    {
        $request->validate([
            'selected_seats' => 'required|array|min:1',
            'selected_seats.*.seat_id' => 'required|exists:seats,id',
            'selected_seats.*.ticket_type_id' => 'required|exists:ticket_types,id',
        ]);

        DB::beginTransaction();
        try {
            // Create booking in draft status
            $booking = Booking::create([
                'user_id' => Auth::id(),
                'show_id' => $show->id,
                'status' => Booking::STATUS_DRAFT,
                'payment_status' => Booking::PAYMENT_PENDING,
                'expires_at' => now()->addMinutes(15), // 15 minutes to complete
                'booking_data' => $request->selected_seats,
            ]);

            $totalAmount = 0;

            // Create seat reservations and booking items
            foreach ($request->selected_seats as $seatData) {
                $seat = Seat::find($seatData['seat_id']);
                $ticketType = $show->ticketTypes()->find($seatData['ticket_type_id']);

                // Check if seat is still available
                if (!$seat->isAvailableForShow($show->id)) {
                    throw new \Exception("Seat {$seat->identifier} is no longer available.");
                }

                // Create temporary reservation
                SeatReservation::create([
                    'show_id' => $show->id,
                    'seat_id' => $seat->id,
                    'user_id' => Auth::id(),
                    'booking_id' => $booking->id,
                    'status' => SeatReservation::STATUS_TEMPORARY,
                    'reserved_until' => now()->addMinutes(15),
                    'price_paid' => $ticketType->price,
                ]);

                // Create booking item
                BookingItem::create([
                    'booking_id' => $booking->id,
                    'ticket_type_id' => $ticketType->id,
                    'seat_id' => $seat->id,
                    'quantity' => 1,
                    'unit_price' => $ticketType->price,
                    'total_price' => $ticketType->price,
                    'seat_identifier' => $seat->identifier,
                ]);

                $totalAmount += $ticketType->price;
            }

            // Update booking total
            $booking->update(['total_amount' => $totalAmount]);

            DB::commit();

            return redirect()->route('booking.checkout', $show)
                ->with('booking_id', $booking->id);

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Show checkout page
     */
    public function checkout(Request $request, Show $show)
    {
        $bookingId = $request->session()->get('booking_id') ?? $request->get('booking_id');

        $booking = Booking::with(['bookingItems.seat', 'bookingItems.ticketType'])
            ->where('user_id', Auth::id())
            ->where('status', Booking::STATUS_DRAFT)
            ->findOrFail($bookingId);

        // Check if booking is expired
        if ($booking->isExpired()) {
            return redirect()->route('booking.select-seats', $show)
                ->with('error', 'Your seat selection has expired. Please select seats again.');
        }

        return view('booking.checkout', compact('show', 'booking'));
    }

    /**
     * Confirm booking and process payment
     */
    public function confirmBooking(Request $request, Show $show)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'required|string|max:20',
            'payment_method' => 'required|string',
        ]);

        $booking = Booking::where('user_id', Auth::id())
            ->where('status', Booking::STATUS_DRAFT)
            ->findOrFail($request->booking_id);

        DB::beginTransaction();
        try {
            // Update booking with customer info
            $booking->update([
                'status' => Booking::STATUS_PENDING,
                'payment_status' => Booking::PAYMENT_PROCESSING,
                'booking_data' => array_merge($booking->booking_data ?? [], [
                    'customer_info' => $request->only(['first_name', 'last_name', 'email', 'phone']),
                    'payment_method' => $request->payment_method,
                ]),
            ]);

            // Process payment (integrate with your payment gateway)
            $paymentResult = $this->processPayment($booking, $request);

            if ($paymentResult['success']) {
                // Confirm booking
                $booking->update([
                    'status' => Booking::STATUS_CONFIRMED,
                    'payment_status' => Booking::PAYMENT_COMPLETED,
                    'payment_reference' => $paymentResult['reference'],
                    'confirmed_at' => now(),
                ]);

                // Update reservations to confirmed
                $booking->seatReservations()->update([
                    'status' => SeatReservation::STATUS_SOLD
                ]);

                // Generate tickets
                foreach ($booking->bookingItems as $item) {
                    $item->generateTickets();
                }

                DB::commit();

                return redirect()->route('payment.success', $booking);
            } else {
                throw new \Exception($paymentResult['message']);
            }

        } catch (\Exception $e) {
            DB::rollback();

            $booking->update([
                'payment_status' => Booking::PAYMENT_FAILED
            ]);

            return redirect()->route('payment.failed', $booking)
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Show user's bookings
     */
    public function myBookings()
    {
        $bookings = Booking::with(['show', 'bookingItems'])
            ->where('customer_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('bookings.my', compact('bookings'));
    }

    /**
     * Show specific booking details
     */
    public function show(Booking $booking)
    {
        // Ensure user can only view their own bookings
        if ($booking->user_id !== Auth::id()) {
            abort(403);
        }

        $booking->load(['show', 'bookingItems.seat', 'bookingItems.ticketType', 'tickets']);

        return view('bookings.show', compact('booking'));
    }

    /**
     * Cancel a booking
     */
    public function cancel(Booking $booking)
    {
        if ($booking->user_id !== Auth::id()) {
            abort(403);
        }

        if (!in_array($booking->status, [Booking::STATUS_PENDING, Booking::STATUS_CONFIRMED])) {
            return back()->with('error', 'This booking cannot be cancelled.');
        }

        DB::beginTransaction();
        try {
            // Update booking status
            $booking->update([
                'status' => Booking::STATUS_CANCELLED
            ]);

            // Release seat reservations
            $booking->seatReservations()->update([
                'status' => SeatReservation::STATUS_CANCELLED
            ]);

            // Cancel tickets
            $booking->tickets()->update([
                'status' => 'cancelled'
            ]);

            // Process refund if payment was completed
            if ($booking->payment_status === Booking::PAYMENT_COMPLETED) {
                $this->processRefund($booking);
            }

            DB::commit();

            return back()->with('success', 'Booking cancelled successfully.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Failed to cancel booking. Please contact support.');
        }
    }

    /**
     * Process payment (integrate with your payment gateway)
     */
    private function processPayment(Booking $booking, Request $request)
    {
        // This is where you'd integrate with Stripe, PayPal, etc.
        // For now, returning success for demo purposes

        return [
            'success' => true,
            'reference' => 'PAY_' . time(),
            'message' => 'Payment processed successfully'
        ];
    }

    /**
     * Process refund
     */
    private function processRefund(Booking $booking)
    {
        // Integrate with your payment gateway's refund API
        // Update booking payment status to refunded

        $booking->update([
            'payment_status' => Booking::PAYMENT_REFUNDED
        ]);
    }
}

// app/Http/Controllers/SeatMapController.php

namespace App\Http\Controllers;

use App\Models\Show;
use App\Models\Seat;
use App\Models\SeatReservation;
use Illuminate\Http\Request;

class SeatMapController extends Controller
{
    /**
     * Get seats for a show (AJAX)
     */
    public function getSeats(Show $show)
    {
        $seats = $show->venue->seats()->with(['category', 'reservations' => function($query) use ($show) {
            $query->where('show_id', $show->id)
                  ->whereIn('status', ['reserved', 'sold', 'blocked']);
        }])->get();

        $seatData = $seats->map(function($seat) use ($show) {
            return [
                'id' => $seat->id,
                'identifier' => $seat->identifier,
                'section' => $seat->section,
                'row' => $seat->row,
                'seat_number' => $seat->seat_number,
                'coordinates' => [
                    'x' => $seat->coordinates_x,
                    'y' => $seat->coordinates_y
                ],
                'category' => $seat->category,
                'status' => $seat->getStatusForShow($show->id),
                'is_accessible' => $seat->is_accessible,
            ];
        });

        return response()->json([
            'seats' => $seatData,
            'ticket_types' => $show->ticketTypes
        ]);
    }

    /**
     * Check seat availability (AJAX)
     */
    public function checkAvailability(Show $show, Request $request)
    {
        $seatIds = $request->input('seat_ids', []);

        $unavailableSeats = SeatReservation::where('show_id', $show->id)
            ->whereIn('seat_id', $seatIds)
            ->whereIn('status', ['reserved', 'sold', 'blocked'])
            ->pluck('seat_id')
            ->toArray();

        return response()->json([
            'available_seats' => array_diff($seatIds, $unavailableSeats),
            'unavailable_seats' => $unavailableSeats
        ]);
    }
}
