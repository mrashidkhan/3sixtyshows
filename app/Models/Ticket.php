<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'show_id', 'customer_id', 'booking_id', 'ticket_type_id',
        'ticket_number', 'price', 'status', 'seat_number',
        'purchased_date', 'qr_code'
    ];

    protected $casts = [
        'purchased_date' => 'datetime',
    ];

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
}
