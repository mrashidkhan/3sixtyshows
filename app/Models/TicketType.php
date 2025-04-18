<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'description', 'show_id', 'price',
        'capacity', 'is_active', 'display_order'
    ];

    protected $casts = [
        'is_active' => 'boolean',
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
}
