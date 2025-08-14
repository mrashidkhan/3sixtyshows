<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Models\TicketHold;

class Booking extends Model
{
    protected $fillable = [
    'user_id', 'show_id', 'booking_number', 'total_amount', 'status',  
    'payment_status', 'payment_method', 'payment_reference',
    'booking_date', 'expires_at', 'confirmed_at', 'number_of_tickets',
    'transaction_id', 'ticket_breakdown', 'service_fee', 'processing_fee', 'grand_total'
];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'service_fee' => 'decimal:2',
        'processing_fee' => 'decimal:2',
        'grand_total' => 'decimal:2',
        'ticket_breakdown' => 'array',
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

    public function payment()
    {
        return $this->hasOne(Payment::class)->latest();
    }

    // UPDATE RELATIONSHIPS
public function user()
{
    return $this->belongsTo(User::class);
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
    // For general admission, count from booking items
    if ($this->bookingItems()->exists()) {
        return $this->bookingItems()->sum('quantity');
    }
    // Fallback to seat reservations for assigned seating
    return $this->seatReservations()->count();
}

    // Calculate fees
    // public function getBookingFeesAttribute()
    // {
    //     $fees = [];
    //     $subtotal = $this->total_amount;

    //     // Service fee (3% of subtotal, min $2)
    //     $serviceFee = max($subtotal * 0.03, 2.00);
    //     $fees['service_fee'] = $serviceFee;

    //     // Processing fee ($1.50 per ticket)
    //     $processingFee = $this->total_tickets * 1.50;
    //     $fees['processing_fee'] = $processingFee;

    //     $fees['total_fees'] = $serviceFee + $processingFee;
    //     $fees['grand_total'] = $subtotal + $fees['total_fees'];

    //     return $fees;
    // }

    // Mark booking as expired (NEW)
    public function markAsExpired()
    {
        $this->update([
            'status' => self::STATUS_EXPIRED,
            'payment_status' => self::PAYMENT_FAILED
        ]);

        // Release any ticket holds
        TicketHold::where('show_id', $this->show_id)
            ->where('session_id', session()->getId())
            ->delete();
    }

    // Confirm booking after payment (NEW)
    public function confirmBooking()
    {
        $this->update([
            'status' => self::STATUS_CONFIRMED,
            'payment_status' => self::PAYMENT_COMPLETED,
            'confirmed_at' => now()
        ]);

        $this->generateTickets();

        // Clear holds
        TicketHold::where('show_id', $this->show_id)
            ->where('session_id', session()->getId())
            ->delete();
    }

    // Keep existing getBookingFeesAttribute for backward compatibility
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

    // Add these 2 methods to your existing Booking.php model:



// 2. Add this new method for fee calculation
public function calculateFees()
{
    $subtotal = $this->total_amount;
    $serviceFee = max($subtotal * 0.03, 2.00);
    $processingFee = $this->total_tickets * 1.50;
    $grandTotal = $subtotal + $serviceFee + $processingFee;

    return [
        'service_fee' => $serviceFee,
        'processing_fee' => $processingFee,
        'grand_total' => $grandTotal
    ];
}

// 3. Add this new method for generating tickets
public function generateTickets()
{
    foreach ($this->bookingItems as $item) {
        $item->generateTickets();
    }
}
}
