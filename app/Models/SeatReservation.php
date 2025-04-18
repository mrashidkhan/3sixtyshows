<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SeatReservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'show_id', 'seat_id', 'ticket_id', 'booking_id', 'status',
        'reserved_by', 'reserved_until', 'notes'
    ];

    protected $casts = [
        'reserved_until' => 'datetime',
    ];

    // Seat reservation for a specific show
    public function show()
    {
        return $this->belongsTo(Show::class);
    }

    // The seat being reserved
    public function seat()
    {
        return $this->belongsTo(Seat::class);
    }

    // The ticket associated with this reservation (if any)
    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    // The booking associated with this reservation (if any)
    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    // Admin who made the reservation (if any)
    public function reservedBy()
    {
        return $this->belongsTo(User::class, 'reserved_by');
    }
}
