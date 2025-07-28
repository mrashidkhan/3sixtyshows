<?php

namespace App\Http\Controllers;

use App\Models\Show;
use App\Models\Booking;
use App\Models\BookingItem;
use App\Models\SeatReservation;
use App\Models\Seat;
use App\Services\BookingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class OptimizedBookingController extends Controller
{
    protected $bookingService;

    public function __construct(BookingService $bookingService)
    {
        $this->bookingService = $bookingService;
    }

    /**
     * Show seat selection page (Optimized)
     */
    public function selectSeats(Show $show)
    {
        // Check if show is bookable
        if ($show->status === 'past' || !$show->is_active) {
            return redirect()->route('show.details', $show->slug)
                ->with('error', 'This show is no longer available for booking.');
        }

        // Cache seat data for 5 minutes to reduce database hits
        $cacheKey = "show_seats_{$show->id}";
        $seatData = Cache::remember($cacheKey, 300, function () use ($show) {
            return [
                'seats' => $this->getOptimizedSeats($show),
                'ticket_types' => $show->ticketTypes()
                    ->where('is_active', true)
                    ->select('id', 'name', 'price', 'description')
                    ->get()
            ];
        });

        return view('booking.select-seats', array_merge(compact('show'), $seatData));
    }

    /**
     * Get optimized seat data with minimal queries
     */
    private function getOptimizedSeats(Show $show)
    {
        // Single query to get all necessary seat data
        return DB::table('seats')
            ->leftJoin('seat_categories', 'seats.seat_category_id', '=', 'seat_categories.id')
            ->leftJoin('seat_reservations', function($join) use ($show) {
                $join->on('seats.id', '=', 'seat_reservations.seat_id')
                     ->where('seat_reservations.show_id', '=', $show->id)
                     ->whereIn('seat_reservations.status', ['reserved', 'sold', 'blocked']);
            })
            ->where('seats.venue_id', $show->venue_id)
            ->where('seats.is_active', true)
            ->select(
                'seats.id',
                'seats.section',
                'seats.row',
                'seats.seat_number',
                'seats.coordinates_x',
                'seats.coordinates_y',
                'seats.is_accessible',
                'seat_categories.name as category_name',
                'seat_categories.color_code',
                'seat_reservations.status as reservation_status'
            )
            ->get()
            ->map(function($seat) {
                return [
                    'id' => $seat->id,
                    'identifier' => $seat->section . '-' . $seat->row . '-' . $seat->seat_number,
                    'section' => $seat->section,
                    'row' => $seat->row,
                    'seat_number' => $seat->seat_number,
                    'coordinates' => [
                        'x' => (float) $seat->coordinates_x,
                        'y' => (float) $seat->coordinates_y
                    ],
                    'category' => [
                        'name' => $seat->category_name,
                        'color_code' => $seat->color_code
                    ],
                    'status' => $seat->reservation_status ?: 'available',
                    'is_accessible' => (bool) $seat->is_accessible,
                ];
            });
    }

    /**
     * Reserve selected seats temporarily (Optimized)
     */
    public function reserveSeats(Request $request, Show $show)
    {
        $request->validate([
            'selected_seats' => 'required|array|min:1|max:10', // Limit to 10 seats max
            'selected_seats.*.seat_id' => 'required|exists:seats,id',
            'selected_seats.*.ticket_type_id' => 'required|exists:ticket_types,id',
        ]);

        // Use service for complex booking logic
        try {
            $booking = $this->bookingService->createDraftBooking($show, $request->selected_seats, Auth::id());

            // Clear seat cache
            Cache::forget("show_seats_{$show->id}");

            return redirect()->route('booking.checkout', $show)
                ->with('booking_id', $booking->id);

        } catch (\Exception $e) {
            Log::error('Booking creation failed', [
                'user_id' => Auth::id(),
                'show_id' => $show->id,
                'error' => $e->getMessage()
            ]);

            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Show checkout page (Optimized)
     */
    public function checkout(Request $request, Show $show)
    {
        $bookingId = $request->session()->get('booking_id') ?? $request->get('booking_id');

        // Optimized query with eager loading
        $booking = Booking::with([
            'bookingItems:id,booking_id,seat_id,ticket_type_id,unit_price,total_price,seat_identifier',
            'bookingItems.seat:id,section,row,seat_number',
            'bookingItems.ticketType:id,name,price'
        ])
        ->where('customer_id', Auth::id())
        ->where('status', Booking::STATUS_DRAFT)
        ->findOrFail($bookingId);

        // Check if booking is expired
        if ($booking->isExpired()) {
            // Clean up expired booking
            $this->bookingService->cleanupExpiredBooking($booking);

            return redirect()->route('booking.select-seats', $show)
                ->with('error', 'Your seat selection has expired. Please select seats again.');
        }

        return view('booking.checkout', compact('show', 'booking'));
    }

    /**
     * Show user's bookings (Optimized with pagination)
     */
    public function myBookings(Request $request)
    {
        $perPage = $request->get('per_page', 10);
        $status = $request->get('status');

        $query = Booking::with([
            'show:id,title,start_date,featured_image',
            'bookingItems:id,booking_id,seat_identifier,total_price'
        ])
        ->where('customer_id', Auth::id())
        ->select('id', 'show_id', 'booking_number', 'total_amount', 'status', 'payment_status', 'created_at');

        if ($status) {
            $query->where('status', $status);
        }

        $bookings = $query->orderBy('created_at', 'desc')
                         ->paginate($perPage);

        return view('bookings.my', compact('bookings'));
    }

    /**
     * API endpoint for seat availability (AJAX optimized)
     */
    public function getSeatsAvailability(Show $show, Request $request)
    {
        $seatIds = $request->input('seat_ids', []);

        if (empty($seatIds)) {
            return response()->json(['error' => 'No seat IDs provided'], 400);
        }

        // Optimized query for availability check
        $unavailableSeats = DB::table('seat_reservations')
            ->where('show_id', $show->id)
            ->whereIn('seat_id', $seatIds)
            ->whereIn('status', ['reserved', 'sold', 'blocked'])
            ->pluck('seat_id')
            ->toArray();

        return response()->json([
            'available_seats' => array_diff($seatIds, $unavailableSeats),
            'unavailable_seats' => $unavailableSeats,
            'timestamp' => now()->toISOString()
        ]);
    }

    /**
     * API endpoint for real-time seat status updates
     */
    public function getSeatStatusUpdates(Show $show, Request $request)
    {
        $lastUpdate = $request->input('last_update');
        $cacheKey = "seat_updates_{$show->id}";

        // Get recent seat status changes
        $query = DB::table('seat_reservations')
            ->where('show_id', $show->id)
            ->select('seat_id', 'status', 'updated_at');

        if ($lastUpdate) {
            $query->where('updated_at', '>', Carbon::parse($lastUpdate));
        }

        $updates = $query->get();

        return response()->json([
            'updates' => $updates,
            'timestamp' => now()->toISOString()
        ]);
    }

    /**
     * Bulk operations for admin (Optimized)
     */
    public function bulkUpdateSeats(Request $request, Show $show)
    {
        $request->validate([
            'seat_ids' => 'required|array',
            'action' => 'required|in:block,unblock,release',
        ]);

        DB::beginTransaction();
        try {
            switch ($request->action) {
                case 'block':
                    $this->bookingService->bulkBlockSeats($show->id, $request->seat_ids);
                    break;
                case 'unblock':
                    $this->bookingService->bulkUnblockSeats($show->id, $request->seat_ids);
                    break;
                case 'release':
                    $this->bookingService->bulkReleaseSeats($show->id, $request->seat_ids);
                    break;
            }

            DB::commit();
            Cache::forget("show_seats_{$show->id}");

            return response()->json(['success' => true, 'message' => 'Seats updated successfully']);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
