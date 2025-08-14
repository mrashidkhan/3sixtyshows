<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookingItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id', 'ticket_type_id', 'seat_id', 'general_admission_area_id',
        'quantity', 'unit_price', 'total_price', 'seat_identifier', 'item_metadata'
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
        'item_metadata' => 'array',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function ticketType()
    {
        return $this->belongsTo(TicketType::class);
    }

    public function seat()
    {
        return $this->belongsTo(Seat::class);
    }

    public function generalAdmissionArea()
    {
        return $this->belongsTo(GeneralAdmissionArea::class);
    }

    // Generate tickets for this booking item
    public function generateTickets($userId = null)
{
    $tickets = [];

    for ($i = 0; $i < $this->quantity; $i++) {
        $ticketData = [
            'show_id' => $this->booking->show_id,
            'user_id' => $userId, // USE user_id instead of customer_id
            'booking_id' => $this->booking_id,
            'ticket_type_id' => $this->ticket_type_id,
            'price' => $this->unit_price,
            'status' => 'active',
            'purchased_date' => now(),
        ];

        if ($this->seat_id) {
            // Assigned seating
            $ticketData['seat_id'] = $this->seat_id;
            $ticketData['seat_identifier'] = $this->seat_identifier;
            $ticketData['ticket_mode'] = 'assigned_seat';
        } else {
            // General admission
            $ticketData['ticket_mode'] = 'general_admission';
            $ticketData['ticket_metadata'] = [
                'area_name' => $this->generalAdmissionArea->name ?? 'General Admission',
                'area_id' => $this->general_admission_area_id
            ];
        }

        $ticket = Ticket::create($ticketData);
        $tickets[] = $ticket;
    }

    return collect($tickets);
}


    // Check if this is for assigned seating
    public function isAssignedSeating()
    {
        return !is_null($this->seat_id);
    }

    // Get display name for the item
    public function getDisplayNameAttribute()
    {
        if ($this->isAssignedSeating()) {
            return $this->seat_identifier;
        }

        return $this->generalAdmissionArea->name ?? 'General Admission';
    }
}
