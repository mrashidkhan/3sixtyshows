<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id', 'customer_id', 'amount', 'currency', 'payment_method',
        'payment_status', 'transaction_id', 'payment_gateway', 'gateway_response',
        'paid_at', 'refunded_at', 'refund_amount', 'payment_metadata'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'refund_amount' => 'decimal:2',
        'paid_at' => 'datetime',
        'refunded_at' => 'datetime',
        'gateway_response' => 'array',
        'payment_metadata' => 'array',
    ];

    // Payment statuses
    const STATUS_PENDING = 'pending';
    const STATUS_PROCESSING = 'processing';
    const STATUS_COMPLETED = 'completed';
    const STATUS_FAILED = 'failed';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_REFUNDED = 'refunded';
    const STATUS_PARTIALLY_REFUNDED = 'partially_refunded';

    // Payment methods
    const METHOD_CREDIT_CARD = 'credit_card';
    const METHOD_DEBIT_CARD = 'debit_card';
    const METHOD_PAYPAL = 'paypal';
    const METHOD_STRIPE = 'stripe';
    const METHOD_BANK_TRANSFER = 'bank_transfer';
    const METHOD_CASH = 'cash';

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    // Check if payment is successful
    public function isSuccessful()
    {
        return $this->payment_status === self::STATUS_COMPLETED;
    }

    // Check if payment is refundable
    public function isRefundable()
    {
        return in_array($this->payment_status, [self::STATUS_COMPLETED, self::STATUS_PARTIALLY_REFUNDED]);
    }

    // Get remaining refundable amount
    public function getRemainingRefundableAmountAttribute()
    {
        if (!$this->isRefundable()) {
            return 0;
        }

        return $this->amount - ($this->refund_amount ?? 0);
    }

    // Scope for successful payments
    public function scopeSuccessful($query)
    {
        return $query->where('payment_status', self::STATUS_COMPLETED);
    }

    // Scope for failed payments
    public function scopeFailed($query)
    {
        return $query->where('payment_status', self::STATUS_FAILED);
    }
}
