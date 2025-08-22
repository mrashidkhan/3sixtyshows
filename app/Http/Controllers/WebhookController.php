<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\PayPalService;
use App\Services\PayPalWebhookService;
use App\Models\Booking;
use App\Models\WebhookLog;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class WebhookController extends Controller
{
    private $paypalService;
    private $webhookService;

    public function __construct(PayPalService $paypalService, PayPalWebhookService $webhookService)
    {
        $this->paypalService = $paypalService;
        $this->webhookService = $webhookService;
    }

    /**
     * Handle PayPal webhook
     */
    public function paypalWebhook(Request $request)
    {
        $startTime = microtime(true);
        $payload = $request->getContent();
        $headers = $request->headers->all();

        // Generate unique webhook ID for tracking
        $webhookId = uniqid('wh_', true);

        try {
            // Log incoming webhook
            $this->logWebhook($webhookId, 'received', $payload, $headers);

            // Verify webhook signature for security
            if (!$this->webhookService->verifySignature($payload, $headers)) {
                $this->logWebhook($webhookId, 'signature_failed', $payload, $headers);
                return response()->json(['error' => 'Invalid signature'], 401);
            }

            // Parse webhook data
            $data = json_decode($payload, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                $this->logWebhook($webhookId, 'json_error', $payload, $headers);
                return response()->json(['error' => 'Invalid JSON'], 400);
            }

            $eventType = $data['event_type'] ?? null;
            $resourceType = $data['resource_type'] ?? null;

            if (!$eventType) {
                $this->logWebhook($webhookId, 'missing_event_type', $payload, $headers);
                return response()->json(['error' => 'Missing event type'], 400);
            }

            // Process webhook based on event type
            $result = $this->processWebhookEvent($data, $webhookId);

            // Log successful processing
            $processingTime = round((microtime(true) - $startTime) * 1000, 2);
            $this->logWebhook($webhookId, 'processed', $payload, $headers, [
                'event_type' => $eventType,
                'processing_time_ms' => $processingTime,
                'result' => $result
            ]);

            return response()->json(['status' => 'success', 'webhook_id' => $webhookId]);

        } catch (\Exception $e) {
            // Log error
            $this->logWebhook($webhookId, 'error', $payload, $headers, [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            Log::error('PayPal webhook processing error', [
                'webhook_id' => $webhookId,
                'error' => $e->getMessage(),
                'payload' => $payload
            ]);

            return response()->json(['error' => 'Webhook processing failed'], 500);
        }
    }

    /**
     * Process specific webhook events
     */
    private function processWebhookEvent($data, $webhookId)
    {
        $eventType = $data['event_type'];
        $resource = $data['resource'] ?? [];

        Log::info('Processing PayPal webhook', [
            'webhook_id' => $webhookId,
            'event_type' => $eventType,
            'resource_id' => $resource['id'] ?? null
        ]);

        switch ($eventType) {
            case 'PAYMENT.CAPTURE.COMPLETED':
                return $this->handlePaymentCaptureCompleted($resource, $webhookId);

            case 'PAYMENT.CAPTURE.DENIED':
                return $this->handlePaymentCaptureDenied($resource, $webhookId);

            case 'CHECKOUT.ORDER.APPROVED':
                return $this->handleOrderApproved($resource, $webhookId);

            case 'CHECKOUT.ORDER.COMPLETED':
                return $this->handleOrderCompleted($resource, $webhookId);

            case 'PAYMENT.CAPTURE.REFUNDED':
                return $this->handlePaymentRefunded($resource, $webhookId);

            case 'BILLING.SUBSCRIPTION.CANCELLED':
                return $this->handleSubscriptionCancelled($resource, $webhookId);

            default:
                Log::info('Unhandled PayPal webhook event', [
                    'webhook_id' => $webhookId,
                    'event_type' => $eventType
                ]);
                return ['status' => 'ignored', 'reason' => 'Event type not handled'];
        }
    }

    /**
     * Handle completed payment capture
     */
    private function handlePaymentCaptureCompleted($resource, $webhookId)
    {
        $captureId = $resource['id'] ?? null;
        $orderId = $resource['supplementary_data']['related_ids']['order_id'] ?? null;

        if (!$captureId || !$orderId) {
            throw new \Exception('Missing capture or order ID in webhook');
        }

        // Find booking by PayPal order ID
        $booking = Booking::where('paypal_order_id', $orderId)->first();

        if (!$booking) {
            Log::warning('Booking not found for PayPal order', [
                'webhook_id' => $webhookId,
                'order_id' => $orderId,
                'capture_id' => $captureId
            ]);
            return ['status' => 'ignored', 'reason' => 'Booking not found'];
        }

        // Check if already processed
        if ($booking->payment_status === 'paid') {
            Log::info('Payment already processed', [
                'webhook_id' => $webhookId,
                'booking_id' => $booking->id,
                'order_id' => $orderId
            ]);
            return ['status' => 'already_processed'];
        }

        DB::transaction(function () use ($booking, $resource, $captureId, $webhookId) {
            // Update booking status
            $booking->update([
                'status' => 'confirmed',
                'payment_status' => 'paid',
                'paypal_capture_id' => $captureId,
                'paid_at' => now(),
                'webhook_processed_at' => now()
            ]);

            // Generate tickets if not already generated
            $this->generateTickets($booking);

            // Send confirmation email
            $this->sendConfirmationEmail($booking);

            Log::info('Payment capture completed via webhook', [
                'webhook_id' => $webhookId,
                'booking_id' => $booking->id,
                'booking_number' => $booking->booking_number,
                'amount' => $booking->total_amount,
                'capture_id' => $captureId
            ]);
        });

        return ['status' => 'processed', 'booking_id' => $booking->id];
    }

    /**
     * Handle denied payment capture
     */
    private function handlePaymentCaptureDenied($resource, $webhookId)
    {
        $orderId = $resource['supplementary_data']['related_ids']['order_id'] ?? null;

        if (!$orderId) {
            throw new \Exception('Missing order ID in webhook');
        }

        $booking = Booking::where('paypal_order_id', $orderId)->first();

        if (!$booking) {
            return ['status' => 'ignored', 'reason' => 'Booking not found'];
        }

        $booking->update([
            'status' => 'failed',
            'payment_status' => 'failed',
            'webhook_processed_at' => now()
        ]);

        Log::warning('Payment capture denied via webhook', [
            'webhook_id' => $webhookId,
            'booking_id' => $booking->id,
            'order_id' => $orderId
        ]);

        return ['status' => 'processed', 'booking_id' => $booking->id, 'action' => 'marked_failed'];
    }

    /**
     * Handle order approved (but not yet captured)
     */
    private function handleOrderApproved($resource, $webhookId)
    {
        $orderId = $resource['id'] ?? null;

        if (!$orderId) {
            throw new \Exception('Missing order ID in webhook');
        }

        $booking = Booking::where('paypal_order_id', $orderId)->first();

        if (!$booking) {
            return ['status' => 'ignored', 'reason' => 'Booking not found'];
        }

        Log::info('Order approved via webhook', [
            'webhook_id' => $webhookId,
            'booking_id' => $booking->id,
            'order_id' => $orderId
        ]);

        return ['status' => 'acknowledged', 'booking_id' => $booking->id];
    }

    /**
     * Handle completed order
     */
    private function handleOrderCompleted($resource, $webhookId)
    {
        // Usually handled by PAYMENT.CAPTURE.COMPLETED
        // This provides additional confirmation
        $orderId = $resource['id'] ?? null;

        if (!$orderId) {
            throw new \Exception('Missing order ID in webhook');
        }

        Log::info('Order completed via webhook', [
            'webhook_id' => $webhookId,
            'order_id' => $orderId
        ]);

        return ['status' => 'acknowledged'];
    }

    /**
     * Handle payment refunded
     */
    private function handlePaymentRefunded($resource, $webhookId)
    {
        $refundId = $resource['id'] ?? null;
        $captureId = $resource['links'][0]['href'] ?? null;

        // Extract capture ID from refund links if available
        if ($captureId && preg_match('/captures\/([A-Z0-9]+)/', $captureId, $matches)) {
            $captureId = $matches[1];
        }

        $booking = Booking::where('paypal_capture_id', $captureId)->first();

        if (!$booking) {
            Log::warning('Booking not found for refund', [
                'webhook_id' => $webhookId,
                'refund_id' => $refundId,
                'capture_id' => $captureId
            ]);
            return ['status' => 'ignored', 'reason' => 'Booking not found'];
        }

        $booking->update([
            'status' => 'refunded',
            'payment_status' => 'refunded',
            'refunded_at' => now(),
            'webhook_processed_at' => now()
        ]);

        Log::info('Payment refunded via webhook', [
            'webhook_id' => $webhookId,
            'booking_id' => $booking->id,
            'refund_id' => $refundId
        ]);

        return ['status' => 'processed', 'booking_id' => $booking->id, 'action' => 'marked_refunded'];
    }

    /**
     * Handle subscription cancelled (if using subscriptions)
     */
    private function handleSubscriptionCancelled($resource, $webhookId)
    {
        // Implement if you have subscription-based bookings
        Log::info('Subscription cancelled via webhook', [
            'webhook_id' => $webhookId,
            'subscription_id' => $resource['id'] ?? null
        ]);

        return ['status' => 'acknowledged'];
    }

    /**
     * Generate tickets for confirmed booking
     */
    private function generateTickets($booking)
    {
        foreach ($booking->bookingTickets as $bookingTicket) {
            if (!$bookingTicket->ticket_number) {
                $bookingTicket->update([
                    'ticket_number' => 'TKT-' . $booking->booking_number . '-' . str_pad($bookingTicket->id, 4, '0', STR_PAD_LEFT),
                    'qr_code' => hash('sha256', $booking->id . $bookingTicket->id . now()),
                    'status' => 'active'
                ]);
            }
        }
    }

    /**
     * Send confirmation email
     */
    private function sendConfirmationEmail($booking)
    {
        try {
            // Implement your email sending logic here
            // Mail::to($booking->customer_email)->send(new BookingConfirmationMail($booking));

            Log::info('Confirmation email queued', [
                'booking_id' => $booking->id,
                'email' => $booking->customer_email
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send confirmation email', [
                'booking_id' => $booking->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Log webhook activity
     */
    private function logWebhook($webhookId, $status, $payload, $headers, $additional = [])
    {
        try {
            WebhookLog::create([
                'webhook_id' => $webhookId,
                'status' => $status,
                'payload' => $payload,
                'headers' => json_encode($headers),
                'additional_data' => $additional ? json_encode($additional) : null,
                'processed_at' => now()
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to log webhook', [
                'webhook_id' => $webhookId,
                'error' => $e->getMessage()
            ]);
        }
    }
}
