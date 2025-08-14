@extends('layouts.master')

@section('content')
    <!-- Payment Banner Section -->
    @include('partials.payment-banner')

    <!-- Payment Content Section -->
    @include('partials.payment-content')
@endsection

@push('styles')
<style>
/* Payment Page Specific Styles */

/* Step Indicator (reuse from customer details) */
.step-indicator {
    margin: 20px 0 40px 0;
}

.step-indicator .steps-wrapper {
    display: flex;
    align-items: center;
    justify-content: space-between;
    max-width: 450px;
    margin: 0 auto;
    position: relative;
}

.step-indicator .step {
    display: flex;
    align-items: center;
    flex-direction: column;
    text-align: center;
    position: relative;
    z-index: 2;
    flex: 0 0 auto;
}

.step-indicator .step-number {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 14px;
    margin-bottom: 8px;
    transition: all 0.3s ease;
}

.step-indicator .step-text {
    font-size: 12px;
    font-weight: 500;
    white-space: nowrap;
    margin-top: 5px;
    color: #666;
}

.step-indicator .step.completed .step-number {
    background: #28a745;
    color: white;
}

.step-indicator .step.completed .step-text {
    color: #28a745;
    font-weight: 600;
}

.step-indicator .step.active .step-number {
    background: #007bff;
    color: white;
    box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.3);
}

.step-indicator .step.active .step-text {
    color: #007bff;
    font-weight: 600;
}

.step-indicator .steps-wrapper::before {
    content: '';
    position: absolute;
    top: 20px;
    left: 20%;
    right: 20%;
    height: 2px;
    background: #6c757d;
    z-index: 1;
}

.step-indicator .steps-wrapper::after {
    content: '';
    position: absolute;
    top: 20px;
    left: 20%;
    width: 60%;
    height: 2px;
    background: #28a745;
    z-index: 1;
}

/* Payment Method Cards */
.payment-method-card {
    transition: all 0.3s ease;
}

.payment-method-card:hover {
    border-color: #007bff !important;
    background: #f8f9ff !important;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 123, 255, 0.15);
}

.payment-method-card.active {
    border-color: #007bff !important;
    background: #f8f9ff !important;
}

.payment-method-card input[type="radio"] {
    accent-color: #007bff;
}

/* Payment Button Animation */
#payment-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(40, 167, 69, 0.4);
}

/* Timer Animation */
.timer {
    animation: pulse 1s infinite;
}

@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.05); }
    100% { transform: scale(1); }
}

/* Customer Info Display */
.customer-info-display {
    border: 1px solid #e9ecef;
}

.customer-info-display h6 {
    color: #28a745 !important;
}

/* Booking Summary Enhancements */
.booking-summary {
    box-shadow: 0 10px 30px rgba(0,0,0,0.1) !important;
}

.booking-summary h5 {
    color: #007bff !important;
}

/* Responsive Design */
@media (max-width: 768px) {
    .step-indicator .step-text {
        display: none;
    }

    .step-indicator .step-number {
        width: 35px;
        height: 35px;
        font-size: 12px;
    }

    .step-indicator .steps-wrapper {
        max-width: 300px;
    }

    .step-indicator .steps-wrapper::before,
    .step-indicator .steps-wrapper::after {
        top: 17px;
    }
}

/* Security Icons */
.text-success {
    color: #28a745 !important;
}
</style>
@endpush

@push('scripts')
<script>
// Payment Timer (10 minutes for payment)
let timeLeft = 10 * 60; // 10 minutes in seconds
const timerElement = document.getElementById('countdown-timer');

function updateTimer() {
    const minutes = Math.floor(timeLeft / 60);
    const seconds = timeLeft % 60;

    timerElement.textContent = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;

    if (timeLeft <= 60) {
        timerElement.style.background = '#dc3545';
        timerElement.style.animation = 'pulse 1s infinite';
    }

    if (timeLeft <= 0) {
        alert('Your payment session has expired. You will be redirected to start over.');
        window.location.href = '{{ route("ga-booking.tickets", $show->slug) }}';
        return;
    }

    timeLeft--;
}

// Update timer every second
setInterval(updateTimer, 1000);

// Payment Method Selection
document.addEventListener('DOMContentLoaded', function() {
    const paymentCards = document.querySelectorAll('.payment-method-card');
    const radioButtons = document.querySelectorAll('input[name="payment_method"]');

    paymentCards.forEach(card => {
        card.addEventListener('click', function() {
            const radio = this.querySelector('input[type="radio"]');
            if (!radio.disabled) {
                radio.checked = true;

                // Update visual state
                paymentCards.forEach(c => c.classList.remove('active'));
                this.classList.add('active');

                // Show/hide payment forms (for future implementation)
                const method = radio.value;
                console.log('Selected payment method:', method);
            }
        });
    });
});
</script>
@endpush
