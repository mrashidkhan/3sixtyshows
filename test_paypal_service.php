<?php
// Simple script to test PayPal service directly
require_once 'vendor/autoload.php';

// Initialize Laravel app
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    // Test PayPal service directly
    echo "Testing PayPal Service...\n";
    echo "========================\n";

    // Check environment variables
    echo "Environment Variables:\n";
    echo "PAYPAL_MODE: " . env('PAYPAL_MODE', 'NOT SET') . "\n";
    echo "PAYPAL_SANDBOX_CLIENT_ID: " . (env('PAYPAL_SANDBOX_CLIENT_ID') ? 'SET' : 'NOT SET') . "\n";
    echo "PAYPAL_SANDBOX_CLIENT_SECRET: " . (env('PAYPAL_SANDBOX_CLIENT_SECRET') ? 'SET' : 'NOT SET') . "\n";
    echo "\n";

    // Check config values
    echo "Config Values:\n";
    $config = config('paypal');
    echo "Mode: " . ($config['mode'] ?? 'NOT SET') . "\n";

    if (isset($config['mode'])) {
        $mode = $config['mode'];
        echo "Client ID: " . (isset($config[$mode]['client_id']) ? 'SET' : 'NOT SET') . "\n";
        echo "Client Secret: " . (isset($config[$mode]['client_secret']) ? 'SET' : 'NOT SET') . "\n";
        echo "API URL: " . ($config[$mode]['api_url'] ?? 'NOT SET') . "\n";
    }
    echo "\n";

    // Try to create PayPal service
    echo "Creating PayPal Service...\n";
    $paypal = new \App\Services\PayPalService();
    echo "PayPal Service created successfully\n\n";

    // Try to get access token
    echo "Getting Access Token...\n";
    $token = $paypal->getAccessToken();
    echo "Access token obtained successfully\n";
    echo "Token length: " . strlen($token) . "\n\n";

    // Test creating an order
    echo "Creating Test Order...\n";
    $order = $paypal->createOrder(10.00, 'Test Ticket Purchase');
    echo "Order created successfully\n";
    echo "Order ID: " . $order['id'] . "\n";
    echo "Order status: " . $order['status'] . "\n";

    // Find approval URL
    $approvalUrl = null;
    foreach ($order['links'] as $link) {
        if ($link['rel'] === 'approve') {
            $approvalUrl = $link['href'];
            break;
        }
    }
    echo "Approval URL: " . $approvalUrl . "\n\n";

    echo "PayPal integration is working correctly!\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}
