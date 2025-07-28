{{-- resources/views/booking/select-seats.blade.php --}}
@extends('layouts.master')

@section('title', 'Select Seats - ' . $show->title)

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-6">
        <h1 class="text-3xl font-bold mb-2">Select Your Seats</h1>
        <p class="text-gray-600">{{ $show->title }} - {{ $show->start_date->format('M d, Y \a\t g:i A') }}</p>
    </div>

    <div class="grid lg:grid-cols-3 gap-8">
        <!-- Seat Map -->
        <div class="lg:col-span-2">
            <div class="bg-white border rounded-lg p-6">
                <div class="mb-4">
                    <div class="bg-gray-800 text-white text-center py-2 rounded mb-4">
                        STAGE
                    </div>
                </div>

                <div id="seat-map" class="seat-map">
                    <!-- Seat map will be loaded here via JavaScript -->
                </div>

                <!-- Legend -->
                <div class="flex justify-center space-x-6 mt-6 text-sm">
                    <div class="flex items-center">
                        <div class="w-4 h-4 bg-green-500 rounded mr-2"></div>
                        Available
                    </div>
                    <div class="flex items-center">
                        <div class="w-4 h-4 bg-blue-500 rounded mr-2"></div>
                        Selected
                    </div>
                    <div class="flex items-center">
                        <div class="w-4 h-4 bg-red-500 rounded mr-2"></div>
                        Taken
                    </div>
                    <div class="flex items-center">
                        <div class="w-4 h-4 bg-gray-400 rounded mr-2"></div>
                        Blocked
                    </div>
                </div>
            </div>
        </div>

        <!-- Booking Summary -->
        <div class="lg:col-span-1">
            <div class="bg-white border rounded-lg p-6 sticky top-4">
                <h3 class="text-xl font-semibold mb-4">Booking Summary</h3>

                <div id="selected-seats" class="mb-6">
                    <p class="text-gray-500">No seats selected</p>
                </div>

                <div id="pricing-breakdown" class="hidden mb-6">
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span>Subtotal:</span>
                            <span id="subtotal">$0.00</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Service Fee:</span>
                            <span id="service-fee">$0.00</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Processing Fee:</span>
                            <span id="processing-fee">$0.00</span>
                        </div>
                        <hr class="my-2">
                        <div class="flex justify-between font-semibold">
                            <span>Total:</span>
                            <span id="total">$0.00</span>
                        </div>
                    </div>
                </div>

                <form id="booking-form" action="{{ route('booking.reserve-seats', $show) }}" method="POST">
                    @csrf
                    <input type="hidden" name="selected_seats" id="selected-seats-input">

                    <button type="submit"
                            id="continue-btn"
                            class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-4 rounded-lg transition-colors disabled:bg-gray-400"
                            disabled>
                        Continue to Checkout
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
let selectedSeats = [];
let seatPrices = @json($show->ticketTypes->pluck('price', 'id'));

// Load seat map
fetch(`{{ route('api.seats.get', $show) }}`)
    .then(response => response.json())
    .then(data => renderSeatMap(data));

function renderSeatMap(seats) {
    const seatMap = document.getElementById('seat-map');
    // Implementation for rendering interactive seat map
    // This would create clickable seat elements based on venue layout
}

function selectSeat(seatId) {
    // Handle seat selection logic
    updateBookingSummary();
}

function updateBookingSummary() {
    // Update the booking summary with selected seats and pricing
    document.getElementById('selected-seats-input').value = JSON.stringify(selectedSeats);
    document.getElementById('continue-btn').disabled = selectedSeats.length === 0;
}
</script>
@endpush
@endsection
