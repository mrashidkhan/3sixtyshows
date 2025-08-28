<!-- ==========Customer Details Content========== -->
<section class="event-about padding-bottom" style="padding-top:60px;">
    <div class="container">
        <form method="POST" action="{{ route('ga-booking.process-customer-details', $show->slug) }}">
            @csrf
            <div class="row justify-content-between">
                <!-- Customer Form Column -->
                <div class="col-lg-7">
                    <div class="event-about-content">
                        <div class="section-header-3 left-style">
                            <h3 class="title">Customer Information</h3>
                            <p>Please provide your details to complete the booking. Your tickets will be sent to your
                                email.</p>
                        </div>

                        <!-- Step Indicator -->
                        <div class="step-indicator mb-4">
    <div class="steps-wrapper">
        <div class="step completed">
            <div class="step-number">
                <i class="fas fa-check"></i>
            </div>
            <div class="step-text">Select Tickets</div>
        </div>
        <div class="step active">
            <div class="step-number">2</div>
            <div class="step-text">Customer Details</div>
        </div>
        <div class="step pending">
            <div class="step-number">3</div>
            <div class="step-text">Payment</div>
        </div>
    </div>
</div>

                        @if ($errors->any())
                            <div class="alert alert-danger"
                                style="border-radius: 10px; border: none; padding: 15px 20px;">
                                <h6><i class="fas fa-exclamation-triangle"></i> Please fix the following errors:</h6>
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="customer-form">
                            <div class="row">
                                <div class="col-12 mb-3">
                                    <label for="name" class="form-label"
                                        style="font-weight: 600; color: #1a1a2e; margin-bottom: 8px; display: block;">
                                        <i class="fas fa-user me-2"></i> Full Name *
                                    </label>
                                    <input type="text" class="form-control" id="name" name="name"
                                        value="{{ old('name') }}" placeholder="Enter your full name (e.g., John Doe)" required
                                        style="border-radius: 8px;
                          border: 2px solid #e9ecef;
                          padding: 12px 15px;
                          font-size: 16px;
                          width: 100%;
                          transition: all 0.3s;">
                                </div>

                                <div class="col-12 mb-3">
                                    <label for="email" class="form-label"
                                        style="font-weight: 600; color: #1a1a2e; margin-bottom: 8px; display: block;">
                                        <i class="fas fa-envelope me-2"></i> Email Address *
                                    </label>
                                    <input type="email" class="form-control" id="email" name="email"
                                        value="{{ old('email') }}" placeholder="Enter your email address (e.g., john@example.com)" required
                                        style="border-radius: 8px;
                          border: 2px solid #e9ecef;
                          padding: 12px 15px;
                          font-size: 16px;
                          width: 100%;
                          transition: all 0.3s;">
                                    <small class="form-text text-muted mt-2" style="display: block;">
                                        <i class="fas fa-info-circle me-1"></i> Your tickets will be sent to this email
                                        address
                                    </small>
                                </div>

                                <div class="col-12 mb-3">
                                    <label for="phone" class="form-label"
                                        style="font-weight: 600; color: #1a1a2e; margin-bottom: 8px; display: block;">
                                        <i class="fas fa-phone me-2"></i> Phone Number *
                                    </label>
                                    <input type="tel" class="form-control" id="phone" name="phone"
                                        value="{{ old('phone') }}" placeholder="Enter your phone number (e.g., +1 234 567 8900)" required
                                        style="border-radius: 8px;
                          border: 2px solid #e9ecef;
                          padding: 12px 15px;
                          font-size: 16px;
                          width: 100%;
                          transition: all 0.3s;">
                                </div>

                                <div class="col-12 mb-3">
    <div class="form-check d-flex align-items-start" style="padding-left: 0;">
        <input class="form-check-input me-3 mt-1" type="checkbox" id="terms"
               name="terms" value="1" required style="transform: scale(1.3); flex-shrink: 0;">
        <label class="form-check-label" for="terms"
               style="font-size: 14px; line-height: 1.4;">
            I agree to the <a href="#" target="_blank"
                style="color: #007bff; text-decoration: none;">Terms & Conditions</a>
            and <a href="#" target="_blank"
                style="color: #007bff; text-decoration: none;">Privacy Policy</a> *
        </label>
    </div>
