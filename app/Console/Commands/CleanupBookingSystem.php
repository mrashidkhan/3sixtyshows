<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\BookingService;
use App\Models\Booking;
use App\Models\SeatReservation;
use App\Models\TicketHold;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class CleanupBookingSystem extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'booking:cleanup
                            {--expired-bookings : Clean up expired bookings}
                            {--expired-reservations : Clean up expired seat reservations}
                            {--expired-holds : Clean up expired ticket holds}
                            {--clear-cache : Clear booking-related cache}
                            {--all : Run all cleanup operations}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up expired bookings, reservations, and optimize performance';

    protected $bookingService;

    public function __construct(BookingService $bookingService)
    {
        parent::__construct();
        $this->bookingService = $bookingService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (!config('booking.cleanup_expired_bookings.enabled', true)) {
            $this->info('Booking cleanup is disabled in configuration.');
            return;
        }

        $this->info('Starting booking system cleanup...');

        $operations = [];

        if ($this->option('all')) {
            $operations = ['expired-bookings', 'expired-reservations', 'expired-holds', 'clear-cache'];
        } else {
            if ($this->option('expired-bookings')) $operations[] = 'expired-bookings';
            if ($this->option('expired-reservations')) $operations[] = 'expired-reservations';
            if ($this->option('expired-holds')) $operations[] = 'expired-holds';
            if ($this->option('clear-cache')) $operations[] = 'clear-cache';
        }

        // If no specific operations specified, run default cleanup
        if (empty($operations)) {
            $operations = ['expired-bookings', 'expired-reservations'];
        }

        foreach ($operations as $operation) {
            $this->runCleanupOperation($operation);
        }

        $this->info('Booking system cleanup completed successfully!');
    }

    /**
     * Run specific cleanup operation
     */
    private function runCleanupOperation(string $operation)
    {
        switch ($operation) {
            case 'expired-bookings':
                $this->cleanupExpiredBookings();
                break;
            case 'expired-reservations':
                $this->cleanupExpiredReservations();
                break;
            case 'expired-holds':
                $this->cleanupExpiredHolds();
                break;
            case 'clear-cache':
                $this->clearBookingCache();
                break;
        }
    }

    /**
     * Clean up expired bookings
     */
    private function cleanupExpiredBookings()
    {
        $this->info('Cleaning up expired bookings...');

        $batchSize = config('booking.cleanup_expired_bookings.batch_size', 50);
        $cleaned = 0;

        Booking::where('status', Booking::STATUS_PENDING)
            ->where('expires_at', '<', now())
            ->chunk($batchSize, function ($bookings) use (&$cleaned) {
                foreach ($bookings as $booking) {
                    try {
                        $this->bookingService->expireBooking($booking);
                        $cleaned++;
                    } catch (\Exception $e) {
                        $this->error("Failed to expire booking {$booking->id}: " . $e->getMessage());
                        Log::error('Booking cleanup failed', [
                            'booking_id' => $booking->id,
                            'error' => $e->getMessage()
                        ]);
                    }
                }
            });

        $this->info("Cleaned up {$cleaned} expired bookings.");
    }

    /**
     * Clean up expired seat reservations
     */
    private function cleanupExpiredReservations()
    {
        $this->info('Cleaning up expired seat reservations...');

        $cleaned = SeatReservation::where('status', SeatReservation::STATUS_TEMPORARY)
            ->where('reserved_until', '<', now())
            ->delete();

        $this->info("Cleaned up {$cleaned} expired seat reservations.");
    }

    /**
     * Clean up expired ticket holds
     */
    private function cleanupExpiredHolds()
    {
        $this->info('Cleaning up expired ticket holds...');

        $cleaned = TicketHold::where('expires_at', '<', now())
            ->delete();

        $this->info("Cleaned up {$cleaned} expired ticket holds.");
    }

    /**
     * Clear booking-related cache
     */
    private function clearBookingCache()
    {
        $this->info('Clearing booking-related cache...');

        $cacheKeys = [
            'show_seats_*',
            'booking_metrics_*',
            'seat_updates_*',
        ];

        foreach ($cacheKeys as $pattern) {
            // This is a simplified approach - in production you might want to use Redis SCAN
            Cache::flush(); // For simplicity, but you can implement more selective clearing
        }

        $this->info('Booking cache cleared.');
    }
}
