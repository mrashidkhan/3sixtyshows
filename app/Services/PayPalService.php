<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class PayPalService
{
    private $client;
    private $baseUrl;
    private $clientId;
    private $clientSecret;
    private $currency;

    public function __construct()
    {
        $config = config('paypal');
        $mode = $config['mode'];

        $this->baseUrl = $config[$mode]['api_url'];
        $this->clientId = $config[$mode]['client_id'];
        $this->clientSecret = $config[$mode]['client_secret'];
        $this->currency = $config['currency'];

        $this->client = new Client([
            'base_uri' => $this->baseUrl,
            'timeout' => $config['timeout'],
            'connect_timeout' => $config['connect_timeout'],
            'verify' => $this->getSSLCertPath(),
        ]);
    }

    /**
     * Get PayPal access token
     */
    /**
 * Get PayPal access token with enhanced error handling
 */
public function getAccessToken()
{
    $cacheKey = 'paypal_access_token';

    // Check if token exists in cache
    if (Cache::has($cacheKey)) {
        return Cache::get($cacheKey);
    }

    // Validate credentials before making request
    if (empty($this->clientId) || empty($this->clientSecret)) {
        Log::error('PayPal credentials missing', [
            'client_id_set' => !empty($this->clientId),
            'client_secret_set' => !empty($this->clientSecret),
            'base_url' => $this->baseUrl
        ]);
        throw new \Exception('PayPal credentials not configured properly');
    }

    try {
        Log::info('PayPal access token request', [
            'url' => $this->baseUrl . '/v1/oauth2/token',
            'client_id_length' => strlen($this->clientId),
            'client_secret_length' => strlen($this->clientSecret)
        ]);

        $response = $this->client->post('/v1/oauth2/token', [
            'headers' => [
                'Accept' => 'application/json',
                'Accept-Language' => 'en_US',
            ],
            'auth' => [$this->clientId, $this->clientSecret],
            'form_params' => [
                'grant_type' => 'client_credentials'
            ]
        ]);

        $statusCode = $response->getStatusCode();
        $responseBody = $response->getBody()->getContents();

        Log::info('PayPal access token response', [
            'status_code' => $statusCode,
            'response_length' => strlen($responseBody)
        ]);

        if ($statusCode !== 200) {
            Log::error('PayPal access token failed', [
                'status_code' => $statusCode,
                'response' => $responseBody
            ]);
            throw new \Exception("PayPal API returned status code: {$statusCode}");
        }

        $data = json_decode($responseBody, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            Log::error('PayPal access token JSON decode error', [
                'error' => json_last_error_msg(),
                'response' => $responseBody
            ]);
            throw new \Exception('Invalid JSON response from PayPal');
        }

        if (!isset($data['access_token'])) {
            Log::error('PayPal access token missing in response', [
                'response_keys' => array_keys($data),
                'response' => $data
            ]);
            throw new \Exception('Access token not found in PayPal response');
        }

        $accessToken = $data['access_token'];
        $expiresIn = ($data['expires_in'] ?? 3600) - 300; // Subtract 5 minutes for safety

        // Cache the token
        Cache::put($cacheKey, $accessToken, $expiresIn);

        Log::info('PayPal access token obtained successfully', [
            'token_length' => strlen($accessToken),
            'expires_in' => $expiresIn
        ]);

        return $accessToken;

    } catch (RequestException $e) {
        $statusCode = $e->hasResponse() ? $e->getResponse()->getStatusCode() : 'unknown';
        $responseBody = $e->hasResponse() ? $e->getResponse()->getBody()->getContents() : 'no response';

        Log::error('PayPal access token RequestException', [
            'message' => $e->getMessage(),
            'status_code' => $statusCode,
            'response' => $responseBody,
            'url' => $this->baseUrl . '/v1/oauth2/token'
        ]);

        throw new \Exception("Failed to get PayPal access token: {$e->getMessage()}. Response: {$responseBody}");
    } catch (\Exception $e) {
        Log::error('PayPal access token general error', [
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);

        throw new \Exception("PayPal access token error: {$e->getMessage()}");
    }
}

    /**
     * Get SSL certificate path for PayPal API requests
     * Handles SSL certificate verification issues on different platforms
     */
    private function getSSLCertPath()
    {
        // Check if SSL verification is disabled in config
        $config = config('paypal');
        if (isset($config['ssl_verify']) && $config['ssl_verify'] === false) {
            return false;
        }

        // Check if a custom certificate path is defined in config
        if (isset($config['ssl_cert_path'])) {
            $certPath = $config['ssl_cert_path'];
            // If it's a relative path, make it absolute
            if (!str_starts_with($certPath, '/') && !str_starts_with($certPath, '\\') && !preg_match('/^[A-Za-z]:/', $certPath)) {
                $certPath = base_path($certPath);
            }
            if (file_exists($certPath)) {
                return $certPath;
            }
        }

        // For Windows systems, use the system certificate bundle or disable verification
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {

            // Try to use the system certificate bundle
            $certPath = ini_get('curl.cainfo');
            if ($certPath && file_exists($certPath)) {
                return $certPath;
            }

            // If we can't find a certificate bundle, we have a few options:
            // 1. Try to download one (not recommended in production)
            // 2. Disable verification (not recommended for production)
            // 3. Use a bundled certificate file

            // For development, we'll try to use a bundled certificate
            $bundledCert = storage_path('app/cacert.pem');
            if (file_exists($bundledCert)) {
                return $bundledCert;
            }

            // If nothing else works and we're in local environment, disable verification
            if (app()->environment('local')) {
                return false; // Disable SSL verification for local development
            }
        } else {
            // For non-Windows systems, use system default
            return true;
        }

        // Default to system default
        return true;
    }

    /**
     * Create PayPal order
     */
    public function createOrder($amount, $description = 'Ticket Purchase', $orderId = null, $returnUrl = null, $cancelUrl = null)
    {
        try {
            $accessToken = $this->getAccessToken();

            // Use custom URLs if provided, otherwise use config URLs
            $returnUrl = $returnUrl ?? config('paypal.urls.success');
            $cancelUrl = $cancelUrl ?? config('paypal.urls.cancel');

            // Replace placeholders in URLs if they still contain them
            if (strpos($returnUrl, '{slug}') !== false || strpos($cancelUrl, '{slug}') !== false) {
                // Use a generic slug for testing purposes
                $placeholderSlug = 'test';
                $returnUrl = str_replace('{slug}', $placeholderSlug, $returnUrl);
                $cancelUrl = str_replace('{slug}', $placeholderSlug, $cancelUrl);
            }

            $orderData = [
                'intent' => 'CAPTURE',
                'purchase_units' => [
                    [
                        'reference_id' => $orderId ?? 'ORDER_' . time(),
                        'description' => $description,
                        'amount' => [
                            'currency_code' => $this->currency,
                            'value' => number_format($amount, 2, '.', '')
                        ]
                    ]
                ],
                'application_context' => [
                    'return_url' => $returnUrl,
                    'cancel_url' => $cancelUrl,
                    'brand_name' => '3Sixty Shows',
                    'landing_page' => 'BILLING',
                    'user_action' => 'PAY_NOW',
                    'shipping_preference' => 'NO_SHIPPING'
                ]
            ];

            $response = $this->client->post('/v2/checkout/orders', [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $accessToken,
                ],
                'json' => $orderData
            ]);

            $responseData = json_decode($response->getBody(), true);

            Log::info('PayPal order created', [
                'order_id' => $responseData['id'],
                'amount' => $amount,
                'status' => $responseData['status']
            ]);

            return $responseData;

        } catch (RequestException $e) {
            $statusCode = $e->hasResponse() ? $e->getResponse()->getStatusCode() : 'unknown';
            $responseBody = $e->hasResponse() ? $e->getResponse()->getBody()->getContents() : 'no response';

            Log::error('PayPal create order RequestException', [
                'message' => $e->getMessage(),
                'status_code' => $statusCode,
                'response' => $responseBody,
                'url' => $this->baseUrl . '/v2/checkout/orders'
            ]);

            throw new \Exception("Failed to create PayPal order: {$e->getMessage()}. Response: {$responseBody}");
        } catch (\Exception $e) {
            Log::error('PayPal create order general error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            throw new \Exception("Failed to create PayPal order: {$e->getMessage()}");
        }
    }

    /**
     * Capture PayPal payment
     */
    public function capturePayment($orderId)
    {
        try {
            $accessToken = $this->getAccessToken();

            $response = $this->client->post("/v2/checkout/orders/{$orderId}/capture", [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $accessToken,
                ]
            ]);

            $responseData = json_decode($response->getBody(), true);

            Log::info('PayPal payment captured', [
                'order_id' => $orderId,
                'capture_id' => $responseData['purchase_units'][0]['payments']['captures'][0]['id'] ?? null,
                'status' => $responseData['status']
            ]);

            return $responseData;

        } catch (RequestException $e) {
            Log::error('PayPal capture payment error: ' . $e->getMessage());
            throw new \Exception('Failed to capture PayPal payment');
        }
    }

    /**
     * Get order details
     */
    public function getOrderDetails($orderId)
    {
        try {
            $accessToken = $this->getAccessToken();

            $response = $this->client->get("/v2/checkout/orders/{$orderId}", [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $accessToken,
                ]
            ]);

            return json_decode($response->getBody(), true);

        } catch (RequestException $e) {
            Log::error('PayPal get order details error: ' . $e->getMessage());
            throw new \Exception('Failed to get PayPal order details');
        }
    }

    /**
     * Refund payment
     */
    public function refundPayment($captureId, $amount = null, $reason = 'Ticket cancellation')
    {
        try {
            $accessToken = $this->getAccessToken();

            $refundData = [
                'note_to_payer' => $reason
            ];

            // If amount is specified, add it to refund data
            if ($amount) {
                $refundData['amount'] = [
                    'currency_code' => $this->currency,
                    'value' => number_format($amount, 2, '.', '')
                ];
            }

            $response = $this->client->post("/v2/payments/captures/{$captureId}/refund", [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $accessToken,
                ],
                'json' => $refundData
            ]);

            $responseData = json_decode($response->getBody(), true);

            Log::info('PayPal refund processed', [
                'capture_id' => $captureId,
                'refund_id' => $responseData['id'],
                'amount' => $amount,
                'status' => $responseData['status']
            ]);

            return $responseData;

        } catch (RequestException $e) {
            Log::error('PayPal refund error: ' . $e->getMessage());
            throw new \Exception('Failed to process PayPal refund');
        }
    }

    /**
     * Verify webhook signature
     */
    public function verifyWebhookSignature($requestBody, $headers)
    {
        // Implementation for webhook signature verification
        // This is important for security in production
        return true; // Simplified for now
    }
}
