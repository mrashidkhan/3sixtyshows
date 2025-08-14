{{-- File: resources/views/pages/booking-success.blade.php --}}

@extends('layouts.master')

@section('content')
    <!-- Success Banner -->
    <section class="banner-section" style="padding-top:150px; padding-bottom:50px;">
        <div class="banner-bg bg_img bg-fixed" data-background="{{ asset('assets/images/banner/banner01.jpg') }}"></div>
        <div class="container">
            <div class="banner-content text-center">
                <div class="success-icon mb-4">
                    <i class="fas fa-check-circle" style="font-size: 80px; color: #28a745;"></i>
                </div>
                <h1 class="title" style="font-size:48px; color: #28a745;">
                    Booking Confirmed!
                </h1>
                <p style="font-size:20px; margin-top: 20px;">
                    Thank you for your purchase. Your tickets have been sent to your email.
                </p>
                <div class="booking-number mt-3">
                    <span class="badge bg-success" style="font-size: 18px; padding: 12px 24px; border-radius: 25px;">
                        Booking #{{ $booking->booking_number }}
                    </span>
                </div>
            </div>
        </div>
    </section>

    <!-- Booking Details -->
    <section class="event-about padding-bottom" style="padding-top:60px;">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">

                    <!-- Booking Summary Card -->
                    <div class="booking-success-card" style="background: #ffffff; border-radius: 15px; padding: 40px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); border: 2px solid #28a745; margin-bottom: 30px;">

                        <!-- Header -->
                        <div class="text-center mb-4">
                            <h3 style="color: #1a1a2e; margin-bottom: 15px;">
                                <i class="fas fa-ticket-alt me-2"></i> Your Tickets
                            </h3>
                            <p class="text-muted">
                                Confirmation details for your booking
                            </p>
                        </div>

                        <!-- Show Information -->
                        <div class="show-details mb-4" style="background: #f8f9fa; padding: 20px; border-radius: 10px; border-left: 4px solid #007bff;">
                            <h5 style="color: #1a1a2e; margin-bottom: 15px;">{{ $show->title }}</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <p style="color: #1a1a2e;" class="mb-2">
                                        <strong><i class="fas fa-calendar me-2"></i> Date:</strong>
                                        {{ $show->start_date->format('l, F j, Y') }}
                                    </p>
                                    <p style="color: #1a1a2e;" class="mb-2">
                                        <strong><i class="fas fa-clock me-2"></i> Time:</strong>
                                        {{ $show->start_date->format('g:i A') }}
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    <p style="color: #1a1a2e;" class="mb-2">
                                        <strong><i class="fas fa-map-marker-alt me-2"></i> Venue:</strong>
                                        {{ $show->venue->name }}
                                    </p>
                                    <p style="color: #1a1a2e;" class="mb-2">
                                        <strong><i class="fas fa-user me-2"></i> Name:</strong>
                                        {{ $booking->user->name }}
                                        {{-- {{ $booking->user->email }} --}}
                                    </p>
                                    <p style="color: #1a1a2e;" class="mb-0">
                                        <strong><i class="fas fa-user me-2"></i> Email:</strong>
                                        {{-- {{ $booking->user->name }} --}}
                                        {{ $booking->user->email }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Ticket Details -->
                        <div class="tickets-section mb-4">
                            <h6 style="color: #1a1a2e; margin-bottom: 20px; border-bottom: 2px solid #dee2e6; padding-bottom: 10px;">
                                <i class="fas fa-list me-2"></i> Ticket Details
                            </h6>

                            @foreach ($booking->bookingItems as $item)
                                <div class="ticket-row d-flex justify-content-between align-items-center mb-3"
                                     style="padding: 15px; background: #f8f9fa; border-radius: 8px; border-left: 4px solid #28a745;">
                                    <div class="ticket-info">
                                        <div style="font-weight: 600; color: #1a1a2e;">
                                            {{ $item->ticketType->name }}
                                        </div>
                                        <div class="text-muted" style="font-size: 14px;">
                                            Quantity: {{ $item->quantity }} × ${{ number_format($item->unit_price, 2) }}
                                        </div>
                                    </div>
                                    <div class="ticket-price">
                                        <strong style="color: #28a745; font-size: 16px;">
                                            ${{ number_format($item->total_price, 2) }}
                                        </strong>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Payment Summary -->
                        <div class="payment-summary" style="background: #f8f9fa; padding: 20px; border-radius: 10px;">
                            <h6 style="color: #1a1a2e; margin-bottom: 15px;">
                                <i class="fas fa-receipt me-2"></i> Payment Summary
                            </h6>

                            <div class="d-flex justify-content-between mb-2">
                                <span style="color: #1a1a2e;">Subtotal:</span>
                                <span style="color: #28a745; font-weight: 600;">${{ number_format($booking->total_amount, 2) }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span style="color: #1a1a2e;">Service Fee:</span>
                                <span style="color: #28a745; font-weight: 600;">${{ number_format($booking->service_fee, 2) }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-3">
                                <span style="color: #1a1a2e;">Processing Fee:</span>
                                <span style="color: #28a745; font-weight: 600;">${{ number_format($booking->processing_fee, 2) }}</span>
                            </div>
                            <hr style="border-top: 2px solid #007bff;">
                            <div class="d-flex justify-content-between h5 mb-0">
                                <span style="color: #1a1a2e; font-weight: 700;">Total Paid:</span>
                                <span style="color: #28a745; font-weight: 800; font-size: 20px;">
                                    ${{ number_format($booking->grand_total, 2) }}
                                </span>
                            </div>

                            <div class="payment-method mt-3 pt-3" style="border-top: 1px solid #dee2e6;">
                                <small class="text-muted">
                                    <i class="fas fa-credit-card me-1"></i>
                                    Payment Method: {{ ucfirst($booking->payment_method) }}
                                    @if($booking->payment_reference)
                                        <br>
                                        <i class="fas fa-hashtag me-1"></i>
                                        Transaction ID: {{ $booking->payment_reference }}
                                    @endif
                                </small>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="action-buttons text-center mt-4">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <button class="btn btn-primary btn-lg w-100" onclick="window.print()"
                                            style="border-radius: 25px; padding: 12px 30px;">
                                        <i class="fas fa-print me-2"></i> Print Tickets
                                    </button>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <a href="{{ route('index') }}" class="btn btn-outline-primary btn-lg w-100"
                                       style="border-radius: 25px; padding: 12px 30px;">
                                        <i class="fas fa-home me-2"></i> Back to Home
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Important Notes -->
                        <div class="important-notes mt-4 p-3" style="background: #fff3cd; border: 1px solid #ffeaa7; border-radius: 8px;">
                            <h6 style="color: #856404; margin-bottom: 10px;">
                                <i class="fas fa-info-circle me-2"></i> Important Information
                            </h6>
                            <ul class="mb-0" style="color: #856404; font-size: 14px;">
                                <li>Please bring a valid photo ID to the venue</li>
                                <li>Tickets have been sent to: <strong>{{ $booking->user->email }}</strong></li>
                                <li>Check your email for detailed tickets with QR codes</li>
                                <li>Arrive at least 30 minutes before show time</li>
                                <li>Contact support if you have any questions</li>
                            </ul>
                        </div>

                    </div>

                    <!-- Support Contact -->
                    <div class="text-center">
                        <p class="text-muted">
                            Need help? Contact us at
                            <a href="mailto:info@3sixtyshows.com" style="color: #007bff;">info@3sixtyshows.com</a>
                            or call <a href="tel:+18553606SHOWS" style="color: #007bff;">+1-855-360-SHOWS</a>
                        </p>
                    </div>

                </div>
            </div>
        </div>
    </section>
@endsection

@push('styles')
<style>
/* Print styles */
@media print {
    .banner-section,
    .action-buttons,
    .navbar,
    .footer {
        display: none !important;
    }

    .booking-success-card {
        box-shadow: none !important;
        border: 2px solid #000 !important;
    }
}

/* Success page animations */
.success-icon i {
    animation: bounceIn 1s ease-out;
}

@keyframes bounceIn {
    0% {
        transform: scale(0.3);
        opacity: 0.3;
    }
    50% {
        transform: scale(1.05);
    }
    70% {
        transform: scale(0.9);
    }
    100% {
        transform: scale(1);
        opacity: 1;
    }
}

.booking-success-card {
    animation: slideInUp 0.8s ease-out;
}

@keyframes slideInUp {
    from {
        transform: translateY(30px);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
    }
}
</style>
@endpush