</div>

                                <div class="col-12 mb-4">
    <div class="form-check d-flex align-items-start" style="padding-left: 0;">
        <input class="form-check-input me-3 mt-1" type="checkbox" id="newsletter"
               name="newsletter" value="1" style="transform: scale(1.3); flex-shrink: 0;">
        <label class="form-check-label" for="newsletter"
               style="font-size: 14px; line-height: 1.4;">
            Subscribe to our newsletter for upcoming events and exclusive offers
        </label>
    </div>
</div>
                            </div>
                        </div>
                    </div>
                </div>


                <!-- Booking Summary Column -->
                <div class="col-lg-5">
                    <div class="booking-summary-wrapper" style="position: sticky; top: 120px;">
                        <div class="booking-summary"
                            style="background: #f8f9fa; border-radius: 10px; padding: 25px; box-shadow: 0 5px 15px rgba(0,0,0,0.1);">
                            <h5 class="mb-3"
                                style="color: #1a1a2e; border-bottom: 2px solid #007bff; padding-bottom: 10px;">
                                <i class="fas fa-receipt"></i> Booking Summary
                            </h5>

                            <div class="show-info mb-3" style="background: white; padding: 15px; border-radius: 8px;">
                                <h6 style="color: #1a1a2e; margin-bottom: 8px;">{{ $show->title }}</h6>
                                <p class="text-muted mb-1" style="font-size: 14px;">
                                    <i class="fas fa-calendar"></i> {{ $show->start_date->format('l, F j, Y') }}
                                </p>
                                <p class="text-muted mb-0" style="font-size: 14px;">
                                    <i class="fas fa-clock"></i> {{ $show->start_date->format('g:i A') }}
                                </p>
                            </div>

                            <!-- Selected Tickets -->
                            <div class="tickets-section mb-3">
                                @foreach ($bookingData['ticket_breakdown'] as $ticket)
                                    <div class="ticket-item d-flex justify-content-between align-items-center mb-2"
                                        style="padding: 12px; background: white; border-radius: 8px; border-left: 4px solid #007bff;">
                                        <div class="flex-grow-1">
                                            <div style="font-weight: 600; color: #1a1a2e;">
                                                {{ $ticket['ticket_type_name'] }}</div>
                                            <div class="text-muted" style="font-size: 12px;">Qty:
                                                {{ $ticket['quantity'] }} ×
                                                ${{ number_format($ticket['unit_price'], 2) }}</div>
                                        </div>
                                        <div class="text-end">
                                            <strong
                                                style="color: #28a745;">${{ number_format($ticket['total_price'], 2) }}</strong>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <!-- Price Breakdown -->
                            <div class="price-breakdown">
                                <hr style="border-top: 1px solid #dee2e6;">
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Subtotal:</span>
                                    <span style="font-weight: 600;">${{ number_format($subtotal, 2) }}</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Service Fee:</span>
                                    <span style="font-weight: 600;">${{ number_format($serviceFee, 2) }}</span>
                                </div>
                                <div class="d-flex justify-content-between mb-3">
                                    <span>Processing Fee:</span>
                                    <span style="font-weight: 600;">${{ number_format($processingFee, 2) }}</span>
                                </div>
                                <hr style="border-top: 2px solid #007bff;">
                                <div class="d-flex justify-content-between h5 mb-0">
                                    <span style="color: #1a1a2e;">Total:</span>
                                    <span
                                        style="color: #28a745; font-weight: bold;">${{ number_format($grandTotal, 2) }}</span>
                                </div>
                            </div>

                            <button type="submit" class="btn w-100 mt-3"
                                style="background: linear-gradient(45deg, #28a745, #20c997);
                                           border: none;
                                           padding: 15px 30px;
                                           border-radius: 25px;
                                           color: white;
                                           font-weight: 600;
                                           text-transform: uppercase;
                                           letter-spacing: 1px;
                                           transition: all 0.3s;">
                                <i class="fas fa-credit-card"></i> Continue to Payment
                            </button>

                            <div class="text-center mt-3">
                                <a href="{{ route('ga-booking.tickets', $show->slug) }}" class="btn btn-link"
                                    style="color: #007bff; text-decoration: none;">
                                    <i class="fas fa-arrow-left"></i> Back to Ticket Selection
                                </a>
                            </div>

                            <div class="security-note mt-3 text-center">
                                <small class="text-muted">
                                    <i class="fas fa-shield-alt"></i>
                                    Your information is secure and encrypted
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</section>

