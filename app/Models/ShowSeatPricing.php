<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShowSeatPricing extends Model
{
    protected $fillable = [
        'show_id', 'seat_category_id', 'price', 'is_active'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function show()
    {
        return $this->belongsTo(Show::class);
    }

    public function seatCategory()
    {
        return $this->belongsTo(SeatCategory::class);
    }
}
