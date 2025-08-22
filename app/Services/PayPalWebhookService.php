<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class PayPalWebhookService
{
    private $paypalService;
    private $webhookId;

    public function __construct(PayPalService $paypalService)
    {
        $this->paypalService = $paypalService;
        $this->webhookId = config('paypal.webhook_id'); // You'll need to set this
    }

    /**
     * Verify PayPal webhook signature
     *
     * @param string $payload The raw webhook payload
     * @param array $headers Request headers
     * @return bool True if signature is valid
     */
    public function verifySignature($payload, $headers)
    {
        // In development, you might want to skip verification
        if (config('app.env') === 'local' && config('paypal.skip_webhook_verification', false)) {
            Log::info('Skipping webhook signature verification in local environment');
            return true;
        }

        try {
            // Extract required headers
            $authAlgo = $this->getHeader($headers, 'paypal-auth-algo');
            $transmission = $this->getHeader($headers, 'paypal-transmission-id');
            $certId = $this->getHeader($headers, 'paypal-cert-id');
            $signature = $this->getHeader($headers, 'paypal-transmission-sig');
            $timestamp = $this->getHeader($headers, 'paypal-transmission-time');

            if (!$authAlgo || !$transmission || !$certId || !$signature || !$timestamp) {
                Log::error('Missing required PayPal webhook headers', [
                    'auth_algo' => $authAlgo,
                    'transmission_id' => $transmission,
                    'cert_id' => $certId,
                    'signature' => $signature ? 'present' : 'missing',
                    'timestamp' => $timestamp
                ]);
                return false;
            }

            // Verify signature using PayPal API
            return $this->verifyWithPayPalAPI($payload, [
                'auth_algo' => $authAlgo,
                'transmission_id' => $transmission,
                'cert_id' => $certId,
                'transmission_sig' => $signature,
                'transmission_time' => $timestamp
            ]);

        } catch (\Exception $e) {
            Log::error('PayPal webhook signature verification error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    /**
     * Verify signature using PayPal's verification API
     */
    private function verifyWithPayPalAPI($payload, $signatureData)
    {
        try {
            $accessToken = $this->paypalService->getAccessToken();
            $baseUrl = config('paypal.mode') === 'sandbox'
                ? 'https://api.sandbox.paypal.com'
                : 'https://api.paypal.com';

            $verificationData = [
                'auth_algo' => $signatureData['auth_algo'],
                'transmission_id' => $signatureData['transmission_id'],
                'cert_id' => $signatureData['cert_id'],
                'transmission_sig' => $signatureData['transmission_sig'],
                'transmission_time' => $signatureData['transmission_time'],
                'webhook_id' => $this->webhookId,
                'webhook_event' => json_decode($payload, true)
            ];

            $response = Http::withToken($accessToken)
                ->post($baseUrl . '/v1/notifications/verify-webhook-signature', $verificationData);

            if (!$response->successful()) {
                Log::error('PayPal signature verification API error', [
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);
                return false;
            }

            $result = $response->json();
            $verified = ($result['verification_status'] ?? '') === 'SUCCESS';

            Log::info('PayPal webhook signature verification result', [
                'verified' => $verified,
                'status' => $result['verification_status'] ?? 'unknown'
            ]);

            return $verified;

        } catch (\Exception $e) {
            Log::error('PayPal signature verification API call failed', [
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Get header value (case-insensitive)
     */
    private function getHeader($headers, $name)
    {
        $name = strtolower($name);

        foreach ($headers as $key => $value) {
            if (strtolower($key) === $name) {
                return is_array($value) ? $value[0] : $value;
            }
        }

        return null;
    }

    /**
     * Validate webhook timestamp (prevent replay attacks)
     */
    public function isTimestampValid($timestamp, $toleranceSeconds = 300)
    {
        $webhookTime = (int) $timestamp;
        $currentTime = time();
        $timeDiff = abs($currentTime - $webhookTime);

        $valid = $timeDiff <= $toleranceSeconds;

        if (!$valid) {
            Log::warning('PayPal webhook timestamp outside tolerance', [
                'webhook_timestamp' => $webhookTime,
                'current_timestamp' => $currentTime,
                'difference_seconds' => $timeDiff,
                'tolerance_seconds' => $toleranceSeconds
            ]);
        }

        return $valid;
    }

    /**
     * Create webhook endpoint (for setup)
     */
    public function createWebhookEndpoint($url)
    {
        try {
            $accessToken = $this->paypalService->getAccessToken();
            $baseUrl = config('paypal.mode') === 'sandbox'
                ? 'https://api.sandbox.paypal.com'
                : 'https://api.paypal.com';

            $webhookData = [
                'url' => $url,
                'event_types' => config('paypal.webhook_events', [
                    ['name' => 'PAYMENT.CAPTURE.COMPLETED'],
                    ['name' => 'PAYMENT.CAPTURE.DENIED'],
                    ['name' => 'CHECKOUT.ORDER.APPROVED'],
                    ['name' => 'CHECKOUT.ORDER.COMPLETED'],
                    ['name' => 'PAYMENT.CAPTURE.REFUNDED']
                ])
            ];

            $response = Http::withToken($accessToken)
                ->post($baseUrl . '/v1/notifications/webhooks', $webhookData);

            if ($response->successful()) {
                $result = $response->json();
                Log::info('PayPal webhook endpoint created', [
                    'webhook_id' => $result['id'],
                    'url' => $url
                ]);
                return $result;
            } else {
                Log::error('Failed to create PayPal webhook endpoint', [
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);
                return false;
            }

        } catch (\Exception $e) {
            Log::error('Error creating PayPal webhook endpoint', [
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * List existing webhooks
     */
    public function listWebhooks()
    {
        try {
            $accessToken = $this->paypalService->getAccessToken();
            $baseUrl = config('paypal.mode') === 'sandbox'
                ? 'https://api.sandbox.paypal.com'
                : 'https://api.paypal.com';

            $response = Http::withToken($accessToken)
                ->get($baseUrl . '/v1/notifications/webhooks');

            if ($response->successful()) {
                return $response->json();
            } else {
                Log::error('Failed to list PayPal webhooks', [
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);
                return false;
            }

        } catch (\Exception $e) {
            Log::error('Error listing PayPal webhooks', [
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
}
