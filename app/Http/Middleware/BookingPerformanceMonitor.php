<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class BookingPerformanceMonitor
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (!config('booking.monitoring.track_performance_metrics', true)) {
            return $next($request);
        }

        $startTime = microtime(true);
        $startMemory = memory_get_usage();

        $response = $next($request);

        $endTime = microtime(true);
        $endMemory = memory_get_usage();

        $executionTime = ($endTime - $startTime) * 1000; // Convert to milliseconds
        $memoryUsage = $endMemory - $startMemory;

        // Log slow requests
        if (config('booking.monitoring.log_slow_queries', true)) {
            $threshold = config('booking.monitoring.slow_query_threshold', 1000);

            if ($executionTime > $threshold) {
                Log::warning('Slow booking request detected', [
                    'url' => $request->fullUrl(),
                    'method' => $request->method(),
                    'execution_time_ms' => round($executionTime, 2),
                    'memory_usage_bytes' => $memoryUsage,
                    'user_id' => auth()->id(),
                    'ip' => $request->ip(),
                ]);
            }
        }

        // Store performance metrics in cache for dashboard
        $this->storePerformanceMetrics($request, $executionTime, $memoryUsage);

        // Add performance headers for debugging
        if (config('app.debug')) {
            $response->headers->set('X-Execution-Time', round($executionTime, 2) . 'ms');
            $response->headers->set('X-Memory-Usage', round($memoryUsage / 1024, 2) . 'KB');
        }

        return $response;
    }

    /**
     * Store performance metrics for monitoring dashboard
     */
    private function storePerformanceMetrics(Request $request, float $executionTime, int $memoryUsage)
    {
        $route = $request->route()?->getName();

        if (!$route || !$this->isBookingRoute($route)) {
            return;
        }

        $cacheKey = "booking_metrics_{$route}_" . date('Y-m-d-H');

        $metrics = Cache::get($cacheKey, [
            'total_requests' => 0,
            'total_execution_time' => 0,
            'max_execution_time' => 0,
            'min_execution_time' => PHP_FLOAT_MAX,
            'total_memory_usage' => 0,
            'max_memory_usage' => 0,
        ]);

        $metrics['total_requests']++;
        $metrics['total_execution_time'] += $executionTime;
        $metrics['max_execution_time'] = max($metrics['max_execution_time'], $executionTime);
        $metrics['min_execution_time'] = min($metrics['min_execution_time'], $executionTime);
        $metrics['total_memory_usage'] += $memoryUsage;
        $metrics['max_memory_usage'] = max($metrics['max_memory_usage'], $memoryUsage);

        Cache::put($cacheKey, $metrics, now()->addHours(25)); // Keep for 25 hours
    }

    /**
     * Check if the route is booking-related
     */
    private function isBookingRoute(string $routeName): bool
    {
        $bookingRoutes = [
            'booking.select-seats',
            'booking.reserve-seats',
            'booking.checkout',
            'booking.confirm',
            'bookings.my',
            'api.seats.availability',
            'api.seats.updates',
        ];

        return in_array($routeName, $bookingRoutes);
    }
}
