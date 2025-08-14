<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GeneralAdmissionArea extends Model
{
    use HasFactory;

    protected $fillable = [
        'venue_id', 'name', 'description', 'capacity',
        'default_price', 'color_code', 'display_order', 'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'default_price' => 'decimal:2',
    ];

    public function venue()
    {
        return $this->belongsTo(Venue::class);
    }

    public function ticketHolds()
    {
        return $this->hasMany(TicketHold::class);
    }

    public function bookingItems()
    {
        return $this->hasMany(BookingItem::class);
    }

    // Get available capacity for a specific show
    public function getAvailableCapacityForShow($showId)
    {
        $sold = BookingItem::whereHas('booking', function ($query) use ($showId) {
            $query->where('show_id', $showId)->where('status', 'confirmed');
        })->where('general_admission_area_id', $this->id)->sum('quantity');

        $held = TicketHold::where('show_id', $showId)
            ->where('general_admission_area_id', $this->id)
            ->where('expires_at', '>', now())
            ->sum('quantity');

        return $this->capacity - $sold - $held;
    }
}
