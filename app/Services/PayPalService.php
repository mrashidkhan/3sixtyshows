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

    // Update all other methods to use getHttpClient() instead of Http::

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

            $response = $this->getHttpClient()
                ->withToken($token)
                ->post("{$this->apiUrl}/v2/checkout/orders/{$orderId}/capture");

            if ($response->successful()) {
                $capture = $response->json();
                Log::info('PayPal Payment Captured', [
                    'order_id' => $orderId,
                    'status' => $capture['status'],
                    'capture_id' => $capture['purchase_units'][0]['payments']['captures'][0]['id'] ?? null
                ]);
                return $capture;
            }

            throw new \Exception('PayPal Capture failed: ' . $response->body());

        } catch (\Exception $e) {
            Log::error('PayPal Capture Error: ' . $e->getMessage());
            throw $e;
        }
    }

    public function getOrderDetails($orderId)
    {
        try {
            $token = $this->getAccessToken();

            $response = $this->getHttpClient()
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
}
