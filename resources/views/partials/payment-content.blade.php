<!-- ==========Payment Content========== -->
{{-- File: resources/views/partials/payment-form-content.blade.php --}}

<section class="event-about padding-bottom" style="padding-top:60px;">
    <div class="container">
        <form method="POST" action="{{ route('ga-booking.process-payment', $show->slug) }}" id="payment-form">
            @csrf
            <div class="row justify-content-between">
                <!-- Payment Form Column -->
                <div class="col-lg-7">
                    <div class="event-about-content">
                        <div class="section-header-3 left-style">
                            <h3 class="title">Payment Information</h3>
                            <p>Please select your payment method and complete the secure checkout process.</p>
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
                                <div class="step completed">
                                    <div class="step-number">
                                        <i class="fas fa-check"></i>
                                    </div>
                                    <div class="step-text">Customer Details</div>
                                </div>
                                <div class="step active">
                                    <div class="step-number">3</div>
                                    <div class="step-text">Payment</div>
                                </div>
                            </div>
                        </div>

                        @if ($errors->any())
                            <div class="alert alert-danger" style="border-radius: 10px; border: none; padding: 15px 20px;">
                                <h6><i class="fas fa-exclamation-triangle"></i> Please fix the following errors:</h6>
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        @if(session('error'))
                            <div class="alert alert-danger" style="border-radius: 10px; border: none; padding: 15px 20px;">
                                <i class="fas fa-exclamation-triangle"></i> {{ session('error') }}
                            </div>
                        @endif

                        <!-- Customer Information Display -->
                        <div class="customer-info-display mb-4" style="background: #f8f9fa; padding: 20px; border-radius: 10px; border-left: 4px solid #28a745;">
                            <h6 style="color: #1a1a2e; margin-bottom: 15px;">
                                <i class="fas fa-user-check"></i> Customer Information
                            </h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <p style="color: #1a1a2e;" class="mb-2"><strong>Name:</strong> {{ $customerData['name'] }}</p>
                                    <p style="color: #1a1a2e;" class="mb-0"><strong>Email:</strong> {{ $customerData['email'] }}</p>
                                </div>
                                <div class="col-md-6">
                                    <p style="color: #1a1a2e;" class="mb-2"><strong>Phone:</strong> {{ $customerData['phone'] }}</p>
                                    <p class="mb-0">
                                        <small class="text-muted">
                                            <i class="fas fa-info-circle"></i> Tickets will be sent to your email
                                        </small>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Payment Method Selection -->
                        <div class="payment-methods mb-4">
                            <h5 style="color: #1a1a2e; margin-bottom: 20px;">
                                <i class="fas fa-credit-card"></i> Select Payment Method
                            </h5>
                            <p class="text-muted mb-3" style="font-size: 14px;">
                                <i class="fas fa-shield-alt text-success"></i> All payments are processed securely through PayPal's trusted gateway
                            </p>

                            <!-- Payment Method Cards -->
                            <div class="payment-method-cards">
                                <!-- Credit/Debit Card -->
                                <div class="payment-method-card active" data-method="card"
                                     style="border: 2px solid #007bff; border-radius: 10px; padding: 20px; margin-bottom: 15px; cursor: pointer; background: #f8f9ff; transition: all 0.3s ease;">
                                    <div class="d-flex align-items-center">
                                        <input type="radio" name="payment_method" value="card" checked
                                               style="margin-right: 15px; transform: scale(1.3);">
                                        <div class="flex-grow-1">
                                            <h6 style="margin-bottom: 5px; color: #1a1a2e;">
                                                <i class="fas fa-credit-card me-2"></i> Credit / Debit Card
                                            </h6>
                                            <p class="text-muted mb-0" style="font-size: 13px;">
                                                Pay directly with your card - processed securely by PayPal
                                            </p>
                                        </div>
                                        <div class="payment-icons">
                                            <i class="fab fa-cc-visa" style="font-size: 24px; color: #1a1f71; margin-right: 8px;"></i>
                                            <i class="fab fa-cc-mastercard" style="font-size: 24px; color: #eb001b; margin-right: 8px;"></i>
                                            <i class="fab fa-cc-amex" style="font-size: 24px; color: #006fcf;"></i>
                                        </div>
                                    </div>
                                </div>

                                <!-- PayPal Account -->
                                <div class="payment-method-card" data-method="paypal"
                                     style="border: 2px solid #e9ecef; border-radius: 10px; padding: 20px; margin-bottom: 15px; cursor: pointer; transition: all 0.3s ease;">
                                    <div class="d-flex align-items-center">
                                        <input type="radio" name="payment_method" value="paypal"
                                               style="margin-right: 15px; transform: scale(1.3);">
                                        <div class="flex-grow-1">
                                            <h6 style="margin-bottom: 5px; color: #1a1a2e;">
                                                <i class="fab fa-paypal me-2"></i> PayPal Account
                                            </h6>
                                            <p class="text-muted mb-0" style="font-size: 13px;">
                                                Pay with your PayPal balance, bank account, or saved cards
                                            </p>
                                        </div>
                                        <div class="payment-icons">
                                            <i class="fab fa-paypal" style="font-size: 28px; color: #003087;"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Credit Card Form -->
                        <div class="card-form-section" id="card-form" style="display: block;">
                            <h6 style="color: #1a1a2e; margin-bottom: 20px;">
                                <i class="fas fa-credit-card me-2"></i> Card Information
                            </h6>

                            <div class="card-form-fields">
                                <!-- Card Number -->
                                <div class="form-group mb-3" style="position: relative;">
                                    <label for="card_number" class="form-label" style="font-weight: 600; color: #1a1a2e; margin-bottom: 8px;">
                                        <i class="fas fa-credit-card me-2"></i> Card Number *
                                    </label>
                                    <input type="text"
                                           class="form-control card-input"
                                           id="card_number"
                                           name="card_number"
                                           placeholder="1234 5678 9012 3456"
                                           maxlength="19"
                                           required
                                           style="border-radius: 8px; border: 2px solid #e9ecef; padding: 15px 50px 15px 15px; font-size: 16px; font-family: 'Courier New', monospace;">
                                    <div class="card-type-icon" id="card-type-icon" style="position: absolute; right: 15px; top: 38px; transform: translateY(-50%); display: none;">
                                        <i class="fab fa-cc-visa" style="font-size: 24px;"></i>
                                    </div>
                                </div>

                                <!-- Card Holder Name -->
                                <div class="form-group mb-3">
                                    <label for="card_holder_name" class="form-label" style="font-weight: 600; color: #1a1a2e; margin-bottom: 8px;">
                                        <i class="fas fa-user me-2"></i> Cardholder Name *
                                    </label>
                                    <input type="text"
                                           class="form-control card-input"
                                           id="card_holder_name"
                                           name="card_holder_name"
                                           placeholder="John Doe"
                                           required
                                           style="border-radius: 8px; border: 2px solid #e9ecef; padding: 15px; font-size: 16px; text-transform: uppercase;">
                                </div>

                                <!-- Expiry and CVV Row -->
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="card_expiry" class="form-label" style="font-weight: 600; color: #1a1a2e; margin-bottom: 8px;">
                                                <i class="fas fa-calendar me-2"></i> Expiry Date *
                                            </label>
                                            <input type="text"
                                                   class="form-control card-input"
                                                   id="card_expiry"
                                                   name="card_expiry"
                                                   placeholder="MM/YY"
                                                   maxlength="5"
                                                   required
                                                   style="border-radius: 8px; border: 2px solid #e9ecef; padding: 15px; font-size: 16px; font-family: 'Courier New', monospace;">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="card_cvv" class="form-label" style="font-weight: 600; color: #1a1a2e; margin-bottom: 8px;">
                                                <i class="fas fa-lock me-2"></i> CVV *
                                                <small class="text-muted">(3-4 digits)</small>
                                            </label>
                                            <input type="text"
                                                   class="form-control card-input"
                                                   id="card_cvv"
                                                   name="card_cvv"
                                                   placeholder="123"
                                                   maxlength="4"
                                                   required
                                                   style="border-radius: 8px; border: 2px solid #e9ecef; padding: 15px; font-size: 16px; font-family: 'Courier New', monospace;">
                                        </div>
                                    </div>
                                </div>

                                <!-- Billing Address -->
                                <div class="billing-address mb-3" style="background: #f8f9fa; padding: 20px; border-radius: 8px; border: 1px solid #e9ecef;">
                                    <h6 style="color: #1a1a2e; margin-bottom: 15px;">
                                        <i class="fas fa-map-marker-alt me-2"></i> Billing Address
                                    </h6>

                                    <div class="row">
                                        <div class="col-12 mb-3">
                                            <input type="text"
                                                   class="form-control"
                                                   name="billing_address"
                                                   placeholder="Street Address"
                                                   required
                                                   style="border-radius: 6px; border: 1px solid #dee2e6; padding: 12px; font-size: 14px;">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <input type="text"
                                                   class="form-control"
                                                   name="billing_city"
                                                   placeholder="City"
                                                   required
                                                   style="border-radius: 6px; border: 1px solid #dee2e6; padding: 12px; font-size: 14px;">
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <input type="text"
                                                   class="form-control"
                                                   name="billing_state"
                                                   placeholder="State"
                                                   required
                                                   style="border-radius: 6px; border: 1px solid #dee2e6; padding: 12px; font-size: 14px;">
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <input type="text"
                                                   class="form-control"
                                                   name="billing_zip"
                                                   placeholder="ZIP Code"
                                                   required
                                                   style="border-radius: 6px; border: 1px solid #dee2e6; padding: 12px; font-size: 14px;">
                                        </div>
                                    </div>
                                </div>

                                <!-- Security Features -->
                                <div class="security-features text-center p-3" style="background: #e8f5e8; border-radius: 8px; border: 1px solid #d4edda;">
                                    <div class="row align-items-center">
                                        <div class="col-md-4">
                                            <i class="fas fa-shield-alt text-success" style="font-size: 24px;"></i>
                                            <small class="d-block text-success mt-1">SSL Secured</small>
                                        </div>
                                        <div class="col-md-4">
                                            <i class="fas fa-lock text-success" style="font-size: 24px;"></i>
                                            <small class="d-block text-success mt-1">256-bit Encryption</small>
                                        </div>
                                        <div class="col-md-4">
                                            <i class="fas fa-user-shield text-success" style="font-size: 24px;"></i>
                                            <small class="d-block text-success mt-1">PCI Compliant</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- PayPal Information Section -->
                        <div class="paypal-info-section" id="paypal-info" style="display: none;">
                            <div class="paypal-redirect-info text-center py-4" style="background: #f0f8ff; border-radius: 10px; border: 2px solid #007bff;">
                                <i class="fab fa-paypal fa-4x text-primary mb-3"></i>
                                <h6 class="text-primary mb-3">PayPal Account Payment</h6>
                                <p class="text-muted mb-3">
                                    You will be securely redirected to PayPal to complete your payment.
                                    You can pay with:
                                </p>
                                <div class="row justify-content-center">
                                    <div class="col-md-8">
                                        <div class="paypal-options d-flex justify-content-around align-items-center flex-wrap">
                                            <div class="paypal-option text-center mb-2">
                                                <i class="fas fa-wallet text-primary" style="font-size: 20px;"></i>
                                                <small class="d-block text-muted">PayPal Balance</small>
                                            </div>
                                            <div class="paypal-option text-center mb-2">
                                                <i class="fas fa-university text-primary" style="font-size: 20px;"></i>
                                                <small class="d-block text-muted">Bank Account</small>
                                            </div>
                                            <div class="paypal-option text-center mb-2">
                                                <i class="fas fa-credit-card text-primary" style="font-size: 20px;"></i>
                                                <small class="d-block text-muted">Saved Cards</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-3 p-3" style="background: white; border-radius: 8px; border: 1px solid #dee2e6;">
                                    <small class="text-muted">
                                        <i class="fas fa-info-circle text-info"></i>
                                        No PayPal account? You can still pay with your credit or debit card through PayPal's secure checkout.
                                    </small>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- Booking Summary Column -->
                <div class="col-lg-5">
                    <div class="booking-summary-wrapper" style="position: sticky; top: 120px;">
                        <div class="booking-summary" style="background: #ffffff; border-radius: 10px; padding: 25px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); border: 1px solid #e9ecef;">

                            <!-- Order Summary Header -->
                            <h5 class="mb-3" style="color: #1a1a2e; border-bottom: 2px solid #007bff; padding-bottom: 10px;">
                                <i class="fas fa-receipt"></i> Order Summary
                            </h5>

                            <!-- Show Information -->
                            <div class="show-info mb-3" style="background: #f8f9fa; padding: 15px; border-radius: 8px;">
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
                                        style="padding: 12px; background: #f8f9fa; border-radius: 8px; border-left: 4px solid #007bff;">
                                        <div class="flex-grow-1">
                                            <div style="font-weight: 600; color: #1a1a2e;">
                                                {{ $ticket['ticket_type_name'] }}
                                            </div>
                                            <div class="text-muted" style="font-size: 12px;">
                                                Qty: {{ $ticket['quantity'] }} × ${{ number_format($ticket['unit_price'], 2) }}
                                            </div>
                                        </div>
                                        <div class="text-end">
                                            <strong style="color: #28a745;">
                                                ${{ number_format($ticket['total_price'], 2) }}
                                            </strong>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <!-- Price Breakdown -->
                            <div class="price-breakdown">
                                <hr style="border-top: 1px solid #dee2e6;">
                                <div class="d-flex justify-content-between mb-2">
                                    <span style="color: #1a1a2e; font-weight: 600;">Subtotal:</span>
                                    <span style="color: #28a745; font-weight: 700;">${{ number_format($subtotal, 2) }}</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span style="color: #1a1a2e; font-weight: 600;">Service Fee:</span>
                                    <span style="color: #28a745; font-weight: 700;">${{ number_format($serviceFee, 2) }}</span>
                                </div>
                                <div class="d-flex justify-content-between mb-3">
                                    <span style="color: #1a1a2e; font-weight: 600;">Processing Fee:</span>
                                    <span style="color: #28a745; font-weight: 700;">${{ number_format($processingFee, 2) }}</span>
                                </div>
                                <hr style="border-top: 2px solid #007bff;">
                                <div class="d-flex justify-content-between h5 mb-0">
                                    <span style="color: #1a1a2e; font-weight: 700;">Total:</span>
                                    <span style="color: #28a745; font-weight: 800; font-size: 20px;">
                                        ${{ number_format($grandTotal, 2) }}
                                    </span>
                                </div>
                            </div>

                            <!-- Payment Button -->
                            <button type="submit" class="btn w-100 mt-4" id="payment-btn"
                                style="background: linear-gradient(45deg, #28a745, #20c997);
                                       border: none;
                                       padding: 18px 30px;
                                       border-radius: 25px;
                                       color: white;
                                       font-weight: 700;
                                       text-transform: uppercase;
                                       letter-spacing: 1px;
                                       font-size: 16px;
                                       transition: all 0.3s;">
                                <span id="btn-text-card">
                                    <i class="fas fa-credit-card me-2"></i>
                                    Complete Secure Payment
                                </span>
                                <span id="btn-text-paypal" style="display: none;">
                                    <i class="fab fa-paypal me-2"></i>
                                    Continue to PayPal
                                </span>
                            </button>

                            <!-- Back Link -->
                            <div class="text-center mt-3">
                                <a href="{{ route('ga-booking.customer-details', $show->slug) }}" class="btn btn-link"
                                    style="color: #007bff; text-decoration: none;">
                                    <i class="fas fa-arrow-left"></i> Back to Customer Details
                                </a>
                            </div>

                            <!-- Security Note -->
                            <div class="security-note mt-3 text-center">
                                <small class="text-muted">
                                    <i class="fas fa-shield-alt text-success"></i>
                                    <strong>Powered by PayPal:</strong> Your payment information is secure and encrypted
                                </small>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</section>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Payment method selection elements
    const paymentCards = document.querySelectorAll('.payment-method-card');
    const cardForm = document.getElementById('card-form');
    const paypalInfo = document.getElementById('paypal-info');
    const btnTextCard = document.getElementById('btn-text-card');
    const btnTextPaypal = document.getElementById('btn-text-paypal');
    const paymentBtn = document.getElementById('payment-btn');

    // Card form elements for validation
    const cardInputs = document.querySelectorAll('.card-input');
    const billingInputs = document.querySelectorAll('input[name^="billing_"]');

    // Payment method selection
    paymentCards.forEach(card => {
        card.addEventListener('click', function() {
            const method = this.dataset.method;
            const radio = this.querySelector('input[type="radio"]');

            if (radio) {
                // Update visual state
                paymentCards.forEach(c => {
                    c.classList.remove('active');
                    c.style.borderColor = '#e9ecef';
                    c.style.background = 'white';
                });

                this.classList.add('active');
                this.style.borderColor = '#007bff';
                this.style.background = '#f8f9ff';

                // Select the radio button
                radio.checked = true;

                // Show/hide appropriate sections and update button
                if (method === 'card') {
                    cardForm.style.display = 'block';
                    paypalInfo.style.display = 'none';
                    btnTextCard.style.display = 'inline';
                    btnTextPaypal.style.display = 'none';
                    paymentBtn.style.background = 'linear-gradient(45deg, #28a745, #20c997)';

                    // Make card fields required
                    cardInputs.forEach(input => input.required = true);
                    billingInputs.forEach(input => input.required = true);
                } else {
                    cardForm.style.display = 'none';
                    paypalInfo.style.display = 'block';
                    btnTextCard.style.display = 'none';
                    btnTextPaypal.style.display = 'inline';
                    paymentBtn.style.background = 'linear-gradient(45deg, #0070ba, #003087)';

                    // Remove required from card fields
                    cardInputs.forEach(input => input.required = false);
                    billingInputs.forEach(input => input.required = false);
                }
            }
        });

        // Hover effects
        card.addEventListener('mouseenter', function() {
            if (!this.classList.contains('active')) {
                this.style.borderColor = '#007bff';
                this.style.boxShadow = '0 3px 10px rgba(0, 123, 255, 0.2)';
            }
        });

        card.addEventListener('mouseleave', function() {
            if (!this.classList.contains('active')) {
                this.style.borderColor = '#e9ecef';
                this.style.boxShadow = 'none';
            }
        });
    });

    // Card number formatting and validation
    const cardNumberInput = document.getElementById('card_number');
    const cardTypeIcon = document.getElementById('card-type-icon');

    if (cardNumberInput) {
        cardNumberInput.addEventListener('input', function() {
            let value = this.value.replace(/\s+/g, '').replace(/[^0-9]/gi, '');
            let formattedValue = value.match(/.{1,4}/g)?.join(' ') || value;

            if (formattedValue.length > 19) {
                formattedValue = formattedValue.substring(0, 19);
            }

            this.value = formattedValue;

            // Detect and show card type
            const cardType = detectCardType(value);
            updateCardTypeIcon(cardType);

            // Validation styling
            if (value.length >= 13) {
                this.style.borderColor = '#28a745';
            } else if (value.length > 0) {
                this.style.borderColor = '#ffc107';
            } else {
                this.style.borderColor = '#e9ecef';
            }
        });
    }

    // Card expiry formatting
    const expiryInput = document.getElementById('card_expiry');
    if (expiryInput) {
        expiryInput.addEventListener('input', function() {
            let value = this.value.replace(/\D/g, '');
            if (value.length >= 2) {
                value = value.substring(0, 2) + '/' + value.substring(2, 4);
            }
            this.value = value;

            // Basic validation
            if (value.length === 5) {
                const [month, year] = value.split('/');
                const currentDate = new Date();
                const currentYear = currentDate.getFullYear() % 100;
                const currentMonth = currentDate.getMonth() + 1;

                if (parseInt(month) >= 1 && parseInt(month) <= 12 &&
                    (parseInt(year) > currentYear || (parseInt(year) === currentYear && parseInt(month) >= currentMonth))) {
                    this.style.borderColor = '#28a745';
                } else {
                    this.style.borderColor = '#dc3545';
                }
            }
        });
    }

    // CVV numeric only
    const cvvInput = document.getElementById('card_cvv');
    if (cvvInput) {
        cvvInput.addEventListener('input', function() {
            this.value = this.value.replace(/\D/g, '');
            if (this.value.length >= 3) {
                this.style.borderColor = '#28a745';
            }
        });
    }

    // Cardholder name validation
    const cardHolderInput = document.getElementById('card_holder_name');
    if (cardHolderInput) {
        cardHolderInput.addEventListener('input', function() {
            this.value = this.value.replace(/[^a-zA-Z\s]/g, '');
            if (this.value.length >= 2) {
                this.style.borderColor = '#28a745';
            }
        });
    }

    // Card type detection function
    function detectCardType(number) {
        const patterns = {
            'visa': /^4[0-9]{12}(?:[0-9]{3})?$/,
            'mastercard': /^5[1-5][0-9]{14}$/,
            'amex': /^3[47][0-9]{13}$/,
            'discover': /^6(?:011|5[0-9]{2})[0-9]{12}$/
        };

        for (const [type, pattern] of Object.entries(patterns)) {
            if (pattern.test(number)) {
                return type;
            }
        }
        return null;
    }

    // Update card type icon
    function updateCardTypeIcon(cardType) {
        if (!cardTypeIcon) return;

        const iconMap = {
            'visa': 'fab fa-cc-visa',
            'mastercard': 'fab fa-cc-mastercard',
            'amex': 'fab fa-cc-amex',
            'discover': 'fab fa-cc-discover'
        };

        const colorMap = {
            'visa': '#1a1f71',
            'mastercard': '#eb001b',
            'amex': '#006fcf',
            'discover': '#ff6000'
        };

        if (cardType && iconMap[cardType]) {
            cardTypeIcon.innerHTML = `<i class="${iconMap[cardType]}" style="font-size: 24px; color: ${colorMap[cardType]};"></i>`;
            cardTypeIcon.style.display = 'block';
        } else {
            cardTypeIcon.style.display = 'none';
        }
    }

    // Form submission handling
    const form = document.getElementById('payment-form');
    form.addEventListener('submit', function(e) {
        const selectedMethod = document.querySelector('input[name="payment_method"]:checked').value;

        if (selectedMethod === 'card') {
            // Additional validation for card payments
            let isValid = true;
            const requiredFields = ['card_number', 'card_holder_name', 'card_expiry', 'card_cvv'];

            requiredFields.forEach(fieldName => {
                const field = document.getElementById(fieldName);
                if (field && (!field.value || field.value.trim() === '')) {
                    field.style.borderColor = '#dc3545';
                    isValid = false;
                }
            });

            if (!isValid) {
                e.preventDefault();
                alert('Please fill in all required card information.');
                return false;
            }
        }

        // Show loading state
        paymentBtn.disabled = true;
        if (selectedMethod === 'card') {
            paymentBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Processing Payment...';
        } else {
            paymentBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Redirecting to PayPal...';
        }
    });
});
</script>
@endpush