<style>
    .form-control:focus {
        border-color: #007bff !important;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25) !important;
    }

    .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(40, 167, 69, 0.4);
    }

    .alert {
        border-radius: 10px;
        border: none;
        padding: 15px 20px;
    }

    .step-indicator .step {
        font-size: 14px;
        font-weight: 500;
    }

    .timer {
        animation: pulse 1s infinite;
    }

    @keyframes pulse {
        0% {
            transform: scale(1);
        }

        50% {
            transform: scale(1.05);
        }

        100% {
            transform: scale(1);
        }
    }

    /* DARK THEME FORM FIX */
    .customer-form {
        display: block !important;
        visibility: visible !important;
    }

    .customer-form .form-control {
        display: block !important;
        width: 100% !important;
        visibility: visible !important;
        opacity: 1 !important;
        height: 48px !important;
        border: 2px solid #ddd !important;
        border-radius: 8px !important;
        padding: 12px 15px !important;
        font-size: 16px !important;
        background-color: white !important;
        color: #333 !important;
        /* Dark text on white background */
        box-sizing: border-box !important;
    }

    .customer-form .form-control::placeholder {
        color: #888 !important;
        /* Placeholder color */
        opacity: 1 !important;
    }

    .customer-form .form-label {
        display: block !important;
        visibility: visible !important;
        font-weight: 600 !important;
        color: white !important;
        /* White text for dark background */
        margin-bottom: 8px !important;
        font-size: 16px !important;
        text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.5) !important;
        /* Text shadow for readability */
    }

    .customer-form .form-check {
        display: flex !important;
        align-items: flex-start !important;
        padding: 15px 0 !important;
        margin-bottom: 15px !important;
        visibility: visible !important;
        min-height: 50px !important;
    }

    .customer-form .form-check-input {
        display: block !important;
        visibility: visible !important;
        width: 18px !important;
        height: 18px !important;
        margin-right: 15px !important;
        margin-top: 2px !important;
        flex-shrink: 0 !important;
        background-color: white !important;
        border: 2px solid #ddd !important;
    }

    .customer-form .form-check-label {
        display: block !important;
        visibility: visible !important;
        font-size: 14px !important;
        line-height: 1.5 !important;
        color: white !important;
        /* White text for checkboxes */
        flex: 1 !important;
        text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.5) !important;
        /* Text shadow */
    }

    .customer-form .col-12 {
        flex: 0 0 100% !important;
        max-width: 100% !important;
        padding: 0 15px !important;
        margin-bottom: 20px !important;
    }

    .customer-form .form-text {
        display: block !important;
        visibility: visible !important;
        color: #ccc !important;
        /* Light gray for dark background */
        font-size: 13px !important;
        margin-top: 5px !important;
    }

    /* Links in checkboxes */
    .customer-form .form-check-label a {
        color: #87CEEB !important;
        /* Light blue for links */
        text-decoration: underline !important;
    }

    .customer-form .form-check-label a:hover {
        color: #ADD8E6 !important;
        /* Lighter blue on hover */
    }

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

/* Step states */
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

.step-indicator .step.pending .step-number {
    background: #6c757d;
    color: white;
}

.step-indicator .step.pending .step-text {
    color: #6c757d;
}

/* Connection lines between steps */
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

/* Progress line (shows completed progress) */
.step-indicator .steps-wrapper::after {
    content: '';
    position: absolute;
    top: 20px;
    left: 20%;
    width: 30%;
    height: 2px;
    background: #28a745;
    z-index: 1;
}

/* Responsive design */
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

@media (max-width: 480px) {
    .step-indicator .step-number {
        width: 30px;
        height: 30px;
        font-size: 11px;
    }

    .step-indicator .steps-wrapper {
        max-width: 250px;
    }

    .step-indicator .steps-wrapper::before,
    .step-indicator .steps-wrapper::after {
        top: 15px;
    }
}

