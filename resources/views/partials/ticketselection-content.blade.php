<!-- ==========Ticket Selection Content========== -->
<section class="event-about padding-bottom" style="padding-top:60px;">
    <div class="container">
        <form id="ticket-form" method="POST" action="{{ route('ga-booking.select-tickets', $show->slug) }}">
            @csrf
            <div class="row justify-content-between">
                <!-- Ticket Selection Column -->
                <div class="col-lg-7">
                    <div class="event-about-content">
                        <div class="section-header-3 left-style">
                            <h3 class="title">Available Ticket Types</h3>
                            <p>Select your desired tickets and quantities below. All prices are in USD.</p>
                        </div>

                        @if(session('error'))
                            <div class="alert alert-danger">{{ session('error') }}</div>
                        @endif

                        <div id="ticket-types" class="row">
                            @foreach($availableTicketTypes as $index => $ticketType)
                            <div class="col-lg-6 mb-4">
                                <div class="ticket-type-card" data-ticket-type="{{ $ticketType->id }}" style="border: 2px solid #e9ecef; border-radius: 10px; padding: 20px; transition: all 0.3s ease; height: 100%;">
                                    <h5 class="mb-2" style="color: #1a1a2e;">{{ $ticketType->name }}</h5>

                                    @if($ticketType->description)
                                    <p class="text-muted mb-3" style="font-size: 14px;">{{ $ticketType->description }}</p>
                                    @endif

                                    <div class="price-info mb-3">
                                        <span class="h4 text-primary">${{ number_format($ticketType->price, 2) }}</span>
                                        <span class="text-muted ms-2">per ticket</span>
                                    </div>

                                    <div class="availability-info mb-3">
                                        <small class="text-success">
                                            <i class="fas fa-check-circle"></i>
                                            {{ $ticketType->available_quantity }} tickets available
                                        </small>
                                    </div>

                                    <div class="quantity-selector d-flex align-items-center justify-content-between">
                                        <span style="font-weight: 600;">Quantity:</span>
                                        <div class="d-flex align-items-center">
                                            <button type="button" class="quantity-btn btn btn-outline-primary btn-sm" onclick="decreaseQuantity({{ $index }})" style="width: 35px; height: 35px; border-radius: 5px;">
                                                <i class="fas fa-minus"></i>
                                            </button>
                                            <input type="number"
                                                   name="tickets[{{ $index }}][quantity]"
                                                   class="quantity-input form-control mx-2"
                                                   value="0"
                                                   min="0"
                                                   max="{{ $ticketType->available_quantity }}"
                                                   data-price="{{ $ticketType->price }}"
                                                   style="width: 60px; text-align: center;"
                                                   onchange="updateSummary()">
                                            <input type="hidden"
                                                   name="tickets[{{ $index }}][ticket_type_id]"
                                                   value="{{ $ticketType->id }}">
                                            <button type="button" class="quantity-btn btn btn-outline-primary btn-sm" onclick="increaseQuantity({{ $index }})" style="width: 35px; height: 35px; border-radius: 5px;">
                                                <i class="fas fa-plus"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Booking Summary Column -->
                <div class="col-lg-4">
                    <div class="booking-summary-wrapper" style="position: sticky; top: 120px;">
                        <div class="booking-summary" style="background: #f8f9fa; border-radius: 10px; padding: 25px; box-shadow: 0 5px 15px rgba(0,0,0,0.1);">
                            <h5 class="mb-3" style="color: #1a1a2e; border-bottom: 2px solid #007bff; padding-bottom: 10px;">
                                <i class="fas fa-shopping-cart"></i> Booking Summary
                            </h5>

                            <div id="selected-tickets" class="mb-3">
                                <p class="text-muted text-center" style="padding: 20px 0;">
                                    <i class="fas fa-ticket-alt fa-2x mb-2" style="opacity: 0.3;"></i><br>
                                    No tickets selected
                                </p>
                            </div>

                            <div class="price-breakdown" id="price-breakdown" style="display: none;">
                                <hr>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Subtotal:</span>
                                    <span id="subtotal" style="font-weight: 600;">$0.00</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Service Fee (3%):</span>
                                    <span id="service-fee" style="font-weight: 600;">$0.00</span>
                                </div>
                                <div class="d-flex justify-content-between mb-3">
                                    <span>Processing Fee:</span>
                                    <span id="processing-fee" style="font-weight: 600;">$0.00</span>
                                </div>
                                <hr style="border-top: 2px solid #007bff;">
                                <div class="d-flex justify-content-between h5 mb-0">
                                    <span style="color: #1a1a2e;">Total:</span>
                                    <span id="total" style="color: #28a745; font-weight: bold;">$0.00</span>
                                </div>
                            </div>

                            <button type="submit" class="btn w-100 mt-3" id="continue-btn" disabled
                                    style="background: linear-gradient(45deg, #28a745, #20c997);
                                           border: none;
                                           padding: 15px 30px;
                                           border-radius: 25px;
                                           color: white;
                                           font-weight: 600;
                                           text-transform: uppercase;
                                           letter-spacing: 1px;
                                           transition: all 0.3s;">
                                <i class="fas fa-arrow-right"></i> Continue to Details
                            </button>

                            <div class="security-note mt-3 text-center">
                                <small class="text-muted">
                                    <i class="fas fa-shield-alt"></i>
                                    Secure booking powered by 3Sixty Shows
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
.ticket-type-card:hover {
    border-color: #007bff !important;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0,123,255,0.2);
}

