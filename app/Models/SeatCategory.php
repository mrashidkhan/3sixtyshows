<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SeatCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'description', 'color_code', 'price', 'is_active', 'display_order'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // A seat category has many seats
    public function seats()
    {
        return $this->hasMany(Seat::class);
    }
}
