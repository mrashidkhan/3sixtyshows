<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SeatCategory extends Model
{
    protected $fillable = [
        'name', 'description', 'color_code', 'base_price',
        'is_active', 'display_order', 'category_metadata'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'base_price' => 'decimal:2',
        'category_metadata' => 'array',
    ];

    public function seats()
    {
        return $this->hasMany(Seat::class);
    }

    public function showPricing()
    {
        return $this->hasMany(ShowSeatPricing::class);
    }

    // Get price for specific show
    public function getPriceForShow($showId)
    {
        $pricing = $this->showPricing()
            ->where('show_id', $showId)
            ->first();

        return $pricing ? $pricing->price : $this->base_price;
    }
}
