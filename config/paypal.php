<?php

return [
    /*
    |--------------------------------------------------------------------------
    | PayPal Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for PayPal REST API integration
    |
    */

    'mode' => env('PAYPAL_MODE', 'sandbox'), // sandbox or live

    'sandbox' => [
        'client_id' => env('PAYPAL_SANDBOX_CLIENT_ID'),
        'client_secret' => env('PAYPAL_SANDBOX_CLIENT_SECRET'),
        'api_url' => env('PAYPAL_SANDBOX_API_URL', 'https://api-m.sandbox.paypal.com'),
    ],

    'live' => [
        'client_id' => env('PAYPAL_LIVE_CLIENT_ID'),
        'client_secret' => env('PAYPAL_LIVE_CLIENT_SECRET'),
        'api_url' => env('PAYPAL_LIVE_API_URL', 'https://api-m.paypal.com'),
    ],

    // Application URLs
    'urls' => [
        'success' => env('PAYPAL_SUCCESS_URL', env('APP_URL') . '/payment/success'),
        'cancel' => env('PAYPAL_CANCEL_URL', env('APP_URL') . '/payment/cancel'),
        'webhook' => env('PAYPAL_WEBHOOK_URL', env('APP_URL') . '/webhooks/payment/paypal'),
    ],

    // Currency settings
    'currency' => env('PAYPAL_CURRENCY', 'USD'),

    // PayPal webhook events to listen for
    'webhook_events' => [
        'PAYMENT.CAPTURE.COMPLETED',
        'PAYMENT.CAPTURE.DENIED',
        'CHECKOUT.ORDER.APPROVED',
        'CHECKOUT.ORDER.COMPLETED',
    ],

    // Timeout settings
    'timeout' => 30,
    'connect_timeout' => 10,

    // SSL settings
    'ssl_verify' => env('PAYPAL_SSL_VERIFY', true),
    'ssl_cert_path' => env('PAYPAL_SSL_CERT_PATH', null),
];
