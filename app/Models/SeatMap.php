<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SeatMap extends Model
{
    use HasFactory;

    protected $fillable = [
        'venue_id', 'name', 'description',
        'map_data', 'image', 'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'map_data' => 'array',
    ];

    // A seat map belongs to a venue
    public function venue()
    {
        return $this->belongsTo(Venue::class);
    }

    // You might want to add a method to get seats for this map
    public function seats()
    {
        return $this->hasOneThrough(
            Seat::class,
            Venue::class,
            'id', // Foreign key on venues table
            'venue_id', // Foreign key on seats table
            'venue_id', // Local key on seat_maps table
            'id' // Local key on venues table
        );
    }
}
