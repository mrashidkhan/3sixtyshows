<?php

namespace App\Services;

use App\Models\Show;
use App\Models\Seat;
use App\Models\GeneralAdmissionArea;
use App\Models\TicketHold;
use App\Models\Booking;
use App\Models\BookingItem;
use App\Models\SeatReservation;
use App\Models\ShowTicketQuota;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class TicketingService
{
    /**
     * Get available seating options for a show
     */
    public function getAvailableSeating(Show $show)
    {
        return [
            'assigned_seating' => $this->getAvailableAssignedSeats($show),
            'general_admission' => $this->getAvailableGeneralAdmission($show),
            'show_mode' => $this->determineShowMode($show)
        ];
    }

    /**
     * Get available assigned seats for a show
     */
    private function getAvailableAssignedSeats(Show $show)
    {
        $venue = $show->venue;

        // Get all seat categories with pricing
        $seatCategories = $venue->seats()
            ->with(['category'])
            ->whereHas('category', function($query) {
                $query->where('is_active', true);
            })
            ->where('is_active', true)
            ->get()
            ->groupBy('seat_category_id');

        $availableSeating = [];

        foreach ($seatCategories as $categoryId => $seats) {
            $category = $seats->first()->category;

            // Get available seats (not reserved/sold)
            $availableSeats = $seats->filter(function($seat) use ($show) {
                return $seat->isAvailableForShow($show->id);
            });

            // Get pricing for this show
            $price = $category->getPriceForShow($show->id);

            $availableSeating[] = [
                'category_id' => $categoryId,
                'category_name' => $category->name,
                'category_description' => $category->description,
                'price' => $price,
                'available_count' => $availableSeats->count(),
                'total_count' => $seats->count(),
                'color_code' => $category->color_code,
                'seats' => $availableSeats->map(function($seat) {
                    return [
                        'id' => $seat->id,
                        'identifier' => $seat->identifier,
                        'section' => $seat->section,
                        'row' => $seat->row,
                        'seat_number' => $seat->seat_number,
                        'coordinates_x' => $seat->coordinates_x,
                        'coordinates_y' => $seat->coordinates_y,
                        'is_accessible' => $seat->is_accessible,
                    ];
                })
            ];
        }

        return $availableSeating;
    }

    /**
     * Get available general admission areas
     */
    private function getAvailableGeneralAdmission(Show $show)
    {
        $venue = $show->venue;

        return $venue->generalAdmissionAreas()
            ->where('is_active', true)
            ->get()
            ->map(function($area) use ($show) {
                $availableCapacity = $area->getAvailableCapacityForShow($show->id);

                // Get pricing from ShowTicketQuota if exists
                $quota = ShowTicketQuota::where('show_id', $show->id)
                    ->where('area_id', $area->id)
                    ->where('area_type', 'general_admission')
                    ->first();

                $price = $quota ? $quota->effective_price : $area->default_price;

                return [
                    'id' => $area->id,
                    'name' => $area->name,
                    'description' => $area->description,
                    'capacity' => $area->capacity,
                    'available_capacity' => $availableCapacity,
                    'price' => $price,
                    'color_code' => $area->color_code,
                ];
            });
    }

    /**
     * Determine if show supports assigned seating, GA, or both
     */
    private function determineShowMode(Show $show)
    {
        $venue = $show->venue;

        $hasSeats = $venue->seats()->where('is_active', true)->exists();
        $hasGA = $venue->generalAdmissionAreas()->where('is_active', true)->exists();

        if ($hasSeats && $hasGA) {
            return 'hybrid'; // Both assigned and GA
        } elseif ($hasSeats) {
            return 'assigned_seating';
        } elseif ($hasGA) {
            return 'general_admission';
        }

        return 'undefined';
    }

    /**
     * Hold tickets temporarily during checkout process
     */
    public function holdTickets(Show $show, array $ticketRequests, $customerId = null, $sessionId = null)
    {
        DB::beginTransaction();

        try {
            $holds = [];
            $holdExpiration = Carbon::now()->addMinutes(15); // 15-minute hold

            foreach ($ticketRequests as $request) {
                if ($request['type'] === 'assigned_seat') {
                    $hold = $this->holdAssignedSeat($show, $request, $holdExpiration, $customerId, $sessionId);
                } elseif ($request['type'] === 'general_admission') {
                    $hold = $this->holdGeneralAdmissionTickets($show, $request, $holdExpiration, $customerId, $sessionId);
                }

                if ($hold) {
                    $holds[] = $hold;
                }
            }

            DB::commit();
            return $holds;

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Hold an assigned seat
     */
    private function holdAssignedSeat(Show $show, array $request, Carbon $expiration, $customerId, $sessionId)
    {
        $seat = Seat::find($request['seat_id']);

        if (!$seat || !$seat->isAvailableForShow($show->id)) {
            throw new \Exception("Seat {$seat->identifier} is not available");
        }

        // Create temporary seat reservation
        $reservation = SeatReservation::create([
            'show_id' => $show->id,
            'seat_id' => $seat->id,
            'user_id' => $customerId,
            'status' => SeatReservation::STATUS_TEMPORARY,
            'reserved_until' => $expiration,
        ]);

        // Create ticket hold record
        return TicketHold::create([
            'show_id' => $show->id,
            'ticket_type_id' => $request['ticket_type_id'] ?? null,
            'customer_id' => $customerId,
            'session_id' => $sessionId,
            'seat_id' => $seat->id,
            'quantity' => 1,
            'hold_type' => 'assigned_seat',
            'expires_at' => $expiration,
            'hold_data' => [
                'seat_identifier' => $seat->identifier,
                'price' => $seat->category->getPriceForShow($show->id),
                'reservation_id' => $reservation->id
            ]
        ]);
    }

    /**
     * Hold general admission tickets
     */
    private function holdGeneralAdmissionTickets(Show $show, array $request, Carbon $expiration, $customerId, $sessionId)
    {
        $area = GeneralAdmissionArea::find($request['area_id']);
        $quantity = $request['quantity'];

        if (!$area || $area->getAvailableCapacityForShow($show->id) < $quantity) {
            throw new \Exception("Insufficient capacity in {$area->name}");
        }

        return TicketHold::create([
            'show_id' => $show->id,
            'ticket_type_id' => $request['ticket_type_id'] ?? null,
            'customer_id' => $customerId,
            'session_id' => $sessionId,
            'general_admission_area_id' => $area->id,
            'quantity' => $quantity,
            'hold_type' => 'general_admission',
            'expires_at' => $expiration,
            'hold_data' => [
                'area_name' => $area->name,
                'price_per_ticket' => $area->default_price,
                'total_price' => $area->default_price * $quantity
            ]
        ]);
    }

    /**
     * Convert held tickets to confirmed booking
     */
    public function confirmBooking(array $holds, $customerId, array $bookingData)
    {
        DB::beginTransaction();

        try {
            // Create the booking
            $booking = Booking::create([
                'customer_id' => $customerId,
                'show_id' => $holds[0]['show_id'],
                'booking_number' => null, // Auto-generated in model
                'status' => Booking::STATUS_PENDING,
                'payment_status' => Booking::PAYMENT_PENDING,
                'total_amount' => $this->calculateTotalFromHolds($holds),
                'number_of_tickets' => array_sum(array_column($holds, 'quantity')),
                'booking_date' => now(),
                'expires_at' => now()->addMinutes(30), // 30 min to complete payment
            ]);

            // Create booking items and tickets for each hold
            foreach ($holds as $holdData) {
                $hold = TicketHold::find($holdData['id']);

                if ($hold->hold_type === 'assigned_seat') {
                    $this->createAssignedSeatBookingItem($booking, $hold);
                } elseif ($hold->hold_type === 'general_admission') {
                    $this->createGeneralAdmissionBookingItem($booking, $hold);
                }

                // Remove the hold
                $hold->delete();
            }

            DB::commit();
            return $booking;

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Create booking item for assigned seat
     */
    private function createAssignedSeatBookingItem(Booking $booking, TicketHold $hold)
    {
        $seat = $hold->seat;
        $price = $hold->hold_data['price'];

        // Create booking item
        $bookingItem = BookingItem::create([
            'booking_id' => $booking->id,
            'ticket_type_id' => $hold->ticket_type_id,
            'seat_id' => $seat->id,
            'quantity' => 1,
            'unit_price' => $price,
            'total_price' => $price,
            'seat_identifier' => $seat->identifier,
        ]);

        // Update seat reservation to confirmed
        $reservationId = $hold->hold_data['reservation_id'];
        SeatReservation::where('id', $reservationId)->update([
            'booking_id' => $booking->id,
            'status' => SeatReservation::STATUS_RESERVED,
            'price_paid' => $price,
        ]);

        return $bookingItem;
    }

    /**
     * Create booking item for general admission
     */
    private function createGeneralAdmissionBookingItem(Booking $booking, TicketHold $hold)
    {
        $area = $hold->generalAdmissionArea;
        $unitPrice = $hold->hold_data['price_per_ticket'];
        $totalPrice = $unitPrice * $hold->quantity;

        return BookingItem::create([
            'booking_id' => $booking->id,
            'ticket_type_id' => $hold->ticket_type_id,
            'general_admission_area_id' => $area->id,
            'quantity' => $hold->quantity,
            'unit_price' => $unitPrice,
            'total_price' => $totalPrice,
        ]);
    }

    /**
     * Calculate total amount from holds
     */
    private function calculateTotalFromHolds(array $holds)
    {
        $total = 0;

        foreach ($holds as $holdData) {
            $hold = TicketHold::find($holdData['id']);

            if ($hold->hold_type === 'assigned_seat') {
                $total += $hold->hold_data['price'];
            } elseif ($hold->hold_type === 'general_admission') {
                $total += $hold->hold_data['total_price'];
            }
        }

        return $total;
    }

    /**
     * Release expired holds
     */
    public function releaseExpiredHolds()
    {
        DB::beginTransaction();

        try {
            $expiredHolds = TicketHold::expired()->get();

            foreach ($expiredHolds as $hold) {
                if ($hold->hold_type === 'assigned_seat' && isset($hold->hold_data['reservation_id'])) {
                    // Remove temporary seat reservation
                    SeatReservation::where('id', $hold->hold_data['reservation_id'])
                        ->where('status', SeatReservation::STATUS_TEMPORARY)
                        ->delete();
                }

                $hold->delete();
            }

            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
