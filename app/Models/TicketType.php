<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'show_id',
        'price',
        'capacity',
        'is_active',
        'display_order'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'price' => 'decimal:2',
        'capacity' => 'integer',
        'display_order' => 'integer',
    ];

    // A ticket type belongs to a show
    public function show()
    {
        return $this->belongsTo(Show::class);
    }

    // A ticket type has many tickets
    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    // Accessor for formatted price
    public function getFormattedPriceAttribute()
    {
        if ($this->price == 0) {
            return 'Free';
        }
        return '$' . number_format($this->price, 2);
    }

    // Accessor for available tickets count
    public function getAvailableTicketsAttribute()
    {
        if (!$this->capacity) {
            return null; // Unlimited
        }

        $soldTickets = $this->tickets()->count();
        return max(0, $this->capacity - $soldTickets);
    }

    // Accessor for sold tickets count
    public function getSoldTicketsAttribute()
    {
        return $this->tickets()->count();
    }

    // Check if ticket type is sold out
    public function getIsSoldOutAttribute()
    {
        if (!$this->capacity) {
            return false; // Unlimited capacity can't be sold out
        }

        return $this->sold_tickets >= $this->capacity;
    }

    // Scope for active ticket types
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Scope for ordering by display order
    public function scopeOrdered($query)
    {
        return $query->orderBy('display_order', 'asc')->orderBy('name', 'asc');
    }

    // Check if tickets are still available
    public function hasAvailableTickets($quantity = 1)
    {
        if (!$this->capacity) {
            return true; // Unlimited capacity
        }

        return $this->available_tickets >= $quantity;
    }
}
