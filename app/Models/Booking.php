<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Booking extends Model
{
    protected $fillable = [
        'customer_id', 'show_id', 'booking_number', 'total_amount', 'status',
        'payment_status', 'payment_method', 'payment_reference',
        'booking_data', 'expires_at', 'confirmed_at', 'number_of_tickets',
        'total_price', 'booking_date', 'transaction_id'
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'total_price' => 'decimal:2',
        'booking_data' => 'array',
        'expires_at' => 'datetime',
        'confirmed_at' => 'datetime',
        'booking_date' => 'datetime',
    ];

    // Booking statuses
    const STATUS_DRAFT = 'draft';
    const STATUS_PENDING = 'pending';
    const STATUS_CONFIRMED = 'confirmed';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_EXPIRED = 'expired';

    // Payment statuses
    const PAYMENT_PENDING = 'pending';
    const PAYMENT_PROCESSING = 'processing';
    const PAYMENT_COMPLETED = 'completed';
    const PAYMENT_FAILED = 'failed';
    const PAYMENT_REFUNDED = 'refunded';

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function show()
    {
        return $this->belongsTo(Show::class);
    }

    public function seatReservations()
    {
        return $this->hasMany(SeatReservation::class);
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    public function bookingItems()
    {
        return $this->hasMany(BookingItem::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function latestPayment()
    {
        return $this->hasOne(Payment::class)->latest();
    }

    // Auto-generate booking number
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($booking) {
            if (!$booking->booking_number) {
                $booking->booking_number = 'BK-' . strtoupper(Str::random(8));
            }
        });
    }

    // Scope for active bookings
    public function scopeActive($query)
    {
        return $query->whereIn('status', [self::STATUS_PENDING, self::STATUS_CONFIRMED]);
    }

    // Check if booking is expired
    public function isExpired()
    {
        return $this->expires_at && $this->expires_at < now();
    }

    // Get total ticket count
    public function getTotalTicketsAttribute()
    {
        return $this->seatReservations()->count();
    }

    // Calculate fees
    public function getBookingFeesAttribute()
    {
        $fees = [];
        $subtotal = $this->total_amount;

        // Service fee (3% of subtotal, min $2)
        $serviceFee = max($subtotal * 0.03, 2.00);
        $fees['service_fee'] = $serviceFee;

        // Processing fee ($1.50 per ticket)
        $processingFee = $this->total_tickets * 1.50;
        $fees['processing_fee'] = $processingFee;

        $fees['total_fees'] = $serviceFee + $processingFee;
        $fees['grand_total'] = $subtotal + $fees['total_fees'];

        return $fees;
    }
}
