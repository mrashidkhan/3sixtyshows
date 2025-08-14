<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class TicketHold extends Model
{
    use HasFactory;

// In your TicketHold.php model, REPLACE the fillable and casts arrays with:

protected $fillable = [
    'show_id',
    'ticket_type_id',
    'customer_id',
    'session_id',
    'seat_id',
    'general_admission_area_id',
    'quantity',
    'hold_type',
    'expires_at',
    'hold_data'
];

protected $casts = [
    'expires_at' => 'datetime',
    'hold_data' => 'array',
];

    public function show()
    {
        return $this->belongsTo(Show::class);
    }

    public function ticketType()
    {
        return $this->belongsTo(TicketType::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function seat()
    {
        return $this->belongsTo(Seat::class);
    }

    public function generalAdmissionArea()
    {
        return $this->belongsTo(GeneralAdmissionArea::class);
    }

    // Scope for active holds
    public function scopeActive($query)
    {
        return $query->where('expires_at', '>', now());
    }

    // Scope for expired holds
    public function scopeExpired($query)
    {
        return $query->where('expires_at', '<=', now());
    }

    // Check if hold is expired
    public function isExpired()
    {
        return $this->expires_at->isPast();
    }

    // Extend hold time
    public function extend($minutes = 15)
    {
        $this->update(['expires_at' => Carbon::now()->addMinutes($minutes)]);
    }

    // Convert hold to booking
    public function convertToBooking($bookingData)
    {
        // This would contain logic to convert the hold into an actual booking
        // Implementation depends on your booking creation process
    }

    // Clean up expired holds - static method
public static function cleanupExpired()
{
    static::where('expires_at', '<', now())->delete();
}

// Hold tickets for General Admission - static method
public static function holdTickets($showId, $ticketTypeId, $quantity, $sessionId, $minutes = 15)
{
    return static::create([
        'show_id' => $showId,
        'ticket_type_id' => $ticketTypeId,
        'session_id' => $sessionId,
        'quantity' => $quantity,
        'expires_at' => now()->addMinutes($minutes)
    ]);
}

// Hold specific seats for Assigned Seating (for future use) - static method
public static function holdSeat($showId, $ticketTypeId, $seatId, $sessionId, $minutes = 15)
{
    return static::create([
        'show_id' => $showId,
        'ticket_type_id' => $ticketTypeId,
        'seat_id' => $seatId,
        'session_id' => $sessionId,
        'quantity' => 1, // Always 1 for specific seats
        'hold_type' => 'session',
        'expires_at' => now()->addMinutes($minutes)
    ]);
}
}
