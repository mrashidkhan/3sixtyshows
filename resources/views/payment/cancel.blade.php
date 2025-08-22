@extends('layouts.app')

@section('title', 'Payment Cancelled')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <!-- Cancel Header -->
        <div class="text-center mb-8">
            <div class="w-20 h-20 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-10 h-10 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.99-.833-2.5 0L5.232 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
            </div>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Payment Cancelled</h1>
            <p class="text-gray-600">Your payment was cancelled. Don't worry, your booking is still reserved for a limited time.</p>
        </div>

        @if(isset($booking))
        <!-- Booking Details Card -->
        <div class="bg-white shadow-lg rounded-lg overflow-hidden mb-6">
            <div class="bg-gradient-to-r from-yellow-500 to-orange-500 px-6 py-4">
                <h2 class="text-xl font-semibold text-white">Reserved Booking</h2>
            </div>

            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Booking Info -->
                    <div>
                        <h3 class="font-semibold text-gray-900 mb-3">Booking Details</h3>
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Booking Number:</span>
                                <span class="font-medium text-gray-900">{{ $booking->booking_number }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Show:</span>
                                <span class="font-medium text-gray-900">{{ $booking->show->title }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Date & Time:</span>
                                <span class="font-medium text-gray-900">
                                    {{ \Carbon\Carbon::parse($booking->show->show_date)->format('M d, Y') }} at
                                    {{ \Carbon\Carbon::parse($booking->show->show_time)->format('g:i A') }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Status Info -->
                    <div>
                        <h3 class="font-semibold text-gray-900 mb-3">Status Information</h3>
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Status:</span>
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                    Payment Pending
                                </span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Reserved Until:</span>
                                <span class="font-medium text-red-600">
                                    {{ $booking->created_at->addMinutes(15)->format('g:i A') }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Total Amount -->
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <div class="bg-gray-50 rounded-lg p-4">
                        <div class="flex justify-between items-center text-lg font-semibold">
                            <span class="text-gray-900">Total Amount:</span>
                            <span class="text-gray-900">${{ number_format($booking->total_amount, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Options Card -->
        <div class="bg-white shadow-lg rounded-lg p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">What would you like to do?</h3>

            <div class="space-y-4">
                <!-- Try Payment Again -->
                @if(isset($booking))
                <div class="border border-gray-200 rounded-lg p-4">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-3 flex-1">
                            <h4 class="text-sm font-medium text-gray-900">Complete Your Payment</h4>
                            <p class="text-sm text-gray-600 mt-1">
                                Your booking is still reserved. Complete the payment to confirm your tickets.
                            </p>
                            <div class="mt-3">
                                <a href="{{ route('ga-booking.payment', $booking->show->slug) }}"
                                   class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 transition-colors duration-200">
                                    Complete Payment
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Start Over -->
                <div class="border border-gray-200 rounded-lg p-4">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                        </div>
                        <div class="ml-3 flex-1">
                            <h4 class="text-sm font-medium text-gray-900">Start New Booking</h4>
                            <p class="text-sm text-gray-600 mt-1">
                                Start a fresh booking process with different ticket selections.
                            </p>
                            <div class="mt-3">
                                <a href="{{ route('activeevents') }}"
                                   class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 transition-colors duration-200">
                                    Browse Shows
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Contact Support -->
                <div class="border border-gray-200 rounded-lg p-4">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192L5.636 18.364M12 2.75a9.25 9.25 0 100 18.5 9.25 9.25 0 000-18.5z"></path>
                            </svg>
                        </div>
                        <div class="ml-3 flex-1">
                            <h4 class="text-sm font-medium text-gray-900">Need Help?</h4>
                            <p class="text-sm text-gray-600 mt-1">
                                Having trouble with payment? Our support team is here to help.
                            </p>
                            <div class="mt-3">
                                <a href="{{ route('contact') }}"
                                   class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 transition-colors duration-200">
                                    Contact Support
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Important Notice -->
        <div class="bg-red-50 border border-red-200 rounded-lg p-4">
            <div class="flex">
                <svg class="w-5 h-5 text-red-400 mt-0.5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div>
                    <h3 class="text-sm font-medium text-red-800">Time Sensitive</h3>
                    <p class="text-sm text-red-700 mt-1">
                        @if(isset($booking))
                            Your booking will expire at {{ $booking->created_at->addMinutes(15)->format('g:i A') }}.
                            Please complete payment before this time to secure your tickets.
                        @else
                            Booking reservations are held for a limited time. Please complete your booking promptly to avoid disappointment.
                        @endif
                    </p>
                </div>
            </div>
        </div>

        <!-- Back to Home -->
        <div class="text-center mt-8">
            <a href="{{ route('index') }}"
               class="inline-flex items-center text-blue-600 hover:text-blue-500 transition-colors duration-200">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to Home
            </a>
        </div>
    </div>
</div>

<script>
    // Auto-refresh page every minute to update countdown
    @if(isset($booking))
    setTimeout(function() {
        location.reload();
    }, 60000);
    @endif
</script>
@endsection
