<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PayPalService
{
    private $clientId;
    private $clientSecret;
    private $apiUrl;
    private $mode;

    public function __construct()
    {
        $this->mode = config('paypal.mode');
        $this->clientId = config("paypal.{$this->mode}.client_id");
        $this->clientSecret = config("paypal.{$this->mode}.client_secret");
        $this->apiUrl = config("paypal.{$this->mode}.api_url");
    }

    /**
     * Get HTTP client with SSL configuration
     */
    private function getHttpClient()
    {
        $options = [
            'verify' => storage_path('app/cacert.pem'), // Path to CA bundle
            'timeout' => 30
        ];

        return Http::withOptions($options);
    }

    /**
     * Get Access Token with SSL fix
     */
    public function getAccessToken()
    {
        try {
            $response = $this->getHttpClient()
                ->withBasicAuth($this->clientId, $this->clientSecret)
                ->asForm()
                ->post("{$this->apiUrl}/v1/oauth2/token", [
                    'grant_type' => 'client_credentials'
                ]);

            if ($response->successful()) {
                return $response->json()['access_token'];
            }

            throw new \Exception('PayPal Authentication failed: ' . $response->body());
        } catch (\Exception $e) {
            Log::error('PayPal Auth Error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * ✅ MISSING METHOD ADDED - Generic createOrder for Express Checkout
     */
    public function createOrder($amount, $description, $invoiceId = null, $returnUrl = null, $cancelUrl = null)
    {
        try {
            $token = $this->getAccessToken();
            $requestId = uniqid('order_', true) . '_' . time();

            $orderData = [
                'intent' => 'CAPTURE',
                'purchase_units' => [
                    [
                        'amount' => [
                            'currency_code' => 'USD',
                            'value' => number_format($amount, 2, '.', '')
                        ],
                        'description' => $description
                    ]
                ],
                'application_context' => [
                    'return_url' => $returnUrl,
                    'cancel_url' => $cancelUrl,
                    'brand_name' => config('app.name', '3Sixty Shows'),
                    'landing_page' => 'LOGIN',
                    'user_action' => 'PAY_NOW',
                    'shipping_preference' => 'NO_SHIPPING'
                ]
            ];

            // Add invoice ID if provided
            if ($invoiceId) {
                $orderData['purchase_units'][0]['invoice_id'] = $invoiceId;
            }

            $response = $this->getHttpClient()
                ->withToken($token)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'PayPal-Request-Id' => $requestId,
                ])
                ->post("{$this->apiUrl}/v2/checkout/orders", $orderData);

            if ($response->successful()) {
                $order = $response->json();
                Log::info('PayPal Express order created', [
                    'order_id' => $order['id'],
                    'status' => $order['status']
                ]);
                return $order;
            }

            $errorBody = $response->json();
            Log::error('PayPal order creation failed', [
                'status' => $response->status(),
                'error' => $errorBody,
                'request_data' => $orderData
            ]);

            throw new \Exception('PayPal order creation failed: ' . ($errorBody['message'] ?? $response->body()));

        } catch (\Exception $e) {
            Log::error('PayPal Create Order Error: ' . $e->getMessage());
            throw $e;
        }
    }

    // Keep existing methods as they are
    public function createPayPalOrder($amount, $description, $invoiceId, $returnUrl, $cancelUrl)
    {
        try {
            $token = $this->getAccessToken();

            $orderData = [
                'intent' => 'CAPTURE',
                'purchase_units' => [
                    [
                        'amount' => [
                            'currency_code' => 'USD',
                            'value' => number_format($amount, 2, '.', '')
                        ],
                        'description' => $description,
                        'invoice_id' => $invoiceId,
                    ]
                ],
                'application_context' => [
                    'return_url' => $returnUrl,
                    'cancel_url' => $cancelUrl,
                    'brand_name' => config('app.name', '3Sixty Shows'),
                    'landing_page' => 'LOGIN',
                    'user_action' => 'PAY_NOW',
                    'shipping_preference' => 'NO_SHIPPING'
                ]
            ];

            $response = $this->getHttpClient()
                ->withToken($token)
                ->post("{$this->apiUrl}/v2/checkout/orders", $orderData);

            if ($response->successful()) {
                $order = $response->json();
                Log::info('PayPal account order created', ['order_id' => $order['id']]);
                return $order;
            }

            throw new \Exception('PayPal order creation failed: ' . $response->body());

        } catch (\Exception $e) {
            Log::error('PayPal Order Creation Error: ' . $e->getMessage());
            throw $e;
        }
    }

    public function createCreditCardOrder($amount, $description, $invoiceId, $cardData, $billingData)
    {
        try {
            $token = $this->getAccessToken();
            $requestId = uniqid('card_', true) . '_' . time();

            $orderData = [
                'intent' => 'CAPTURE',
                'purchase_units' => [
                    [
                        'amount' => [
                            'currency_code' => 'USD',
                            'value' => number_format($amount, 2, '.', '')
                        ],
                        'description' => $description,
                        'invoice_id' => $invoiceId,
                    ]
                ],
                'payment_source' => [
                    'card' => [
                        'number' => $cardData['card_number'],
                        'expiry' => $cardData['card_expiry_year'] . '-' . $cardData['card_expiry_month'],
                        'security_code' => $cardData['card_cvv'],
                        'name' => $cardData['card_holder_name'],
                        'billing_address' => [
                            'address_line_1' => $billingData['address'],
                            'admin_area_2' => $billingData['city'],
                            'admin_area_1' => $billingData['state'],
                            'postal_code' => $billingData['zip'],
                            'country_code' => 'US'
                        ]
                    ]
                ]
            ];

            $response = $this->getHttpClient()
                ->withToken($token)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'PayPal-Request-Id' => $requestId, // REQUIRED for payment_source
                ])
                ->post("{$this->apiUrl}/v2/checkout/orders", $orderData);

            if ($response->successful()) {
                $order = $response->json();
                Log::info('PayPal credit card order created', ['order_id' => $order['id']]);
                return $order;
            }

            $errorBody = $response->json();
            throw new \Exception('Credit card processing failed: ' . ($errorBody['message'] ?? $response->body()));

        } catch (\Exception $e) {
            Log::error('PayPal Credit Card Order Error: ' . $e->getMessage());
            throw $e;
        }
    }

    public function capturePayment($orderId)
{
    try {
        $token = $this->getAccessToken();
        $requestId = uniqid('capture_', true) . '_' . time();

        // ✅ FIX: Send proper empty JSON object, not empty array
        $captureData = new \stdClass(); // Creates empty JSON object {}

        $response = $this->getHttpClient()
            ->withToken($token)
            ->withHeaders([
                'Content-Type' => 'application/json',
                'PayPal-Request-Id' => $requestId,
            ])
            ->post("{$this->apiUrl}/v2/checkout/orders/{$orderId}/capture", $captureData);

        Log::info('PayPal Capture Request', [
            'order_id' => $orderId,
            'request_id' => $requestId,
            'url' => "{$this->apiUrl}/v2/checkout/orders/{$orderId}/capture",
            'body_type' => gettype($captureData)
        ]);

        Log::info('PayPal Capture Response', [
            'status' => $response->status(),
            'body' => $response->body()
        ]);

        if ($response->successful()) {
            $capture = $response->json();
            Log::info('PayPal Payment Captured Successfully', [
                'order_id' => $orderId,
                'status' => $capture['status'],
                'capture_id' => $capture['purchase_units'][0]['payments']['captures'][0]['id'] ?? null
            ]);
            return $capture;
        }

        $errorBody = $response->json();
        Log::error('PayPal Capture Failed', [
            'order_id' => $orderId,
            'status' => $response->status(),
            'error' => $errorBody
        ]);

        throw new \Exception('PayPal Capture failed: ' . ($errorBody['message'] ?? $response->body()));

    } catch (\Exception $e) {
        Log::error('PayPal Capture Exception', [
            'order_id' => $orderId,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        throw $e;
    }
}

    public function getOrderDetails($orderId)
    {
        try {
            $token = $this->getAccessToken();

            $response = $this->getHttpClient()
                ->withToken($token)
                ->get("{$this->apiUrl}/v2/checkout/orders/{$orderId}");

            if ($response->successful()) {
                return $response->json();
            }

            throw new \Exception('PayPal Get Order failed: ' . $response->body());

        } catch (\Exception $e) {
            Log::error('PayPal Get Order Error: ' . $e->getMessage());
            throw $e;
        }
    }

    public function formatCardData($cardNumber, $cardExpiry, $cardCvv, $cardHolderName)
    {
        $expiry = str_replace('/', '', $cardExpiry);
        if (strlen($expiry) === 4) {
            $month = substr($expiry, 0, 2);
            $year = '20' . substr($expiry, 2, 2);
        } else {
            throw new \Exception('Invalid card expiry format');
        }

        return [
            'card_number' => preg_replace('/\s+/', '', $cardNumber),
            'card_expiry_month' => $month,
            'card_expiry_year' => $year,
            'card_cvv' => $cardCvv,
            'card_holder_name' => trim($cardHolderName)
        ];
    }

    // Add webhook verification method
    public function verifyWebhookSignature($payload, $headers)
    {
        // For now, return true - implement proper verification in production
        return true;
    }
}