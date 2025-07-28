<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Seat extends Model
{
    protected $fillable = [
        'venue_id', 'seat_category_id', 'section', 'row', 'seat_number',
        'coordinates_x', 'coordinates_y', 'status', 'is_active', 'is_accessible',
        'seat_metadata'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_accessible' => 'boolean',
        'coordinates_x' => 'float',
        'coordinates_y' => 'float',
        'seat_metadata' => 'array',
    ];

    // Seat statuses
    const STATUS_AVAILABLE = 'available';
    const STATUS_RESERVED = 'reserved';
    const STATUS_SOLD = 'sold';
    const STATUS_BLOCKED = 'blocked';

    public function venue()
    {
        return $this->belongsTo(Venue::class);
    }

    public function category()
    {
        return $this->belongsTo(SeatCategory::class, 'seat_category_id');
    }

    public function reservations()
    {
        return $this->hasMany(SeatReservation::class);
    }

    // Get seat identifier
    public function getIdentifierAttribute()
    {
        return $this->section . '-' . $this->row . '-' . $this->seat_number;
    }

    // Check if seat is available for specific show
    public function isAvailableForShow($showId)
    {
        if (!$this->is_active) return false;

        return !$this->reservations()
            ->where('show_id', $showId)
            ->whereIn('status', ['reserved', 'sold', 'blocked'])
            ->exists();
    }

    // Get seat status for specific show
    public function getStatusForShow($showId)
    {
        $reservation = $this->reservations()
            ->where('show_id', $showId)
            ->first();

        return $reservation ? $reservation->status : self::STATUS_AVAILABLE;
    }
}
