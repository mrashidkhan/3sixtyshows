<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Seat extends Model
{
    use HasFactory;

    protected $fillable = [
        'venue_id', 'seat_category_id', 'section', 'row', 'seat_number',
        'status', 'coordinates_x', 'coordinates_y', 'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'coordinates_x' => 'integer',
        'coordinates_y' => 'integer',
    ];

    // A seat belongs to a venue
    public function venue()
    {
        return $this->belongsTo(Venue::class);
    }

    // A seat belongs to a category
    public function category()
    {
        return $this->belongsTo(SeatCategory::class, 'seat_category_id');
    }

    // A seat has many seat reservations for different shows
    public function reservations()
    {
        return $this->hasMany(SeatReservation::class);
    }

    // Get full seat identifier (e.g., "Orchestra A-12")
    public function getFullSeatIdentifierAttribute()
    {
        return $this->section . ' ' . $this->row . '-' . $this->seat_number;
    }
}
