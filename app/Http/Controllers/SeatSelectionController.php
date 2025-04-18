<?php

namespace App\Http\Controllers;

use App\Models\Show;
use App\Models\SeatReservation;
use App\Models\Seat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;

class SeatSelectionController extends Controller
{
    // Show seat map to customers
    public function showSeatMap(Show $show)
    {
        // Get all seats for the venue
        $seats = Seat::where('venue_id', $show->venue_id)
            ->where('is_active', true)
            ->with('category')
            ->get();

        // Get all reserved seats for this show
        $reservedSeats = SeatReservation::where('show_id', $show->id)
            ->whereIn('status', ['temporary', 'reserved', 'booked'])
            ->where(function ($query) {
                $query->whereNull('reserved_until')
                    ->orWhere('reserved_until', '>', Carbon::now());
            })
            ->pluck('seat_id')
            ->toArray();

        $categories = $show->venue->seats()->with('category')
            ->get()
            ->pluck('category')
            ->unique()
            ->filter();

        return view('frontend.seat-selection', compact('show', 'seats', 'reservedSeats', 'categories'));
    }

    // Temporarily reserve seats during checkout
    public function temporaryReserve(Request $request)
    {
        $request->validate([
            'show_id' => 'required|exists:shows,id',
            'seat_ids' => 'required|array',
            'seat_ids.*' => 'exists:seats,id',
        ]);

        $show = Show::findOrFail($request->show_id);
        $reservedSeats = [];

        // Clear any existing temporary reservations for this session
        $sessionId = Session::getId();
        SeatReservation::where('reserved_by', $sessionId)
            ->where('status', 'temporary')
            ->delete();

        // Create new temporary reservations
        foreach ($request->seat_ids as $seatId) {
            // Check if seat is available
            $existingReservation = SeatReservation::where('show_id', $show->id)
                ->where('seat_id', $seatId)
                ->first();

            if (!$existingReservation) {
                $reservation = SeatReservation::create([
                    'show_id' => $show->id,
                    'seat_id' => $seatId,
                    'status' => 'temporary',
                    'reserved_by' => $sessionId,
                    'reserved_until' => Carbon::now()->addMinutes(15), // 15 minute hold
                ]);

                $seat = Seat::with('category')->find($seatId);
                $reservedSeats[] = [
                    'id' => $seatId,
                    'identifier' => $seat->fullSeatIdentifier,
                    'category' => $seat->category->name,
                    'price' => $seat->category->price,
                ];
            }
        }

        return response()->json([
            'success' => true,
            'reserved_seats' => $reservedSeats,
            'expires_at' => Carbon::now()->addMinutes(15)->timestamp
        ]);
    }

    // Convert temporary reservations to actual bookings
    public function confirmReservation(Request $request)
    {
        // This would be called after payment processing
        // Logic to convert temporary reservations to booked status
        // And associate them with tickets and a booking
    }
}
