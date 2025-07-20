<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShowTicketQuota extends Model
{
    use HasFactory;

    protected $fillable = [
        'show_id', 'ticket_type_id', 'area_id', 'area_type',
        'total_quota', 'sold_count', 'reserved_count',
        'price_override', 'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'price_override' => 'decimal:2',
    ];

    public function show()
    {
        return $this->belongsTo(Show::class);
    }

    public function ticketType()
    {
        return $this->belongsTo(TicketType::class);
    }

    // Polymorphic relationship to either SeatCategory or GeneralAdmissionArea
    public function area()
    {
        if ($this->area_type === 'seat_category') {
            return $this->belongsTo(SeatCategory::class, 'area_id');
        } elseif ($this->area_type === 'general_admission') {
            return $this->belongsTo(GeneralAdmissionArea::class, 'area_id');
        }

        return null;
    }

    // Get the effective price (override or ticket type price)
    public function getEffectivePriceAttribute()
    {
        return $this->price_override ?? $this->ticketType->price;
    }

    // Check if tickets are available
    public function hasAvailability($requestedQuantity = 1)
    {
        return ($this->total_quota - $this->sold_count - $this->reserved_count) >= $requestedQuantity;
    }

    // Reserve tickets (for cart/checkout process)
    public function reserveTickets($quantity)
    {
        if ($this->hasAvailability($quantity)) {
            $this->increment('reserved_count', $quantity);
            return true;
        }
        return false;
    }

    // Convert reserved tickets to sold
    public function confirmReservation($quantity)
    {
        $this->decrement('reserved_count', $quantity);
        $this->increment('sold_count', $quantity);
    }

    // Release reserved tickets
    public function releaseReservation($quantity)
    {
        $this->decrement('reserved_count', $quantity);
    }
}
