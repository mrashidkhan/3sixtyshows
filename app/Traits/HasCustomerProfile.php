<?php

namespace App\Traits;

use App\Models\Customer;
use Illuminate\Support\Facades\Auth;

trait HasCustomerProfile
{
    /**
     * Get the current user's customer profile or create one.
     */
    public function getOrCreateCustomer(array $attributes = [])
    {
        $user = Auth::user();

        if (!$user) {
            return null;
        }

        if ($user->hasCustomerProfile()) {
            return $user->customer;
        }

        return $user->createCustomerProfile($attributes);
    }

    /**
     * Get the currently authenticated customer.
     */
    public function currentCustomer()
    {
        $user = Auth::user();
        return $user ? $user->customer : null;
    }
}
