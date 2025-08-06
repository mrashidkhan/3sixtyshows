<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\Booking;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class PaymentService
{
    /**
     * Process payment for a booking
     */
    public function processPayment(Booking $booking, array $paymentData)
    {
        DB::beginTransaction();

        try {
            $payment = Payment::create([
                'booking_id' => $booking->id,
                'amount' => $booking->total_amount,
                'currency' => $paymentData['currency'] ?? 'USD',
                'payment_method' => $paymentData['method'] ?? 'stripe',
                'payment_gateway' => $paymentData['gateway'] ?? 'stripe',
                'transaction_id' => $paymentData['transaction_id'] ?? null,
                'gateway_response' => $paymentData['gateway_response'] ?? null,
                'status' => Payment::STATUS_PENDING,
            ]);

            // Simulate payment processing
            $paymentResult = $this->processWithGateway($payment, $paymentData);

            if ($paymentResult['success']) {
                $payment->update([
                    'status' => Payment::STATUS_COMPLETED,
                    'transaction_id' => $paymentResult['transaction_id'],
                    'paid_at' => now(),
                ]);

                $booking->update(['payment_status' => 'paid']);
            } else {
                $payment->update([
                    'status' => Payment::STATUS_FAILED,
                    'failure_reason' => $paymentResult['error'],
                ]);

                $booking->update(['payment_status' => 'failed']);
            }

            DB::commit();

            return [
                'success' => $paymentResult['success'],
                'payment' => $payment,
                'message' => $paymentResult['success'] ? 'Payment processed successfully' : $paymentResult['error']
            ];

        } catch (Exception $e) {
            DB::rollback();
            Log::error('Payment processing failed', [
                'booking_id' => $booking->id,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'payment' => null,
                'message' => 'Payment processing failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Process refund for a payment
     */
    public function processRefund(Payment $payment, $amount = null)
    {
        if ($payment->status !== Payment::STATUS_COMPLETED) {
            return [
                'success' => false,
                'message' => 'Cannot refund a payment that is not completed'
            ];
        }

        $refundAmount = $amount ?? $payment->amount;

        DB::beginTransaction();

        try {
            // Simulate refund processing with gateway
            $refundResult = $this->processRefundWithGateway($payment, $refundAmount);

            if ($refundResult['success']) {
                $payment->update([
                    'refund_amount' => $refundAmount,
                    'refund_status' => 'completed',
                    'refunded_at' => now(),
                ]);

                $payment->booking->update(['payment_status' => 'refunded']);
            }

            DB::commit();

            return $refundResult;

        } catch (Exception $e) {
            DB::rollback();
            Log::error('Refund processing failed', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Refund processing failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Simulate payment gateway processing
     */
    protected function processWithGateway(Payment $payment, array $paymentData)
    {
        // This is a simulation - replace with actual gateway integration
        $success = true; // Simulate successful payment

        if ($success) {
            return [
                'success' => true,
                'transaction_id' => 'txn_' . uniqid(),
                'message' => 'Payment successful'
            ];
        } else {
            return [
                'success' => false,
                'error' => 'Payment declined by gateway',
                'transaction_id' => null
            ];
        }
    }

    /**
     * Simulate refund gateway processing
     */
    protected function processRefundWithGateway(Payment $payment, $amount)
    {
        // This is a simulation - replace with actual gateway integration
        $success = true; // Simulate successful refund

        if ($success) {
            return [
                'success' => true,
                'refund_id' => 'ref_' . uniqid(),
                'message' => 'Refund processed successfully'
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Refund failed at gateway'
            ];
        }
    }

    /**
     * Get payment status from gateway
     */
    public function getPaymentStatus($transactionId)
    {
        // This would typically query the payment gateway for status
        // For now, return a mock response
        return [
            'status' => 'completed',
            'amount' => 0,
            'currency' => 'USD'
        ];
    }

    /**
     * Validate payment data
     */
    public function validatePaymentData(array $data)
    {
        $required = ['method', 'amount'];

        foreach ($required as $field) {
            if (!isset($data[$field]) || empty($data[$field])) {
                return [
                    'valid' => false,
                    'message' => "Required field '{$field}' is missing"
                ];
            }
        }

        if ($data['amount'] <= 0) {
            return [
                'valid' => false,
                'message' => 'Payment amount must be greater than zero'
            ];
        }

        return ['valid' => true];
    }

    /**
     * Calculate processing fees
     */
    public function calculateFees($amount, $method = 'stripe')
    {
        $feeRates = [
            'stripe' => 0.029, // 2.9%
            'paypal' => 0.032, // 3.2%
            'bank' => 0.01,    // 1%
        ];

        $rate = $feeRates[$method] ?? 0.029;
        return round($amount * $rate, 2);
    }

    /**
     * Generate payment receipt data
     */
    public function generateReceiptData(Payment $payment)
    {
        return [
            'payment_id' => $payment->id,
            'booking_id' => $payment->booking_id,
            'amount' => $payment->amount,
            'currency' => $payment->currency,
            'paid_at' => $payment->paid_at,
            'transaction_id' => $payment->transaction_id,
            'payment_method' => $payment->payment_method,
        ];
    }
}
