{{-- File: resources/views/pages/booking-failed.blade.php --}}

@extends('layouts.master')

@section('content')
    <!-- Failed Banner -->
    <section class="banner-section" style="padding-top:150px; padding-bottom:50px;">
        <div class="banner-bg bg_img bg-fixed" data-background="{{ asset('assets/images/banner/banner01.jpg') }}"></div>
        <div class="container">
            <div class="banner-content text-center">
                <div class="failed-icon mb-4">
                    <i class="fas fa-times-circle" style="font-size: 80px; color: #dc3545;"></i>
                </div>
                <h1 class="title" style="font-size:48px; color: #dc3545;">
                    Payment Failed
                </h1>
                <p style="font-size:20px; margin-top: 20px;">
                    Unfortunately, your payment could not be processed. Please try again.
                </p>
                <div class="error-message mt-3">
                    @if(session('error'))
                        <div class="alert alert-danger d-inline-block" style="border-radius: 25px; padding: 12px 24px;">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            {{ session('error') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>

    <!-- Failure Details -->
    <section class="event-about padding-bottom" style="padding-top:60px;">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">

                    <!-- Failure Information Card -->
                    <div class="booking-failed-card" style="background: #ffffff; border-radius: 15px; padding: 40px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); border: 2px solid #dc3545; margin-bottom: 30px;">

                        <!-- Header -->
                        <div class="text-center mb-4">
                            <h3 style="color: #1a1a2e; margin-bottom: 15px;">
                                <i class="fas fa-exclamation-triangle me-2"></i> What Happened?
                            </h3>
                            <p class="text-muted">
                                Your booking could not be completed at this time
                            </p>
                        </div>

                        <!-- Show Information (if available) -->
                        <div class="show-details mb-4" style="background: #f8f9fa; padding: 20px; border-radius: 10px; border-left: 4px solid #007bff;">
                            <h5 style="color: #1a1a2e; margin-bottom: 15px;">{{ $show->title }}</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <p class="mb-2">
                                        <strong><i class="fas fa-calendar me-2"></i> Date:</strong>
                                        {{ $show->start_date->format('l, F j, Y') }}
                                    </p>
                                    <p class="mb-2">
                                        <strong><i class="fas fa-clock me-2"></i> Time:</strong>
                                        {{ $show->start_date->format('g:i A') }}
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    <p class="mb-2">
                                        <strong><i class="fas fa-map-marker-alt me-2"></i> Venue:</strong>
                                        {{ $show->venue->name }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Common Reasons -->
                        <div class="failure-reasons mb-4">
                            <h6 style="color: #1a1a2e; margin-bottom: 20px; border-bottom: 2px solid #dee2e6; padding-bottom: 10px;">
                                <i class="fas fa-question-circle me-2"></i> Common Reasons for Payment Failure
                            </h6>

                            <div class="reasons-list">
                                <div class="reason-item d-flex align-items-start mb-3" style="padding: 15px; background: #fff5f5; border-radius: 8px; border-left: 4px solid #dc3545;">
                                    <i class="fas fa-credit-card text-danger me-3 mt-1"></i>
                                    <div>
                                        <strong>Card Issues:</strong>
                                        <p class="mb-0 text-muted">Incorrect card details, insufficient funds, or expired card</p>
                                    </div>
                                </div>

                                <div class="reason-item d-flex align-items-start mb-3" style="padding: 15px; background: #fff5f5; border-radius: 8px; border-left: 4px solid #dc3545;">
                                    <i class="fas fa-shield-alt text-danger me-3 mt-1"></i>
                                    <div>
                                        <strong>Bank Security:</strong>
                                        <p class="mb-0 text-muted">Transaction blocked by your bank's security system</p>
                                    </div>
                                </div>

                                <div class="reason-item d-flex align-items-start mb-3" style="padding: 15px; background: #fff5f5; border-radius: 8px; border-left: 4px solid #dc3545;">
                                    <i class="fas fa-clock text-danger me-3 mt-1"></i>
                                    <div>
                                        <strong>Session Timeout:</strong>
                                        <p class="mb-0 text-muted">Booking session expired during payment process</p>
                                    </div>
                                </div>

                                <div class="reason-item d-flex align-items-start mb-3" style="padding: 15px; background: #fff5f5; border-radius: 8px; border-left: 4px solid #dc3545;">
                                    <i class="fas fa-wifi text-danger me-3 mt-1"></i>
                                    <div>
                                        <strong>Connection Issues:</strong>
                                        <p class="mb-0 text-muted">Network interruption during payment processing</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="action-buttons text-center mt-4">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <a href="{{ route('ga-booking.tickets', $show->slug) }}" class="btn btn-danger btn-lg w-100"
                                       style="border-radius: 25px; padding: 15px 30px;">
                                        <i class="fas fa-redo me-2"></i> Try Again
                                    </a>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <a href="{{ route('index') }}" class="btn btn-outline-secondary btn-lg w-100"
                                       style="border-radius: 25px; padding: 15px 30px;">
                                        <i class="fas fa-home me-2"></i> Back to Home
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Help Section -->
                        <div class="help-section mt-4 p-4" style="background: #e7f3ff; border: 1px solid #b3d7ff; border-radius: 8px;">
                            <h6 style="color: #0066cc; margin-bottom: 15px;">
                                <i class="fas fa-life-ring me-2"></i> Need Help?
                            </h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <p class="mb-2" style="color: #0066cc;">
                                        <i class="fas fa-phone me-2"></i>
                                        <strong>Call Us:</strong> +1-855-360-SHOWS
                                    </p>
                                    <p class="mb-2" style="color: #0066cc;">
                                        <i class="fas fa-envelope me-2"></i>
                                        <strong>Email:</strong> info@3sixtyshows.com
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    <p class="mb-2" style="color: #0066cc;">
                                        <i class="fas fa-clock me-2"></i>
                                        <strong>Hours:</strong> 9 AM - 8 PM EST
                                    </p>
                                    <p class="mb-0" style="color: #0066cc;">
                                        <i class="fas fa-comments me-2"></i>
                                        <strong>Live Chat:</strong> Available on website
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Tips Section -->
                        <div class="tips-section mt-4 p-3" style="background: #fff3cd; border: 1px solid #ffeaa7; border-radius: 8px;">
                            <h6 style="color: #856404; margin-bottom: 10px;">
                                <i class="fas fa-lightbulb me-2"></i> Tips for Successful Payment
                            </h6>
                            <ul class="mb-0" style="color: #856404; font-size: 14px;">
                                <li>Double-check your card details before submitting</li>
                                <li>Ensure you have sufficient funds in your account</li>
                                <li>Use a stable internet connection</li>
                                <li>Contact your bank if the issue persists</li>
                                <li>Try using a different payment method</li>
                            </ul>
                        </div>

                    </div>

                    <!-- Alternative Contact -->
                    <div class="text-center">
                        <p class="text-muted">
                            Still having trouble? Our support team is here to help!
                            <br>
                            <a href="mailto:info@3sixtyshows.com" style="color: #007bff; font-weight: 600;">
                                Contact Support <i class="fas fa-external-link-alt"></i>
                            </a>
                        </p>
                    </div>

                </div>
            </div>
        </div>
    </section>
@endsection

@push('styles')
<style>
/* Failed page animations */
.failed-icon i {
    animation: shake 0.8s ease-in-out;
}

@keyframes shake {
    0%, 20%, 40%, 60%, 80%, 100% {
        transform: translateX(0);
    }
    10%, 30%, 50%, 70%, 90% {
        transform: translateX(-5px);
    }
}

.booking-failed-card {
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

/* Hover effects */
.btn:hover {
    transform: translateY(-2px);
    transition: all 0.3s ease;
}
</style>
@endpush