.ticket-type-card.selected {
    border-color: #007bff !important;
    background: #f8f9ff !important;
}

.quantity-btn:hover {
    background: #007bff !important;
    color: white !important;
    border-color: #007bff !important;
}

.quantity-btn:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

#continue-btn:hover:not(:disabled) {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(40,167,69,0.4);
}

#continue-btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.alert {
    border-radius: 10px;
    border: none;
    padding: 15px 20px;
}
</style>

<script>
function increaseQuantity(index) {
    const input = document.querySelector(`input[name="tickets[${index}][quantity]"]`);
    const max = parseInt(input.getAttribute('max'));
    const current = parseInt(input.value);

    if (current < max) {
        input.value = current + 1;
        updateSummary();
    }
}

function decreaseQuantity(index) {
    const input = document.querySelector(`input[name="tickets[${index}][quantity]"]`);
    const current = parseInt(input.value);

    if (current > 0) {
        input.value = current - 1;
        updateSummary();
    }
}

function updateSummary() {
    const quantityInputs = document.querySelectorAll('.quantity-input');
    const selectedTicketsDiv = document.getElementById('selected-tickets');
    const priceBreakdownDiv = document.getElementById('price-breakdown');
    const continueBtn = document.getElementById('continue-btn');

    let totalTickets = 0;
    let subtotal = 0;
    let selectedTicketsHtml = '';

    quantityInputs.forEach((input, index) => {
        const quantity = parseInt(input.value) || 0;
        const price = parseFloat(input.getAttribute('data-price'));
        const ticketTypeName = input.closest('.ticket-type-card').querySelector('h5').textContent;

        if (quantity > 0) {
            totalTickets += quantity;
            subtotal += quantity * price;
            selectedTicketsHtml += `
                <div class="d-flex justify-content-between mb-2" style="padding: 8px 0; border-bottom: 1px solid #dee2e6;">
                    <span style="font-size: 14px;">
                        <strong>${ticketTypeName}</strong><br>
                        <small class="text-muted">${quantity} × $${price.toFixed(2)}</small>
                    </span>
                    <span style="font-weight: 600; color: #28a745;">$${(quantity * price).toFixed(2)}</span>
                </div>
            `;
        }

        // Update card appearance
        const card = input.closest('.ticket-type-card');
        if (quantity > 0) {
            card.classList.add('selected');
        } else {
            card.classList.remove('selected');
        }
    });

    if (totalTickets > 0) {
        const serviceFee = Math.max(subtotal * 0.03, 2.00);
        const processingFee = totalTickets * 1.50;
        const total = subtotal + serviceFee + processingFee;

        selectedTicketsDiv.innerHTML = selectedTicketsHtml;
        document.getElementById('subtotal').textContent = `$${subtotal.toFixed(2)}`;
        document.getElementById('service-fee').textContent = `$${serviceFee.toFixed(2)}`;
        document.getElementById('processing-fee').textContent = `$${processingFee.toFixed(2)}`;
        document.getElementById('total').textContent = `$${total.toFixed(2)}`;

        priceBreakdownDiv.style.display = 'block';
        continueBtn.disabled = false;
    } else {
        selectedTicketsDiv.innerHTML = `
            <p class="text-muted text-center" style="padding: 20px 0;">
                <i class="fas fa-ticket-alt fa-2x mb-2" style="opacity: 0.3;"></i><br>
                No tickets selected
            </p>
        `;
        priceBreakdownDiv.style.display = 'none';
        continueBtn.disabled = true;
    }
}

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    updateSummary();
});
</script>
