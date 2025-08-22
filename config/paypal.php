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

    // Add these to your existing config/paypal.php file:

// Webhook settings
'webhook_id' => env('PAYPAL_WEBHOOK_ID'),
'skip_webhook_verification' => env('PAYPAL_SKIP_WEBHOOK_VERIFICATION', false),

// Webhook events to listen for
'webhook_events' => [
    ['name' => 'PAYMENT.CAPTURE.COMPLETED'],
    ['name' => 'PAYMENT.CAPTURE.DENIED'],
    ['name' => 'CHECKOUT.ORDER.APPROVED'],
    ['name' => 'CHECKOUT.ORDER.COMPLETED'],
    ['name' => 'PAYMENT.CAPTURE.REFUNDED'],
    ['name' => 'BILLING.SUBSCRIPTION.CANCELLED'],
],

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

