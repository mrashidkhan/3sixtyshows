<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SeatReservation;
use App\Models\Show;
use App\Models\Seat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SeatReservationController extends Controller
{
    // Admin methods for managing seat reservations

    public function index(Request $request)
    {
        $query = SeatReservation::query();

        if ($request->filled('show')) {
            $query->where('show_id', $request->show);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $reservations = $query->with(['show', 'seat', 'booking', 'reservedBy'])->paginate(20);
        $shows = Show::upcoming()->pluck('title', 'id');

        return view('admin.reservations.index', compact('reservations', 'shows'));
    }

    public function create()
    {
        $shows = Show::upcoming()->pluck('title', 'id');
        return view('admin.reservations.create', compact('shows'));
    }

    public function getAvailableSeats(Show $show)
    {
        $availableSeats = $show->availableSeats;
        return response()->json($availableSeats);
    }

    public function store(Request $request)
    {
        $request->validate([
            'show_id' => 'required|exists:shows,id',
            'seat_ids' => 'required|array',
            'seat_ids.*' => 'exists:seats,id',
            'status' => 'required|in:reserved,booked',
            'reserved_until' => 'nullable|date|after:now',
            'notes' => 'nullable|string',
        ]);

        $seatsReserved = 0;

        foreach ($request->seat_ids as $seatId) {
            // Check if seat is already reserved for this show
            $existingReservation = SeatReservation::where('show_id', $request->show_id)
                ->where('seat_id', $seatId)
                ->first();

            if (!$existingReservation) {
                SeatReservation::create([
                    'show_id' => $request->show_id,
                    'seat_id' => $seatId,
                    'status' => $request->status,
                    'reserved_by' => Auth::id(),
                    'reserved_until' => $request->reserved_until,
                    'notes' => $request->notes,
                ]);

                $seatsReserved++;
            }
        }

        return redirect()->route('admin.reservations.index')
            ->with('success', "$seatsReserved seats reserved successfully.");
    }

    // Cancel reservation
    public function destroy(SeatReservation $reservation)
    {
        // Only allow cancellation if not associated with a ticket/booking
        if (!$reservation->ticket_id && !$reservation->booking_id) {
            $reservation->delete();
            return redirect()->route('admin.reservations.index')
                ->with('success', 'Reservation cancelled successfully.');
        }

        return redirect()->route('admin.reservations.index')
            ->with('error', 'Cannot cancel a reservation that has an associated ticket or booking.');
    }
}
