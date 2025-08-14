{{-- File: resources/views/pages/booking-payment.blade.php --}}

@extends('layouts.master')

@section('content')
    <!-- Payment Banner Section -->
    @include('partials.payment-banner')

    <!-- Payment Form Content Section -->
    @include('partials.payment-form-content')
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

/* Credit Card Form Styles */
.card-input {
    transition: all 0.3s ease;
    position: relative;
}

.card-input:focus {
    border-color: #007bff !important;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25) !important;
    transform: translateY(-2px);
}

.card-input.valid {
    border-color: #28a745 !important;
    background-image: linear-gradient(45deg, transparent 40%, rgba(40, 167, 69, 0.1) 50%, transparent 60%);
}

.card-input.invalid {
    border-color: #dc3545 !important;
    background-color: #fff5f5;
}

/* Card Type Icons */
.card-type-icon {
    transition: all 0.3s ease;
}

.card-type-visa { color: #1a1f71; }
.card-type-mastercard { color: #eb001b; }
.card-type-amex { color: #006fcf; }
.card-type-discover { color: #ff6000; }

/* Form Group Positioning */
.form-group {
    position: relative;
}

/* Billing Address Styling */
.billing-address {
    border-left: 4px solid #007bff;
}

/* Security Features */
.security-features {
    animation: fadeInUp 1s ease-out 0.5s both;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Payment Button Animation */
#payment-btn {
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

#payment-btn:hover:not(:disabled) {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(40, 167, 69, 0.4);
}

#payment-btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

#payment-btn.processing {
    pointer-events: none;
}

#payment-btn.processing::after {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 20px;
    height: 20px;
    margin: -10px 0 0 -10px;
    border: 2px solid transparent;
    border-top: 2px solid #fff;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
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

    .card-input {
        font-size: 16px !important; /* Prevent zoom on iOS */
    }
}

/* Security Icons */
.text-success {
    color: #28a745 !important;
}

/* Card Number Formatting */
.card-input[name="card_number"] {
    letter-spacing: 2px;
}

.card-input[name="card_expiry"],
.card-input[name="card_cvv"] {
    letter-spacing: 1px;
}

/* Loading State */
.form-loading {
    pointer-events: none;
    opacity: 0.6;
}

.form-loading .card-input {
    background-color: #f8f9fa;
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

// Credit Card Form Validation and Formatting
document.addEventListener('DOMContentLoaded', function() {
    const paymentCards = document.querySelectorAll('.payment-method-card');
    const radioButtons = document.querySelectorAll('input[name="payment_method"]');
    const cardForm = document.getElementById('card-form');
    const paymentForm = document.getElementById('payment-form');
    const paymentBtn = document.getElementById('payment-btn');

    // Card input elements
    const cardNumber = document.getElementById('card_number');
    const cardExpiry = document.getElementById('card_expiry');
    const cardCvv = document.getElementById('card_cvv');
    const cardHolderName = document.getElementById('card_holder_name');
    const cardTypeIcon = document.getElementById('card-type-icon');

    // Payment Method Selection
    paymentCards.forEach(card => {
        card.addEventListener('click', function() {
            const radio = this.querySelector('input[type="radio"]');
            if (!radio.disabled) {
                radio.checked = true;

                // Update visual state
                paymentCards.forEach(c => c.classList.remove('active'));
                this.classList.add('active');

                // Show/hide card form
                const method = radio.value;
                if (method === 'card') {
                    cardForm.style.display = 'block';
                } else {
                    cardForm.style.display = 'none';
                }
            }
        });
    });

    // Card Number Formatting and Validation
    cardNumber.addEventListener('input', function(e) {
        let value = e.target.value.replace(/\s/g, '').replace(/[^0-9]/gi, '');
        let formattedValue = '';

        // Format with spaces every 4 digits
        for (let i = 0; i < value.length; i++) {
            if (i > 0 && i % 4 === 0) {
                formattedValue += ' ';
            }
            formattedValue += value[i];
        }

        e.target.value = formattedValue;

        // Detect card type and validate
        const cardType = detectCardType(value);
        updateCardTypeIcon(cardType);
        validateCardNumber(value);
    });

    // Card Expiry Formatting
    cardExpiry.addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, '');

        if (value.length >= 2) {
            value = value.substring(0, 2) + '/' + value.substring(2, 4);
        }

        e.target.value = value;
        validateExpiry(value);
    });

    // CVV Validation
    cardCvv.addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, '');
        e.target.value = value;
        validateCvv(value);
    });

    // Card Holder Name Validation
    cardHolderName.addEventListener('input', function(e) {
        e.target.value = e.target.value.toUpperCase();
        validateCardHolderName(e.target.value);
    });

    // Form Submission
    paymentForm.addEventListener('submit', function(e) {
        e.preventDefault();

        if (validateForm()) {
            processPayment();
        }
    });

    // Card Type Detection
    function detectCardType(number) {
        const patterns = {
            visa: /^4/,
            mastercard: /^5[1-5]/,
            amex: /^3[47]/,
            discover: /^6(?:011|5)/
        };

        for (const [type, pattern] of Object.entries(patterns)) {
            if (pattern.test(number)) {
                return type;
            }
        }

        return null;
    }

    // Update Card Type Icon
    function updateCardTypeIcon(cardType) {
        const iconElement = cardTypeIcon.querySelector('i');

        if (cardType) {
            cardTypeIcon.style.display = 'block';
            iconElement.className = `fab fa-cc-${cardType} card-type-${cardType}`;
        } else {
            cardTypeIcon.style.display = 'none';
        }
    }

    // Validation Functions
    function validateCardNumber(number) {
        const isValid = number.length >= 13 && number.length <= 19 && luhnCheck(number);
        updateFieldValidation(cardNumber, isValid);
        return isValid;
    }

    function validateExpiry(expiry) {
        if (expiry.length !== 5) {
            updateFieldValidation(cardExpiry, false);
            return false;
        }

        const [month, year] = expiry.split('/').map(Number);
        const currentDate = new Date();
        const currentYear = currentDate.getFullYear() % 100;
        const currentMonth = currentDate.getMonth() + 1;

        const isValid = month >= 1 && month <= 12 &&
                       (year > currentYear || (year === currentYear && month >= currentMonth));

        updateFieldValidation(cardExpiry, isValid);
        return isValid;
    }

    function validateCvv(cvv) {
        const isValid = cvv.length >= 3 && cvv.length <= 4;
        updateFieldValidation(cardCvv, isValid);
        return isValid;
    }

    function validateCardHolderName(name) {
        const isValid = name.trim().length >= 2;
        updateFieldValidation(cardHolderName, isValid);
        return isValid;
    }

    function updateFieldValidation(field, isValid) {
        field.classList.remove('valid', 'invalid');
        field.classList.add(isValid ? 'valid' : 'invalid');
    }

    // Luhn Algorithm for Card Validation
    function luhnCheck(num) {
        let arr = (num + '')
            .split('')
            .reverse()
            .map(x => parseInt(x));
        let lastDigit = arr.splice(0, 1)[0];
        let sum = arr.reduce((acc, val, i) => {
            return acc + ((i % 2 !== 0) ? val : ((val * 2) % 9) || 9);
        }, 0);
        return (sum + lastDigit) % 10 === 0;
    }

    // Form Validation
    function validateForm() {
        const selectedMethod = document.querySelector('input[name="payment_method"]:checked').value;

        if (selectedMethod === 'card') {
            const cardNumberValid = validateCardNumber(cardNumber.value.replace(/\s/g, ''));
            const expiryValid = validateExpiry(cardExpiry.value);
            const cvvValid = validateCvv(cardCvv.value);
            const nameValid = validateCardHolderName(cardHolderName.value);

            if (!cardNumberValid) {
                showError('Please enter a valid card number');
                cardNumber.focus();
                return false;
            }

            if (!expiryValid) {
                showError('Please enter a valid expiry date');
                cardExpiry.focus();
                return false;
            }

            if (!cvvValid) {
                showError('Please enter a valid CVV');
                cardCvv.focus();
                return false;
            }

            if (!nameValid) {
                showError('Please enter the cardholder name');
                cardHolderName.focus();
                return false;
            }
        }

        return true;
    }

    // Process Payment
    function processPayment() {
        paymentBtn.classList.add('processing');
        paymentBtn.disabled = true;
        paymentBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Processing Payment...';

        // Add loading state to form
        document.querySelector('.card-form-section').classList.add('form-loading');

        // Simulate processing delay (remove in production)
        setTimeout(() => {
            // Submit the form
            paymentForm.submit();
        }, 2000);
    }

    // Show Error Message
    function showError(message) {
        // Remove existing error alerts
        const existingAlerts = document.querySelectorAll('.payment-error-alert');
        existingAlerts.forEach(alert => alert.remove());

        // Create new error alert
        const errorAlert = document.createElement('div');
        errorAlert.className = 'alert alert-danger payment-error-alert';
        errorAlert.style.cssText = 'border-radius: 10px; border: none; padding: 15px 20px; margin-bottom: 20px;';
        errorAlert.innerHTML = `<i class="fas fa-exclamation-triangle me-2"></i> ${message}`;

        // Insert before card form
        cardForm.parentNode.insertBefore(errorAlert, cardForm);

        // Scroll to error
        errorAlert.scrollIntoView({ behavior: 'smooth', block: 'center' });

        // Auto remove after 5 seconds
        setTimeout(() => {
            if (errorAlert.parentNode) {
                errorAlert.remove();
            }
        }, 5000);
    }

    // Real-time Card Validation Feedback
    function setupRealTimeValidation() {
        const inputs = [cardNumber, cardExpiry, cardCvv, cardHolderName];

        inputs.forEach(input => {
            input.addEventListener('blur', function() {
                // Only validate on blur if field has content
                if (this.value.trim()) {
                    switch (this.id) {
                        case 'card_number':
                            validateCardNumber(this.value.replace(/\s/g, ''));
                            break;
                        case 'card_expiry':
                            validateExpiry(this.value);
                            break;
                        case 'card_cvv':
                            validateCvv(this.value);
                            break;
                        case 'card_holder_name':
                            validateCardHolderName(this.value);
                            break;
                    }
                }
            });

            input.addEventListener('focus', function() {
                // Remove validation classes on focus
                this.classList.remove('valid', 'invalid');
            });
        });
    }

    // Initialize real-time validation
    setupRealTimeValidation();

    // Auto-focus next field functionality
    function setupAutoFocus() {
        cardNumber.addEventListener('input', function() {
            if (this.value.replace(/\s/g, '').length === 16) {
                cardExpiry.focus();
            }
        });

        cardExpiry.addEventListener('input', function() {
            if (this.value.length === 5) {
                cardCvv.focus();
            }
        });

        cardCvv.addEventListener('input', function() {
            if (this.value.length === 3) {
                cardHolderName.focus();
            }
        });
    }

    setupAutoFocus();

    // Test Card Numbers (for development)
    function populateTestCard() {
        if (window.location.hostname === 'localhost' || window.location.hostname.includes('test')) {
            // Add test card button for development
            const testBtn = document.createElement('button');
            testBtn.type = 'button';
            testBtn.className = 'btn btn-outline-secondary btn-sm mt-2';
            testBtn.innerHTML = '<i class="fas fa-flask me-1"></i> Use Test Card';
            testBtn.onclick = function() {
                cardNumber.value = '4242 4242 4242 4242';
                cardExpiry.value = '12/25';
                cardCvv.value = '123';
                cardHolderName.value = 'TEST USER';

                // Trigger validation
                cardNumber.dispatchEvent(new Event('input'));
                cardExpiry.dispatchEvent(new Event('input'));
                cardCvv.dispatchEvent(new Event('input'));
                cardHolderName.dispatchEvent(new Event('input'));
            };

            cardForm.appendChild(testBtn);
        }
    }

    populateTestCard();
});
</script>
@endpush
