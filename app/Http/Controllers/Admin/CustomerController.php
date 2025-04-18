<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = Customer::query();

        // Apply search filter
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                  ->orWhere('email', 'like', "%{$searchTerm}%")
                  ->orWhere('phone', 'like', "%{$searchTerm}%");
            });
        }

        // Apply booking status filter
        if ($request->filled('booking_status')) {
            if ($request->booking_status === 'with_bookings') {
                $query->has('bookings');
            } elseif ($request->booking_status === 'no_bookings') {
                $query->doesntHave('bookings');
            }
        }

        // Eager load relationships to avoid N+1 problems
        $query->withCount(['bookings', 'tickets']);

        // Get paginated results
        // $customers = $query->orderBy('created_at', 'desc')->paginate(10);
        $customers = Customer::all();
        return view('admin.customer.index', compact('customers'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.customer.add');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:customers',
            'phone' => 'nullable|string|max:20',
            'password' => 'required|string|min:8|confirmed',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
        ]);

        $customer = Customer::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'address' => $request->address,
            'city' => $request->city,
            'state' => $request->state,
            'country' => $request->country,
            'postal_code' => $request->postal_code,
        ]);

        return redirect()->route('customer.index')
            ->with('success', 'Customer created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function show(Customer $customer)
    {
        // Load relationships
        $customer->load(['bookings.show', 'tickets']);

        return view('customer.show', compact('customer'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function edit(Customer $customer)
    {
        // Load relationships for displaying summary data
        $customer->load(['bookings', 'tickets']);

        return view('customer.edit', compact('customer'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Customer $customer)
    {
        // Create validation rules
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:customers,email,' . $customer->id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
        ];

        // Add password validation only if password is being updated
        if ($request->filled('password')) {
            $rules['password'] = 'required|string|min:8|confirmed';
        }

        // Validate the request
        $validated = $request->validate($rules);

        // Update customer data
        $customer->name = $validated['name'];
        $customer->email = $validated['email'];
        $customer->phone = $validated['phone'];
        $customer->address = $validated['address'];
        $customer->city = $validated['city'];
        $customer->state = $validated['state'];
        $customer->country = $validated['country'];
        $customer->postal_code = $validated['postal_code'];

        // Update password if provided
        if ($request->filled('password')) {
            $customer->password = Hash::make($validated['password']);
        }

        $customer->save();

        return redirect()->route('customer.index')
            ->with('success', 'Customer updated successfully.');
    }

    /**
     * Show delete confirmation page.
     *
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function delete(Customer $customer)
    {
        // Load relationships to show warning about related data
        $customer->load(['bookings', 'tickets']);

        return view('customer.delete', compact('customer'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function destroy(Customer $customer)
    {
        // Delete the customer
        $customer->delete();

        return redirect()->route('customer.index')
            ->with('success', 'Customer deleted successfully.');
    }
}
