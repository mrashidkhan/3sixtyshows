<?php
// Simple script to test PayPal integration
require_once 'vendor/autoload.php';

use GuzzleHttp\Client;

try {
    $client = new Client([
        'base_uri' => 'http://localhost:8000',
        'timeout' => 30,
    ]);

    // First, get the CSRF token and login
    $loginResponse = $client->get('/user/login');
    $loginBody = $loginResponse->getBody()->getContents();

    // Extract CSRF token from the login page
    preg_match('/<input type="hidden" name="_token" value="([^"]+)">/', $loginBody, $matches);
    $csrfToken = $matches[1] ?? '';

    // Login with the user credentials
    $loginResponse = $client->post('/user/login', [
        'form_params' => [
            '_token' => $csrfToken,
            'email' => 'mrashid@gmail.com',
            'password' => 'password', // Default password for testing
        ],
        'allow_redirects' => true,
    ]);

    // Now test the PayPal integration
    $response = $client->get('/test-paypal', [
        'headers' => [
            'Accept' => 'application/json',
        ],
    ]);

    $result = json_decode($response->getBody()->getContents(), true);

    echo "PayPal Integration Test Results:\n";
    echo "================================\n";
    echo "Success: " . ($result['success'] ? 'Yes' : 'No') . "\n";

    if ($result['success']) {
        echo "Token Received: " . ($result['token_received'] ? 'Yes' : 'No') . "\n";
        echo "Token Length: " . $result['token_length'] . "\n";
        echo "Order Created: " . ($result['order_created'] ? 'Yes' : 'No') . "\n";
        echo "Order ID: " . ($result['order_id'] ?? 'N/A') . "\n";

        if (isset($result['approval_url'])) {
            echo "Approval URL: " . $result['approval_url'] . "\n";
        }
    } else {
        echo "Error: " . $result['error'] . "\n";
    }

    echo "\nEnvironment Checks:\n";
    print_r($result['env_checks'] ?? []);

    echo "\nConfig Checks:\n";
    print_r($result['config_checks'] ?? []);

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
