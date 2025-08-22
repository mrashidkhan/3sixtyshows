<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WebhookLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'webhook_id',
        'status',
        'event_type',
        'payload',
        'headers',
        'additional_data',
        'resource_id',
        'booking_id',
        'processed_at',
        'processing_time_ms'
    ];

    protected $casts = [
        'processed_at' => 'datetime',
        'headers' => 'array',
        'additional_data' => 'array'
    ];

    /**
     * Get the booking associated with this webhook
     */
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    /**
     * Scope to filter by status
     */
    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope to filter by event type
     */
    public function scopeEventType($query, $eventType)
    {
        return $query->where('event_type', $eventType);
    }

    /**
     * Scope for recent webhooks
     */
    public function scopeRecent($query, $hours = 24)
    {
        return $query->where('processed_at', '>=', now()->subHours($hours));
    }

    /**
     * Scope for failed webhooks
     */
    public function scopeFailed($query)
    {
        return $query->whereIn('status', ['error', 'signature_failed', 'json_error']);
    }

    /**
     * Scope for successful webhooks
     */
    public function scopeSuccessful($query)
    {
        return $query->where('status', 'processed');
    }

    /**
     * Get decoded payload
     */
    public function getDecodedPayloadAttribute()
    {
        return json_decode($this->payload, true);
    }

    /**
     * Check if webhook was processed successfully
     */
    public function isSuccessful(): bool
    {
        return $this->status === 'processed';
    }

    /**
     * Check if webhook failed
     */
    public function isFailed(): bool
    {
        return in_array($this->status, ['error', 'signature_failed', 'json_error']);
    }

    /**
     * Get human-readable status
     */
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'received' => 'Received',
            'processed' => 'Processed Successfully',
            'error' => 'Processing Error',
            'signature_failed' => 'Invalid Signature',
            'json_error' => 'Invalid JSON',
            'ignored' => 'Ignored',
            'already_processed' => 'Already Processed',
            default => ucfirst($this->status)
        };
    }

    /**
     * Get color class for status display
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'processed' => 'text-green-600 bg-green-100',
            'received' => 'text-blue-600 bg-blue-100',
            'error', 'signature_failed', 'json_error' => 'text-red-600 bg-red-100',
            'ignored', 'already_processed' => 'text-gray-600 bg-gray-100',
            default => 'text-gray-600 bg-gray-100'
        };
    }
}
