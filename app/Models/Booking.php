<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// Booking Model (intermediary between Customer and Show)
class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id', 'show_id', 'booking_number', 'number_of_tickets',
        'total_price', 'status', 'payment_method', 'payment_status',
        'booking_date', 'transaction_id'
    ];

    protected $casts = [
        'booking_date' => 'datetime',
    ];

    // A booking belongs to a customer
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    // A booking belongs to a show
    public function show()
    {
        return $this->belongsTo(Show::class);
    }

    // A booking has many tickets
    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }
}
