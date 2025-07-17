<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Add these relationships to your User model

public function bookings()
{
    return $this->hasMany(Booking::class);
}

public function getIsAdminAttribute()
{
    // You should implement your own admin detection here
    // This is just a placeholder
    return $this->email === 'admin@example.com';
}

private function authorizeAdmin()
{
    if (!Auth::check() || !Auth::user()->isAdmin()) {
        return redirect()->route('user_login')->with('error', 'You are not authorized to access this page.');
    }
}
public function isAdmin()
    {
        return $this->role === 'admin';
    }

    /**
     * Get the customer profile associated with the user.
     */
    public function customer()
    {
        return $this->hasOne(Customer::class);
    }

    /**
     * Check if the user has a customer profile.
     */
    public function hasCustomerProfile()
    {
        return $this->customer()->exists();
    }

    /**
     * Create a customer profile if one doesn't exist.
     */
    public function createCustomerProfile(array $attributes = [])
    {
        if (!$this->hasCustomerProfile()) {
            // Pre-fill name and email from user if not provided
            $defaults = [
                'name' => $attributes['name'] ?? $this->name,
                'email' => $attributes['email'] ?? $this->email
            ];

            return $this->customer()->create(array_merge($defaults, $attributes));
        }

        return $this->customer;
    }
}
