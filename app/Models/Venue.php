<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Venue extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'slug', 'description', 'address', 'city', 'state',
        'country', 'postal_code', 'latitude', 'longitude',
        'contact_email', 'contact_phone', 'website', 'capacity'
    ];

    public function shows()
    {
        return $this->hasMany(Show::class);
    }

    // In Venue model
public function seats()
{
    return $this->hasMany(Seat::class);
}

public function seatMap()
{
    return $this->hasOne(SeatMap::class);
}

    // Get full address
    public function getFullAddressAttribute()
    {
        $address = $this->address;

        if ($this->city) {
            $address .= ', ' . $this->city;
        }

        if ($this->state) {
            $address .= ', ' . $this->state;
        }

        if ($this->country) {
            $address .= ', ' . $this->country;
        }

        if ($this->postal_code) {
            $address .= ' ' . $this->postal_code;
        }

        return $address;
    }

    // Auto-generate slug from name
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($venue) {
            $venue->slug = $venue->slug ?: Str::slug($venue->name);
        });

        static::updating(function ($venue) {
            if ($venue->isDirty('name') && !$venue->isDirty('slug')) {
                $venue->slug = Str::slug($venue->name);
            }
        });
    }
}
