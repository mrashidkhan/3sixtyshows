<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Customer extends Authenticatable
{
    use HasFactory;

    protected $fillable = [
        'name', 'email', 'phone', 'address', 'city', 'state',
        'country', 'postal_code', 'password'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    // One customer can have many tickets
    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    // One customer can have many bookings
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}
