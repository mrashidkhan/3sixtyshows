{{-- resources/views/shows/show.blade.php --}}
@extends('layouts.master')

@section('title', $show->title)

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="grid lg:grid-cols-3 gap-8">
        <!-- Main Content -->
        <div class="lg:col-span-2">
            @if($show->featured_image)
                <img src="{{ asset('storage/' . $show->featured_image) }}"
                     alt="{{ $show->title }}"
                     class="w-full h-64 object-cover rounded-lg mb-6">
            @endif

            <h1 class="text-4xl font-bold mb-4">{{ $show->title }}</h1>

            <div class="flex items-center space-x-6 mb-6 text-gray-600">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path>
                    </svg>
                    {{ $show->start_date->format('l, F j, Y') }}
                </div>

                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                    </svg>
                    {{ $show->start_date->format('g:i A') }}
                </div>

                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path>
                    </svg>
                    {{ $show->venue->name }}
                </div>
            </div>

            <div class="prose max-w-none mb-8">
                {!! nl2br(e($show->description)) !!}
            </div>

            @if($show->additional_info)
                <div class="bg-gray-50 p-6 rounded-lg">
                    <h3 class="text-lg font-semibold mb-4">Additional Information</h3>
                    <ul class="space-y-2">
                        @foreach($show->additional_info as $key => $value)
                            <li><strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong> {{ $value }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>

        <!-- Booking Sidebar -->
        <div class="lg:col-span-1">
            <div class="bg-white border rounded-lg p-6 sticky top-4">
                <h3 class="text-xl font-semibold mb-4">Book Tickets</h3>

                @if($show->sold_out)
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                        This show is sold out.
                    </div>
                @elseif($show->status === 'past')
                    <div class="bg-gray-100 border border-gray-400 text-gray-700 px-4 py-3 rounded mb-4">
                        This show has already ended.
                    </div>
                @else
                    <div class="space-y-4 mb-6">
                        @foreach($show->ticketTypes as $ticketType)
                            <div class="border rounded-lg p-4">
                                <div class="flex justify-between items-center">
                                    <div>
                                        <h4 class="font-medium">{{ $ticketType->name }}</h4>
                                        <p class="text-sm text-gray-600">{{ $ticketType->description }}</p>
                                    </div>
                                    <div class="text-right">
                                        <span class="text-lg font-bold">${{ number_format($ticketType->price, 2) }}</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    @auth
                        <a href="{{ route('booking.select-seats', $show) }}"
                           class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-4 rounded-lg transition-colors text-center block">
                            Select Seats & Book
                        </a>
                    @else
                        <div class="text-center">
                            <p class="text-gray-600 mb-4">Please login to book tickets</p>
                            <a href="{{ route('login') }}"
                               class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-4 rounded-lg transition-colors text-center block">
                                Login to Book
                            </a>
                        </div>
                    @endauth
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
