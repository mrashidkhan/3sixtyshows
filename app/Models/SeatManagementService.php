<?php
namespace App\Services;

use App\Models\Show;
use App\Models\Seat;
use App\Models\SeatReservation;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class SeatManagementService
{
    public function getSeatsForShow($showId)
    {
        $cacheKey = "show_seats_{$showId}";

        return Cache::remember($cacheKey, 300, function () use ($showId) {
            $show = Show::findOrFail($showId);

            return Seat::where('venue_id', $show->venue_id)
                ->where('is_active', true)
                ->with(['category', 'reservations' => function ($q) use ($showId) {
                    $q->where('show_id', $showId)->active();
                }])
                ->get()
                ->map(function ($seat) use ($showId) {
                    return [
                        'id' => $seat->id,
                        'identifier' => $seat->identifier,
                        'section' => $seat->section,
                        'row' => $seat->row,
                        'number' => $seat->seat_number,
                        'x' => $seat->coordinates_x,
                        'y' => $seat->coordinates_y,
                        'category' => [
                            'id' => $seat->category->id,
                            'name' => $seat->category->name,
                            'color' => $seat->category->color_code,
                            'price' => $seat->category->getPriceForShow($showId),
                        ],
                        'status' => $seat->getStatusForShow($showId),
                        'is_accessible' => $seat->is_accessible,
                    ];
                });
        });
    }

    public function reserveSeatsTemporarily($showId, array $seatIds, $userId, $minutes = 10)
    {
        DB::beginTransaction();

        try {
            $reservedSeats = [];
            $expiresAt = Carbon::now()->addMinutes($minutes);

            foreach ($seatIds as $seatId) {
                $seat = Seat::findOrFail($seatId);

                // Check if seat is available
                if (!$seat->isAvailableForShow($showId)) {
                    throw new \Exception("Seat {$seat->identifier} is not available");
                }

                // Create temporary reservation
                $reservation = SeatReservation::create([
                    'show_id' => $showId,
                    'seat_id' => $seatId,
                    'user_id' => $userId,
                    'status' => SeatReservation::STATUS_TEMPORARY,
                    'reserved_until' => $expiresAt,
                    'price_paid' => $seat->category->getPriceForShow($showId),
                ]);

                $reservedSeats[] = [
                    'seat_id' => $seatId,
                    'identifier' => $seat->identifier,
                    'price' => $reservation->price_paid,
                    'expires_at' => $expiresAt,
                ];
            }

            DB::commit();

            // Clear cache
            Cache::forget("show_seats_{$showId}");

            return $reservedSeats;

        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    public function confirmReservations($showId, array $seatIds, $bookingId)
    {
        DB::beginTransaction();

        try {
            SeatReservation::where('show_id', $showId)
                ->whereIn('seat_id', $seatIds)
                ->where('status', SeatReservation::STATUS_TEMPORARY)
                ->update([
                    'status' => SeatReservation::STATUS_SOLD,
                    'booking_id' => $bookingId,
                    'reserved_until' => null,
                ]);

            DB::commit();

            // Clear cache
            Cache::forget("show_seats_{$showId}");

        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    public function releaseExpiredReservations()
    {
        $expired = SeatReservation::expired()->get();

        foreach ($expired as $reservation) {
            $reservation->update(['status' => SeatReservation::STATUS_CANCELLED]);
            Cache::forget("show_seats_{$reservation->show_id}");
        }

        return $expired->count();
    }

    public function getSeatMapData($venueId)
    {
        return Cache::remember("venue_map_{$venueId}", 3600, function () use ($venueId) {
            return Seat::where('venue_id', $venueId)
                ->where('is_active', true)
                ->with('category')
                ->get()
                ->groupBy('section')
                ->map(function ($seats, $section) {
                    return [
                        'section' => $section,
                        'seats' => $seats->map(function ($seat) {
                            return [
                                'id' => $seat->id,
                                'row' => $seat->row,
                                'number' => $seat->seat_number,
                                'x' => $seat->coordinates_x,
                                'y' => $seat->coordinates_y,
                                'category_color' => $seat->category->color_code,
                            ];
                        }),
                    ];
                });
        });
    }
}
