{{-- resources/views/booking/checkout.blade.php --}}
@extends('layouts.master')

@section('title', 'Checkout - ' . $show->title)

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-3xl font-bold mb-8">Checkout</h1>

        <div class="grid lg:grid-cols-3 gap-8">
            <!-- Checkout Form -->
            <div class="lg:col-span-2">
                <form action="{{ route('booking.confirm', $show) }}" method="POST" id="checkout-form">
                    @csrf

                    <!-- Customer Information -->
                    <div class="bg-white border rounded-lg p-6 mb-6">
                        <h3 class="text-lg font-semibold mb-4">Customer Information</h3>

                        <div class="grid md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium mb-2">First Name</label>
                                <input type="text" name="first_name" value="{{ auth()->user()->first_name ?? '' }}"
                                       class="w-full border rounded-lg px-3 py-2" required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-2">Last Name</label>
                                <input type="text" name="last_name" value="{{ auth()->user()->last_name ?? '' }}"
                                       class="w-full border rounded-lg px-3 py-2" required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-2">Email</label>
                                <input type="email" name="email" value="{{ auth()->user()->email }}"
                                       class="w-full border rounded-lg px-3 py-2" required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-2">Phone</label>
                                <input type="tel" name="phone" value="{{ auth()->user()->phone ?? '' }}"
                                       class="w-full border rounded-lg px-3 py-2" required>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Method -->
                    <div class="bg-white border rounded-lg p-6 mb-6">
                        <h3 class="text-lg font-semibold mb-4">Payment Method</h3>

                        <div class="space-y-4">
                            <label class="flex items-center">
                                <input type="radio" name="payment_method" value="stripe" checked
                                       class="mr-3">
                                <span>Credit/Debit Card</span>
                            </label>

                            <div id="card-element" class="border rounded-lg p-3">
                                <!-- Stripe card element will be mounted here -->
                            </div>
                        </div>
                    </div>

                    <button type="submit"
                            class="w-full bg-green-600 hover:bg-green-700 text-white font-medium py-3 px-4 rounded-lg transition-colors">
                        Complete Booking
                    </button>
                </form>
            </div>

            <!-- Order Summary -->
            <div class="lg:col-span-1">
                <div class="bg-white border rounded-lg p-6 sticky top-4">
                    <h3 class="text-xl font-semibold mb-4">Order Summary</h3>

                    <div class="mb-4">
                        <h4 class="font-medium">{{ $show->title }}</h4>
                        <p class="text-sm text-gray-600">{{ $show->start_date->format('M d, Y \a\t g:i A') }}</p>
                        <p class="text-sm text-gray-600">{{ $show->venue->name }}</p>
                    </div>

                    <div class="space-y-2 mb-6">
                        @foreach($booking->bookingItems as $item)
                            <div class="flex justify-between text-sm">
                                <span>{{ $item->display_name }} ({{ $item->quantity }}x)</span>
                                <span>${{ number_format($item->total_price, 2) }}</span>
                            </div>
                        @endforeach
                    </div>

                    <hr class="mb-4">

                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span>Subtotal:</span>
                            <span>${{ number_format($booking->total_amount, 2) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Service Fee:</span>
                            <span>${{ number_format($booking->booking_fees['service_fee'], 2) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Processing Fee:</span>
                            <span>${{ number_format($booking->booking_fees['processing_fee'], 2) }}</span>
                        </div>
                        <hr class="my-2">
                        <div class="flex justify-between font-semibold text-lg">
                            <span>Total:</span>
                            <span>${{ number_format($booking->booking_fees['grand_total'], 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
