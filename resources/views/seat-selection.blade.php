<!-- frontend/seat-selection.blade.php -->
@extends('layouts.master')

@section('content')
<div class="container">
    <h1>Select Your Seats</h1>
    <h2>{{ $show->title }} - {{ $show->start_date->format('F j, Y g:i A') }}</h2>
    <h3>{{ $show->venue->name }}</h3>

    <div class="row">
        <div class="col-md-8">
            <!-- Seat Map Display -->
            <div class="seat-map-container">
                <!-- Stage/Screen Visualization -->
                <div class="stage">STAGE</div>

                <!-- Seat Grid -->
                <div class="seats-container">
                    @foreach($seats as $seat)
                    <div class="seat {{ in_array($seat->id, $reservedSeats) ? 'reserved' : 'available' }}"
                         data-id="{{ $seat->id }}"
                         data-category="{{ $seat->category->id ?? 0 }}"
                         data-price="{{ $seat->category->price ?? 0 }}"
                         data-seat="{{ $seat->fullSeatIdentifier }}"
                         style="left: {{ $seat->coordinates_x }}px; top: {{ $seat->coordinates_y }}px;
                                {{ !in_array($seat->id, $reservedSeats) ? 'background-color: '.$seat->category->color_code : '' }}">
                        {{ $seat->row }}-{{ $seat->seat_number }}
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="category-legend mt-4">
                @foreach($categories as $category)
                <div class="category-item">
                    <span class="color-box" style="background-color: {{ $category->color_code }}"></span>
                    <span>{{ $category->name }} - ${{ number_format($category->price, 2) }}</span>
                </div>
                @endforeach
                <div class="category-item">
                    <span class="color-box reserved-box"></span>
                    <span>Reserved/Not Available</span>
                </div>
                <div class="category-item">
                    <span class="color-box selected-box"></span>
                    <span>Your Selection</span>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <!-- Selected Seats and Checkout -->
            <div class="card">
                <div class="card-header">
                    <h3>Your Selected Seats</h3>
                </div>
                <div class="card-body">
                    <div id="selected-seats-list">
                        <p class="text-muted">Click on seats to select them</p>
                    </div>

                    <div class="price-summary mt-3" id="price-summary" style="display: none;">
                        <h4>Summary</h4>
                        <div class="d-flex justify-content-between">
                            <span>Subtotal:</span>
                            <span id="subtotal-price">$0.00</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>Service Fee:</span>
                            <span id="service-fee">$0.00</span>
                        </div>
                        <div class="d-flex justify-content-between font-weight-bold mt-2">
                            <span>Total:</span>
                            <span id="total-price">$0.00</span>
                        </div>
                    </div>

                    <div class="mt-4">
                        <button id="reserve-seats" class="btn btn-primary btn-block" disabled>
                            Continue to Checkout
                        </button>
                        <p class="text-muted mt-2 small">Selected seats will be held for 15 minutes.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // JavaScript for seat selection, temporary reservation, and checkout flow
</script>
@endsection
