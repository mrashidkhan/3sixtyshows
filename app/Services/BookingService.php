<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\BookingItem;
use App\Models\Show;
use App\Models\Seat;
use App\Models\SeatReservation;
use App\Models\User;
use App\Models\Payment;
use App\Models\TicketType;
use App\Services\SeatManagementService;
use App\Services\PaymentService;
use App\Services\NotificationService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Exception;

class BookingService
{
    private $seatService;
    private $paymentService;
    private $notificationService;

    // Booking statuses
    const STATUS_PENDING = 'pending';
    const STATUS_CONFIRMED = 'confirmed';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_REFUNDED = 'refunded';
    const STATUS_EXPIRED = 'expired';

    // Payment statuses
    const PAYMENT_PENDING = 'pending';
    const PAYMENT_COMPLETED = 'completed';
    const PAYMENT_FAILED = 'failed';
    const PAYMENT_REFUNDED = 'refunded';

    public function __construct(
        SeatManagementService $seatService,
        PaymentService $paymentService,
        NotificationService $notificationService
    ) {
        $this->seatService = $seatService;
        $this->paymentService = $paymentService;
        $this->notificationService = $notificationService;
    }

    /**
     * Create a new booking with seat reservations
     */
    public function createBooking($userId, $showId, array $seatIds, array $additionalData = [])
    {
        // Input validation and sanitization
        $this->validateBookingInput($userId, $showId, $seatIds);

        DB::beginTransaction();

        try {
            // Get show details
            $show = Show::with(['venue', 'event'])->findOrFail($showId);

            // Verify seats are available and get pricing
            $seatDetails = $this->validateAndGetSeatDetails($showId, $seatIds);

            // Calculate total amount
            $totalAmount = $this->calculateTotalAmount($seatDetails);
            $fees = $this->calculateFees($totalAmount);
            $finalAmount = $totalAmount + $fees['total'];

            // Create booking record
            $booking = $this->createBookingRecord($userId, $show, $finalAmount, $fees, $additionalData);

            // Reserve seats temporarily
            $reservations = $this->seatService->reserveSeatsTemporarily(
                $showId,
                $seatIds,
                $userId,
                config('booking.reservation_timeout', 15) // 15 minutes default
            );

            // Create booking items
            $this->createBookingItems($booking->id, $seatDetails);

            // Log booking creation
            $this->logBookingActivity($booking->id, 'booking_created', [
                'user_id' => $userId,
                'show_id' => $showId,
                'seat_count' => count($seatIds),
                'total_amount' => $finalAmount
            ]);

            DB::commit();

            // Send confirmation email/SMS
            $this->notificationService->sendBookingConfirmation($booking);

            return [
                'success' => true,
                'booking' => $this->formatBookingResponse($booking),
                'payment_required' => true,
                'expires_at' => $booking->expires_at,
                'total_amount' => $finalAmount
            ];

        } catch (Exception $e) {
            DB::rollBack();

            Log::error('Booking creation failed', [
                'user_id' => $userId,
                'show_id' => $showId,
                'seat_ids' => $seatIds,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            throw new Exception('Booking creation failed: ' . $e->getMessage());
        }
    }

    /**
     * Process payment and confirm booking
     */
    public function processPayment($bookingId, array $paymentData)
    {
        $booking = Booking::with(['items.seat', 'user'])->findOrFail($bookingId);

        // Security check - ensure booking belongs to authenticated user
        if ($booking->user_id !== auth()->id() && !auth()->user()->hasRole('admin')) {
            throw new Exception('Unauthorized access to booking');
        }

        // Check if booking is still valid
        if ($booking->status !== self::STATUS_PENDING) {
            throw new Exception('Booking is no longer pending');
        }

        if ($booking->expires_at < now()) {
            $this->expireBooking($booking);
            throw new Exception('Booking has expired');
        }

        DB::beginTransaction();

        try {
            // Process payment through payment service
            $paymentResult = $this->paymentService->processPayment(
                $booking->total_amount,
                $paymentData,
                [
                    'booking_id' => $booking->id,
                    'user_id' => $booking->user_id,
                    'description' => "Booking #{$booking->booking_number}"
                ]
            );

            if (!$paymentResult['success']) {
                throw new Exception('Payment processing failed: ' . $paymentResult['message']);
            }

            // Create payment record
            $payment = Payment::create([
                'booking_id' => $booking->id,
                'user_id' => $booking->user_id,
                'amount' => $booking->total_amount,
                'payment_method' => $paymentData['method'],
                'transaction_id' => $paymentResult['transaction_id'],
                'status' => self::PAYMENT_COMPLETED,
                'payment_data' => json_encode($paymentResult['details'])
            ]);

            // Update booking status
            $booking->update([
                'status' => self::STATUS_CONFIRMED,
                'payment_status' => self::PAYMENT_COMPLETED,
                'confirmed_at' => now()
            ]);

            // Confirm seat reservations
            $seatIds = $booking->items->pluck('seat_id')->toArray();
            $this->seatService->confirmReservations($booking->show_id, $seatIds, $booking->id);

            // Generate tickets
            $tickets = $this->generateTickets($booking);

            // Log successful payment
            $this->logBookingActivity($booking->id, 'payment_completed', [
                'payment_id' => $payment->id,
                'transaction_id' => $paymentResult['transaction_id'],
                'amount' => $booking->total_amount
            ]);

            DB::commit();

            // Send tickets via email/SMS
            $this->notificationService->sendTickets($booking, $tickets);

            return [
                'success' => true,
                'booking' => $this->formatBookingResponse($booking->fresh()),
                'payment' => $payment,
                'tickets' => $tickets
            ];

        } catch (Exception $e) {
            DB::rollBack();

            Log::error('Payment processing failed', [
                'booking_id' => $bookingId,
                'error' => $e->getMessage()
            ]);

            throw $e;
        }
    }

    /**
     * Cancel booking and refund if applicable
     */
    public function cancelBooking($bookingId, $reason = null, $refundAmount = null)
    {
        $booking = Booking::with(['items', 'payments'])->findOrFail($bookingId);

        // Security check
        if ($booking->user_id !== auth()->id() && !auth()->user()->hasRole('admin')) {
            throw new Exception('Unauthorized access to booking');
        }

        if ($booking->status === self::STATUS_CANCELLED) {
            throw new Exception('Booking is already cancelled');
        }

        DB::beginTransaction();

        try {
            // Calculate refund amount based on cancellation policy
            $refundData = $this->calculateRefund($booking, $refundAmount);

            // Update booking status
            $booking->update([
                'status' => self::STATUS_CANCELLED,
                'cancelled_at' => now(),
                'cancellation_reason' => $reason,
                'refund_amount' => $refundData['amount']
            ]);

            // Release seat reservations
            $seatIds = $booking->items->pluck('seat_id')->toArray();
            SeatReservation::where('booking_id', $booking->id)
                ->update(['status' => SeatReservation::STATUS_CANCELLED]);

            // Process refund if applicable
            if ($refundData['amount'] > 0) {
                $this->processRefund($booking, $refundData['amount']);
            }

            // Clear seat cache
            Cache::forget("show_seats_{$booking->show_id}");

            // Log cancellation
            $this->logBookingActivity($booking->id, 'booking_cancelled', [
                'reason' => $reason,
                'refund_amount' => $refundData['amount']
            ]);

            DB::commit();

            // Send cancellation notification
            $this->notificationService->sendCancellationNotification($booking);

            return [
                'success' => true,
                'booking' => $this->formatBookingResponse($booking->fresh()),
                'refund_amount' => $refundData['amount']
            ];

        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Get booking details with security checks
     */
    public function getBooking($bookingId, $userId = null)
    {
        $booking = Booking::with([
            'items.seat.category',
            'show.venue',
            'show.event',
            'payments',
            'user'
        ])->findOrFail($bookingId);

        // Security check
        $userId = $userId ?? auth()->id();
        if ($booking->user_id !== $userId && !auth()->user()->hasRole('admin')) {
            throw new Exception('Unauthorized access to booking');
        }

        return $this->formatBookingResponse($booking);
    }

    /**
     * Get user's booking history
     */
    public function getUserBookings($userId, array $filters = [])
    {
        $query = Booking::with([
            'items.seat.category',
            'show.venue',
            'show.event'
        ])->where('user_id', $userId);

        // Apply filters
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['from_date'])) {
            $query->where('created_at', '>=', $filters['from_date']);
        }

        if (!empty($filters['to_date'])) {
            $query->where('created_at', '<=', $filters['to_date']);
        }

        $bookings = $query->orderBy('created_at', 'desc')
                         ->paginate($filters['per_page'] ?? 15);

        return [
            'bookings' => $bookings->items(),
            'pagination' => [
                'current_page' => $bookings->currentPage(),
                'total_pages' => $bookings->lastPage(),
                'total_count' => $bookings->total()
            ]
        ];
    }

    /**
     * Expire booking and release seats
     */
    public function expireBooking($booking)
    {
        if (is_numeric($booking)) {
            $booking = Booking::findOrFail($booking);
        }

        if ($booking->status !== self::STATUS_PENDING) {
            return false;
        }

        DB::beginTransaction();

        try {
            $booking->update([
                'status' => self::STATUS_EXPIRED,
                'expired_at' => now()
            ]);

            // Release seat reservations
            SeatReservation::where('booking_id', $booking->id)
                ->where('status', SeatReservation::STATUS_TEMPORARY)
                ->update(['status' => SeatReservation::STATUS_CANCELLED]);

            // Clear cache
            Cache::forget("show_seats_{$booking->show_id}");

            $this->logBookingActivity($booking->id, 'booking_expired');

            DB::commit();

            return true;

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to expire booking', [
                'booking_id' => $booking->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Clean up expired bookings
     */
    public function cleanupExpiredBookings()
    {
        $expiredBookings = Booking::where('status', self::STATUS_PENDING)
            ->where('expires_at', '<', now())
            ->get();

        $count = 0;
        foreach ($expiredBookings as $booking) {
            if ($this->expireBooking($booking)) {
                $count++;
            }
        }

        return $count;
    }

    // Private helper methods

    private function validateBookingInput($userId, $showId, $seatIds)
    {
        if (!User::find($userId)) {
            throw new Exception('Invalid user ID');
        }

        if (!Show::find($showId)) {
            throw new Exception('Invalid show ID');
        }

        if (empty($seatIds) || !is_array($seatIds)) {
            throw new Exception('Seat IDs are required');
        }

        // Validate seat limit per booking
        $maxSeats = config('booking.max_seats_per_booking', 8);
        if (count($seatIds) > $maxSeats) {
            throw new Exception("Maximum {$maxSeats} seats allowed per booking");
        }
    }

    private function validateAndGetSeatDetails($showId, $seatIds)
    {
        $seats = Seat::with('category')->whereIn('id', $seatIds)->get();

        if ($seats->count() !== count($seatIds)) {
            throw new Exception('Some seats are invalid');
        }

        $seatDetails = [];
        foreach ($seats as $seat) {
            if (!$seat->isAvailableForShow($showId)) {
                throw new Exception("Seat {$seat->identifier} is not available");
            }

            $seatDetails[] = [
                'seat' => $seat,
                'price' => $seat->category->getPriceForShow($showId)
            ];
        }

        return $seatDetails;
    }

    private function calculateTotalAmount($seatDetails)
    {
        return collect($seatDetails)->sum('price');
    }

    private function calculateFees($amount)
    {
        $serviceFee = $amount * (config('booking.service_fee_percentage', 2.5) / 100);
        $processingFee = config('booking.processing_fee', 1.50);

        return [
            'service_fee' => $serviceFee,
            'processing_fee' => $processingFee,
            'total' => $serviceFee + $processingFee
        ];
    }

    private function createBookingRecord($userId, $show, $totalAmount, $fees, $additionalData)
    {
        return Booking::create([
            'booking_number' => $this->generateBookingNumber(),
            'user_id' => $userId,
            'show_id' => $show->id,
            'venue_id' => $show->venue_id,
            'event_id' => $show->event_id,
            'status' => self::STATUS_PENDING,
            'payment_status' => self::PAYMENT_PENDING,
            'total_amount' => $totalAmount,
            'service_fee' => $fees['service_fee'],
            'processing_fee' => $fees['processing_fee'],
            'expires_at' => now()->addMinutes(config('booking.payment_timeout', 15)),
            'booking_data' => json_encode($additionalData)
        ]);
    }

    private function createBookingItems($bookingId, $seatDetails)
    {
        foreach ($seatDetails as $detail) {
            BookingItem::create([
                'booking_id' => $bookingId,
                'seat_id' => $detail['seat']->id,
                'price' => $detail['price'],
                'item_type' => 'seat'
            ]);
        }
    }

    private function generateBookingNumber()
    {
        do {
            $number = 'BK' . date('Y') . strtoupper(Str::random(8));
        } while (Booking::where('booking_number', $number)->exists());

        return $number;
    }

    private function calculateRefund($booking, $customAmount = null)
    {
        if ($customAmount !== null) {
            return ['amount' => $customAmount];
        }

        // Implement cancellation policy logic
        $showDate = $booking->show->show_date;
        $hoursUntilShow = now()->diffInHours($showDate);

        if ($hoursUntilShow >= 48) {
            return ['amount' => $booking->total_amount * 0.9]; // 90% refund
        } elseif ($hoursUntilShow >= 24) {
            return ['amount' => $booking->total_amount * 0.5]; // 50% refund
        } else {
            return ['amount' => 0]; // No refund
        }
    }

    private function processRefund($booking, $amount)
    {
        // Implementation depends on payment gateway
        return $this->paymentService->processRefund($booking->id, $amount);
    }

    private function generateTickets($booking)
    {
        $tickets = [];
        foreach ($booking->items as $item) {
            $tickets[] = [
                'ticket_number' => $this->generateTicketNumber(),
                'booking_id' => $booking->id,
                'seat_id' => $item->seat_id,
                'qr_code' => $this->generateQRCode($booking, $item),
                'validity_date' => $booking->show->show_date
            ];
        }
        return $tickets;
    }

    private function generateTicketNumber()
    {
        return 'TK' . date('Y') . strtoupper(Str::random(10));
    }

    private function generateQRCode($booking, $item)
    {
        // Generate QR code data for ticket validation
        return base64_encode(json_encode([
            'booking_id' => $booking->id,
            'seat_id' => $item->seat_id,
            'show_id' => $booking->show_id,
            'timestamp' => now()->timestamp
        ]));
    }

    private function formatBookingResponse($booking)
    {
        return [
            'id' => $booking->id,
            'booking_number' => $booking->booking_number,
            'status' => $booking->status,
            'payment_status' => $booking->payment_status,
            'total_amount' => $booking->total_amount,
            'service_fee' => $booking->service_fee,
            'processing_fee' => $booking->processing_fee,
            'created_at' => $booking->created_at,
            'expires_at' => $booking->expires_at,
            'show' => [
                'id' => $booking->show->id,
                'title' => $booking->show->title,
                'show_date' => $booking->show->show_date,
                'venue' => $booking->show->venue->name
            ],
            'seats' => $booking->items->map(function ($item) {
                return [
                    'id' => $item->seat->id,
                    'identifier' => $item->seat->identifier,
                    'category' => $item->seat->category->name,
                    'price' => $item->price
                ];
            })
        ];
    }

    private function logBookingActivity($bookingId, $action, $data = [])
    {
        Log::info("Booking {$action}", array_merge([
            'booking_id' => $bookingId,
            'user_id' => auth()->id(),
            'timestamp' => now()
        ], $data));
    }

    /**
     * Create draft booking for optimized controller
     */
    public function createDraftBooking(Show $show, array $selectedSeats, int $userId)
    {
        DB::beginTransaction();
        try {
            // Validate seats are still available
            $this->validateSeatAvailability($show->id, array_column($selectedSeats, 'seat_id'));

            // Create booking in draft status
            $booking = Booking::create([
                'customer_id' => $userId,
                'show_id' => $show->id,
                'status' => Booking::STATUS_DRAFT,
                'payment_status' => Booking::PAYMENT_PENDING,
                'expires_at' => now()->addMinutes(15),
                'booking_data' => $selectedSeats,
            ]);

            $totalAmount = 0;

            // Create seat reservations and booking items
            foreach ($selectedSeats as $seatData) {
                $seat = Seat::find($seatData['seat_id']);
                $ticketType = TicketType::find($seatData['ticket_type_id']);

                if (!$seat->isAvailableForShow($show->id)) {
                    throw new Exception("Seat {$seat->identifier} is no longer available.");
                }

                // Create temporary reservation
                SeatReservation::create([
                    'show_id' => $show->id,
                    'seat_id' => $seat->id,
                    'user_id' => $userId,
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
            return $booking;

        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * Validate seat availability
     */
    private function validateSeatAvailability($showId, array $seatIds)
    {
        $unavailable = SeatReservation::where('show_id', $showId)
            ->whereIn('seat_id', $seatIds)
            ->whereIn('status', ['reserved', 'sold', 'blocked'])
            ->pluck('seat_id')
            ->toArray();

        if (!empty($unavailable)) {
            $seatNames = Seat::whereIn('id', $unavailable)->pluck('identifier')->join(', ');
            throw new Exception("Seats {$seatNames} are no longer available.");
        }
    }

    /**
     * Clean up expired booking
     */
    public function cleanupExpiredBooking(Booking $booking)
    {
        DB::beginTransaction();
        try {
            $booking->update(['status' => Booking::STATUS_EXPIRED]);

            // Release seat reservations
            $booking->seatReservations()->update([
                'status' => SeatReservation::STATUS_CANCELLED
            ]);

            // Clear cache
            Cache::forget("show_seats_{$booking->show_id}");

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * Bulk block seats
     */
    public function bulkBlockSeats($showId, array $seatIds)
    {
        foreach ($seatIds as $seatId) {
            SeatReservation::updateOrCreate([
                'show_id' => $showId,
                'seat_id' => $seatId,
            ], [
                'status' => SeatReservation::STATUS_BLOCKED,
                'user_id' => auth()->id(),
                'reserved_until' => now()->addYears(1),
            ]);
        }
    }

    /**
     * Bulk unblock seats
     */
    public function bulkUnblockSeats($showId, array $seatIds)
    {
        SeatReservation::where('show_id', $showId)
            ->whereIn('seat_id', $seatIds)
            ->where('status', SeatReservation::STATUS_BLOCKED)
            ->delete();
    }

    /**
     * Bulk release seats
     */
    public function bulkReleaseSeats($showId, array $seatIds)
    {
        SeatReservation::where('show_id', $showId)
            ->whereIn('seat_id', $seatIds)
            ->whereIn('status', [SeatReservation::STATUS_TEMPORARY, SeatReservation::STATUS_RESERVED])
            ->delete();
    }
}
