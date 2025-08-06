<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // Booking System Cleanup - Run every 5 minutes
        $schedule->command('booking:cleanup --expired-bookings --expired-reservations')
                 ->everyFiveMinutes()
                 ->runInBackground()
                 ->when(function () {
                     return config('booking.cleanup_expired_bookings.enabled', true);
                 });

        // Clear booking cache every hour during off-peak hours
        $schedule->command('booking:cleanup --clear-cache')
                 ->hourly()
                 ->between('2:00', '6:00')
                 ->runInBackground();

        // Daily comprehensive cleanup at 3 AM
        $schedule->command('booking:cleanup --all')
                 ->dailyAt('03:00')
                 ->runInBackground();

        // Laravel's default queue work (if using database queue)
        $schedule->command('queue:work --stop-when-empty')
                 ->everyMinute()
                 ->withoutOverlapping()
                 ->runInBackground();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
