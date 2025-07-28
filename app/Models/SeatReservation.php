<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SeatReservation extends Model
{
    protected $fillable = [
        'show_id', 'seat_id', 'user_id', 'booking_id', 'status',
        'reserved_until', 'price_paid', 'reservation_metadata'
    ];

    protected $casts = [
        'reserved_until' => 'datetime',
        'price_paid' => 'decimal:2',
        'reservation_metadata' => 'array',
    ];

    // Reservation statuses
    const STATUS_TEMPORARY = 'temporary';
    const STATUS_RESERVED = 'reserved';
    const STATUS_SOLD = 'sold';
    const STATUS_BLOCKED = 'blocked';
    const STATUS_CANCELLED = 'cancelled';

    public function show()
    {
        return $this->belongsTo(Show::class);
    }

    public function seat()
    {
        return $this->belongsTo(Seat::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    // Scope for active reservations
    public function scopeActive($query)
    {
        return $query->whereIn('status', [self::STATUS_RESERVED, self::STATUS_SOLD]);
    }

    // Scope for expired temporary reservations
    public function scopeExpired($query)
    {
        return $query->where('status', self::STATUS_TEMPORARY)
                    ->where('reserved_until', '<', now());
    }

    // Check if reservation is expired
    public function isExpired()
    {
        return $this->status === self::STATUS_TEMPORARY &&
               $this->reserved_until < now();
    }
}