/* Checkbox Spacing Fix */
.customer-form .form-check-input {
    display: block !important;
    visibility: visible !important;
    width: 18px !important;
    height: 18px !important;
    margin-right: 20px !important;  /* Increased from 15px to 20px */
    margin-top: 2px !important;
    flex-shrink: 0 !important;
    background-color: white !important;
    border: 2px solid #ddd !important;
}

.customer-form .form-check-label {
    display: block !important;
    visibility: visible !important;
    font-size: 14px !important;
    line-height: 1.6 !important;  /* Increased line height */
    color: white !important;
    flex: 1 !important;
    text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.5) !important;
    padding-left: 5px !important;  /* Extra padding from checkbox */
}

.customer-form .form-check {
    display: flex !important;
    align-items: flex-start !important;
    padding: 20px 0 !important;  /* Increased from 15px to 20px */
    margin-bottom: 20px !important;  /* Increased from 15px to 20px */
    visibility: visible !important;
    min-height: 60px !important;  /* Increased from 50px to 60px */
}

/* Better responsive spacing */
@media (max-width: 768px) {
    .step-indicator .step {
        margin: 0 10px !important;
        font-size: 12px !important;
    }

    .step-indicator .step span {
        display: none !important;  /* Hide text on mobile, show only circles */
    }
}

/* Booking Summary Text Enhancement */
.price-breakdown .d-flex span:first-child {
    color: #1a1a2e !important;  /* Black color for labels */
    font-weight: 600 !important;  /* Bold text */
    font-size: 15px !important;  /* Slightly larger text */
}

.price-breakdown .d-flex span:last-child {
    color: #28a745 !important;  /* Green color for amounts */
    font-weight: 700 !important;  /* Extra bold for amounts */
    font-size: 15px !important;  /* Slightly larger text */
}

/* Total amount special styling */
.price-breakdown .h5 span:first-child {
    color: #1a1a2e !important;  /* Black for "Total:" */
    font-weight: 700 !important;
    font-size: 18px !important;
}

.price-breakdown .h5 span:last-child {
    color: #28a745 !important;  /* Green for total amount */
    font-weight: 800 !important;  /* Extra bold */
    font-size: 20px !important;  /* Larger for emphasis */
}

/* Enhanced contrast for better readability */
.booking-summary {
    background: #ffffff !important;  /* Pure white background */
    border: 1px solid #e9ecef !important;  /* Subtle border */
}

/* Make sure all text in booking summary is dark */
.booking-summary * {
    color: #1a1a2e !important;
}

/* Override for specific green amounts */
.booking-summary .text-end strong,
.booking-summary span[style*="color: #28a745"] {
    color: #28a745 !important;
}

/* Ticket item amounts */
.ticket-item .text-end strong {
    color: #28a745 !important;
    font-weight: 700 !important;
    font-size: 15px !important;
}
</style>

<script>
    // Countdown Timer
    let timeLeft = 15 * 60; // 15 minutes in seconds
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
            alert('Your booking session has expired. You will be redirected to start over.');
            window.location.href = '{{ route('ga-booking.tickets', $show->slug) }}';
            return;
        }

        timeLeft--;
    }

    // Update timer every second
    setInterval(updateTimer, 1000);

    // Form validation
    document.querySelector('form').addEventListener('submit', function(e) {
        const name = document.getElementById('name').value.trim();
        const email = document.getElementById('email').value.trim();
        const phone = document.getElementById('phone').value.trim();
        const terms = document.getElementById('terms').checked;

        if (!name || !email || !phone || !terms) {
            e.preventDefault();
            alert('Please fill in all required fields and accept the terms & conditions.');
            return false;
        }

        // Email validation
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
            e.preventDefault();
            alert('Please enter a valid email address.');
            return false;
        }

        // Phone validation (basic)
        const phoneRegex = /^[\d\s\-\+\(\)]+$/;
        if (!phoneRegex.test(phone) || phone.length < 10) {
            e.preventDefault();
            alert('Please enter a valid phone number.');
            return false;
        }
    });
</script>
