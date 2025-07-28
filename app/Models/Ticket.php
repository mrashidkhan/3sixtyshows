<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'show_id', 'customer_id', 'booking_id', 'ticket_type_id', 'seat_id',
        'ticket_number', 'price', 'status', 'seat_number', 'seat_identifier',
        'purchased_date', 'qr_code', 'ticket_mode', 'ticket_metadata'
    ];

    protected $casts = [
        'purchased_date' => 'datetime',
        'price' => 'decimal:2',
        'ticket_metadata' => 'array',
    ];

    // Ticket statuses
    const STATUS_ACTIVE = 'active';
    const STATUS_USED = 'used';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_REFUNDED = 'refunded';

    // Ticket modes
    const MODE_ASSIGNED_SEAT = 'assigned_seat';
    const MODE_GENERAL_ADMISSION = 'general_admission';

    // A ticket belongs to a ticket type
    public function ticketType()
    {
        return $this->belongsTo(TicketType::class);
    }

    // Other relationships remain the same
    public function show()
    {
        return $this->belongsTo(Show::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    // In Ticket model
public function seat()
{
    return $this->belongsTo(Seat::class);
}


// A ticket has one seat reservation
public function seatReservation()
{
    return $this->hasOne(SeatReservation::class);
}

// Auto-generate ticket number
protected static function boot()
{
    parent::boot();

    static::creating(function ($ticket) {
        if (!$ticket->ticket_number) {
            $ticket->ticket_number = 'TK-' . strtoupper(\Illuminate\Support\Str::random(10));
        }
    });
}

// Check if ticket is for assigned seating
public function isAssignedSeating()
{
    return $this->ticket_mode === self::MODE_ASSIGNED_SEAT && $this->seat_id;
}

// Check if ticket is general admission
public function isGeneralAdmission()
{
    return $this->ticket_mode === self::MODE_GENERAL_ADMISSION;
}

// Get seat display name
public function getSeatDisplayAttribute()
{
    if ($this->isAssignedSeating() && $this->seat) {
        return $this->seat->identifier;
    }

    if ($this->seat_identifier) {
        return $this->seat_identifier;
    }

    return 'General Admission';
}

// Scope for active tickets
public function scopeActive($query)
{
    return $query->where('status', self::STATUS_ACTIVE);
}

// Scope for used tickets
public function scopeUsed($query)
{
    return $query->where('status', self::STATUS_USED);
}
}
