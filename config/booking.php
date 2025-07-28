<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Booking Performance Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains all the configurable options to optimize booking
    | performance and manage booking-related settings.
    |
    */

    // Cache Settings
    'cache' => [
        'seat_data_ttl' => env('BOOKING_SEAT_CACHE_TTL', 300), // 5 minutes
        'show_data_ttl' => env('BOOKING_SHOW_CACHE_TTL', 600), // 10 minutes
        'availability_ttl' => env('BOOKING_AVAILABILITY_CACHE_TTL', 60), // 1 minute
    ],

    // Reservation Settings
    'reservation_timeout' => env('BOOKING_RESERVATION_TIMEOUT', 15), // minutes
    'payment_timeout' => env('BOOKING_PAYMENT_TIMEOUT', 15), // minutes

    // Limits
    'max_seats_per_booking' => env('BOOKING_MAX_SEATS', 10),
    'max_concurrent_bookings' => env('BOOKING_MAX_CONCURRENT', 100),

    // Fees
    'service_fee_percentage' => env('BOOKING_SERVICE_FEE_PERCENTAGE', 2.5),
    'processing_fee' => env('BOOKING_PROCESSING_FEE', 1.50),

    // Performance Settings
    'enable_query_caching' => env('BOOKING_ENABLE_QUERY_CACHE', true),
    'enable_seat_caching' => env('BOOKING_ENABLE_SEAT_CACHE', true),
    'batch_size' => env('BOOKING_BATCH_SIZE', 100),

    // Database Connection Pool
    'use_read_replica' => env('BOOKING_USE_READ_REPLICA', false),
    'read_connection' => env('BOOKING_READ_CONNECTION', 'mysql'),

    // Queue Settings
    'use_queues' => env('BOOKING_USE_QUEUES', true),
    'cleanup_queue' => env('BOOKING_CLEANUP_QUEUE', 'default'),
    'notification_queue' => env('BOOKING_NOTIFICATION_QUEUE', 'notifications'),

    // Cleanup Settings
    'cleanup_expired_bookings' => [
        'enabled' => env('BOOKING_CLEANUP_ENABLED', true),
        'interval' => env('BOOKING_CLEANUP_INTERVAL', 5), // minutes
        'batch_size' => env('BOOKING_CLEANUP_BATCH_SIZE', 50),
    ],

    // API Rate Limiting
    'rate_limiting' => [
        'enabled' => env('BOOKING_RATE_LIMITING', true),
        'max_attempts' => env('BOOKING_MAX_ATTEMPTS', 60), // per minute
        'decay_minutes' => env('BOOKING_DECAY_MINUTES', 1),
    ],

    // Monitoring
    'monitoring' => [
        'log_slow_queries' => env('BOOKING_LOG_SLOW_QUERIES', true),
        'slow_query_threshold' => env('BOOKING_SLOW_QUERY_THRESHOLD', 1000), // milliseconds
        'track_performance_metrics' => env('BOOKING_TRACK_METRICS', true),
    ],
];
