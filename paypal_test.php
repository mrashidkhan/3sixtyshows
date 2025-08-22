<?php
require_once 'vendor/autoload.php';

use App\Services\PayPalService;

try {
    // Test PayPal service
    $paypal = new PayPalService();

    // Test getting access token
    echo "Testing PayPal access token...\n";
    $token = $paypal->getAccessToken();
    echo "Access token obtained successfully\n";
    echo "Token length: " . strlen($token) . "\n";

    // Test creating an order
    echo "\nTesting PayPal order creation...\n";
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

    echo "Approval URL: " . $approvalUrl . "\n";

    echo "\nPayPal integration is working correctly!\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}
