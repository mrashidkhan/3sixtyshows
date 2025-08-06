@extends('layouts.master')

@section('content')

<!-- ==========Banner-Section========== -->
<section class="banner-section" style="padding-top:150px; padding-bottom:0px;">
    <div class="banner-bg bg_img bg-fixed" data-background="https://mtpcdn.azurewebsites.net/cdn/whitelabel/3sixtyshows/images/banner/banner01.jpg?mt=20250123"></div>
    <div class="container">
        <div class="banner-content">
            <h1 class="title cd-headline clip" style="font-size:52px;">
                <span class="d-block" style="width:100%;">My Bookings</span>
            </h1>
            <p style="font-size:25px">Track Your Entertainment Journey with 3Sixty Shows</p>
        </div>
    </div>
</section>

<!-- ==========My Bookings Section========== -->
<section class="bookings-section padding-top padding-bottom">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="section-header-3 text-center">
                    <span class="cate">Your Entertainment History</span>
                    <h2 class="title">Your Ticket Collection</h2>
                    <p>Manage all your show bookings, download tickets, and stay updated on your upcoming entertainment experiences.</p>
                </div>
            </div>
        </div>

        <!-- Filter Section -->
        <div class="booking-filter-area">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="filter-wrapper">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <select id="status-filter" class="nice-select">
                                    <option value="">All Bookings</option>
                                    <option value="confirmed">Confirmed</option>
                                    <option value="pending">Pending</option>
                                    <option value="cancelled">Cancelled</option>
                                    <option value="expired">Expired</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <select id="date-filter" class="nice-select">
                                    <option value="">All Dates</option>
                                    <option value="upcoming">Upcoming Shows</option>
                                    <option value="past">Past Shows</option>
                                    <option value="this-month">This Month</option>
                                    <option value="last-month">Last Month</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bookings List -->
        <div class="bookings-wrapper">
            @forelse($bookings as $booking)
                <div class="booking-item">
                    <div class="booking-item-inner">
                        <!-- Booking Header -->
                        <div class="booking-header">
                            <div class="booking-info">
                                <h4 class="title">{{ $booking->show->title }}</h4>
                                <div class="booking-meta">
                                    <div class="meta-item">
                                        <i class="flaticon-calendar"></i>
                                        <span>{{ $booking->show->start_date->format('M d, Y \a\t g:i A') }}</span>
                                    </div>
                                    <div class="meta-item">
                                        <i class="flaticon-location"></i>
                                        <span>{{ $booking->show->venue->name }}</span>
                                    </div>
                                </div>
                                <div class="booking-details">
                                    <span class="booking-number">Booking #{{ $booking->booking_number }}</span>
                                    <span class="booking-date">Booked on {{ $booking->created_at->format('M d, Y') }}</span>
                                </div>
                            </div>

                            <div class="booking-status">
                                <span class="status-badge
                                    @if($booking->status === 'confirmed') status-confirmed
                                    @elseif($booking->status === 'pending') status-pending
                                    @elseif($booking->status === 'cancelled') status-cancelled
                                    @elseif($booking->status === 'expired') status-expired
                                    @else status-default @endif">
                                    {{ ucfirst($booking->status) }}
                                </span>

                                @if($booking->payment_status)
                                    <span class="payment-badge
                                        @if($booking->payment_status === 'completed') payment-completed
                                        @elseif($booking->payment_status === 'pending') payment-pending
                                        @elseif($booking->payment_status === 'failed') payment-failed
                                        @elseif($booking->payment_status === 'refunded') payment-refunded
                                        @else payment-default @endif">
                                        Payment {{ ucfirst($booking->payment_status) }}
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- Booking Details Grid -->
                        <div class="booking-details-grid">
                            <div class="detail-item">
                                <div class="detail-icon">
                                    <i class="flaticon-ticket"></i>
                                </div>
                                <div class="detail-content">
                                    <span class="detail-label">Tickets</span>
                                    <span class="detail-value">{{ $booking->total_tickets }} {{ Str::plural('ticket', $booking->total_tickets) }}</span>
                                </div>
                            </div>

                            <div class="detail-item">
                                <div class="detail-icon">
                                    <i class="flaticon-dollar"></i>
                                </div>
                                <div class="detail-content">
                                    <span class="detail-label">Total Amount</span>
                                    <span class="detail-value">${{ number_format($booking->total_amount, 2) }}</span>
                                </div>
                            </div>

                            <div class="detail-item">
                                <div class="detail-icon">
                                    <i class="flaticon-calendar"></i>
                                </div>
                                <div class="detail-content">
                                    <span class="detail-label">Booking Date</span>
                                    <span class="detail-value">{{ $booking->created_at->format('M d, Y') }}</span>
                                </div>
                            </div>

                            <div class="detail-item">
                                <div class="detail-icon">
                                    <i class="flaticon-clock"></i>
                                </div>
                                <div class="detail-content">
                                    <span class="detail-label">Show Status</span>
                                    <span class="detail-value
                                        @if($booking->show->start_date->isPast()) show-completed
                                        @elseif($booking->show->start_date->isToday()) show-today
                                        @else show-upcoming @endif">
                                        @if($booking->show->start_date->isPast())
                                            Completed
                                        @elseif($booking->show->start_date->isToday())
                                            Today
                                        @else
                                            Upcoming
                                        @endif
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Seat Details -->
                        @if($booking->bookingItems->count() > 0)
                            <div class="seat-details">
                                <h6 class="seat-title">Seat Details:</h6>
                                <div class="seat-list">
                                    @foreach($booking->bookingItems as $item)
                                        <span class="seat-badge">
                                            @if($item->seat)
                                                <i class="flaticon-armchair"></i>
                                                {{ $item->seat_identifier }}
                                            @else
                                                <i class="flaticon-list"></i>
                                                {{ $item->display_name }} ({{ $item->quantity }}x)
                                            @endif
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Action Buttons -->
                        <div class="booking-actions">
                            <a href="{{ route('bookings.show', $booking) }}" class="custom-button">
                                <i class="flaticon-eye"></i> View Details
                            </a>

                            @if($booking->status === 'confirmed')
                                <a href="{{ route('tickets.download', $booking) }}" class="custom-button bg-success">
                                    <i class="flaticon-download"></i> Download Tickets
                                </a>

                                <a href="{{ route('tickets.pdf', $booking) }}" class="custom-button bg-purple">
                                    <i class="flaticon-pdf"></i> Download PDF
                                </a>
                            @endif

                            @if(in_array($booking->status, ['pending', 'confirmed']) && $booking->show->start_date->isFuture())
                                <form action="{{ route('bookings.cancel', $booking) }}" method="POST" class="inline-form"
                                      onsubmit="return confirm('Are you sure you want to cancel this booking? This action cannot be undone.')">
                                    @csrf
                                    <button type="submit" class="custom-button bg-danger">
                                        <i class="flaticon-cross"></i> Cancel Booking
                                    </button>
                                </form>
                            @endif

                            @if($booking->status === 'confirmed')
                                <button onclick="resendConfirmation({{ $booking->id }})" class="custom-button bg-secondary">
                                    <i class="flaticon-email"></i> Resend Email
                                </button>
                            @endif
                        </div>

                        <!-- Payment Information -->
                        @if($booking->payment_reference)
                            <div class="payment-info">
                                <span class="payment-ref">Payment Reference: {{ $booking->payment_reference }}</span>
                                @if($booking->payment_method)
                                    <span class="payment-method">Method: {{ ucfirst($booking->payment_method) }}</span>
                                @endif
                            </div>
                        @endif

                        <!-- Show Alerts -->
                        @if($booking->status === 'confirmed' && $booking->show->start_date->isToday())
                            <div class="show-alert alert-today">
                                <div class="alert-content">
                                    <i class="flaticon-warning"></i>
                                    <p>Show is today! Don't forget to bring your tickets.</p>
                                </div>
                            </div>
                        @elseif($booking->status === 'confirmed' && $booking->show->start_date->isTomorrow())
                            <div class="show-alert alert-tomorrow">
                                <div class="alert-content">
                                    <i class="flaticon-info"></i>
                                    <p>Show is tomorrow! Make sure you have your tickets ready.</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="no-bookings">
                    <div class="no-bookings-content">
                        <div class="no-bookings-icon">
                            <i class="flaticon-ticket"></i>
                        </div>
                        <h3 class="title">No Bookings Found</h3>
                        <p>You haven't made any bookings yet. Start by browsing our amazing shows!</p>
                        <a href="{{ route('activeevents') }}" class="custom-button">
                            <i class="flaticon-right-arrow"></i> Browse Shows
                        </a>
                    </div>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($bookings->hasPages())
            <div class="pagination-area">
                {{ $bookings->links() }}
            </div>
        @endif
    </div>
</section>

@endsection

@push('styles')
<style>
/* Custom styles for bookings page */
.booking-filter-area {
    margin-bottom: 40px;
}

.filter-wrapper {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 10px;
    border: 1px solid #e9ecef;
}

.booking-item {
    background: #fff;
    border-radius: 15px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.1);
    margin-bottom: 30px;
    overflow: hidden;
    border: 1px solid #e9ecef;
    transition: all 0.3s ease;
}

.booking-item:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0,0,0,0.15);
}

.booking-item-inner {
    padding: 30px;
}

.booking-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 25px;
    padding-bottom: 20px;
    border-bottom: 1px solid #e9ecef;
}

.booking-info .title {
    font-size: 24px;
    font-weight: 600;
    color: #1e2328;
    margin-bottom: 10px;
}

.booking-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
    margin-bottom: 10px;
}

.meta-item {
    display: flex;
    align-items: center;
    color: #6c757d;
    font-size: 14px;
}

.meta-item i {
    margin-right: 8px;
    color: #f5407e;
}

.booking-details {
    display: flex;
    gap: 20px;
    font-size: 13px;
    color: #6c757d;
}

.booking-status {
    display: flex;
    flex-direction: column;
    gap: 10px;
    align-items: flex-end;
}

.status-badge, .payment-badge {
    padding: 8px 15px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.status-confirmed { background: #d4edda; color: #155724; }
.status-pending { background: #fff3cd; color: #856404; }
.status-cancelled { background: #f8d7da; color: #721c24; }
.status-expired { background: #e2e3e5; color: #383d41; }
.status-default { background: #cce7ff; color: #004085; }

.payment-completed { background: #d1ecf1; color: #0c5460; }
.payment-pending { background: #ffeaa7; color: #856404; }
.payment-failed { background: #f5c6cb; color: #721c24; }
.payment-refunded { background: #e2e3f3; color: #383d41; }
.payment-default { background: #e9ecef; color: #495057; }

.booking-details-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-bottom: 25px;
}

.detail-item {
    display: flex;
    align-items: center;
    padding: 15px;
    background: #f8f9fa;
    border-radius: 10px;
    border: 1px solid #e9ecef;
}

.detail-icon {
    margin-right: 15px;
    padding: 10px;
    background: #f5407e;
    color: white;
    border-radius: 50%;
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.detail-content {
    display: flex;
    flex-direction: column;
}

.detail-label {
    font-size: 12px;
    color: #6c757d;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.detail-value {
    font-size: 16px;
    font-weight: 600;
    color: #1e2328;
}

.show-completed { color: #6c757d; }
.show-today { color: #f5407e; }
.show-upcoming { color: #28a745; }

.seat-details {
    margin-bottom: 25px;
}

.seat-title {
    font-size: 14px;
    font-weight: 600;
    color: #495057;
    margin-bottom: 10px;
}

.seat-list {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
}

.seat-badge {
    display: inline-flex;
    align-items: center;
    padding: 5px 12px;
    background: #e3f2fd;
    color: #1976d2;
    border-radius: 15px;
    font-size: 12px;
    font-weight: 500;
    border: 1px solid #bbdefb;
}

.seat-badge i {
    margin-right: 5px;
}

.booking-actions {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin-bottom: 20px;
}

.custom-button {
    display: inline-flex;
    align-items: center;
    padding: 10px 20px;
    background: #f5407e;
    color: white;
    text-decoration: none;
    border-radius: 25px;
    font-size: 14px;
    font-weight: 500;
    border: none;
    cursor: pointer;
    transition: all 0.3s ease;
}

.custom-button:hover {
    background: #e91e63;
    transform: translateY(-2px);
    color: white;
}

.custom-button i {
    margin-right: 8px;
}

.bg-success { background: #28a745; }
.bg-success:hover { background: #218838; }
.bg-purple { background: #6f42c1; }
.bg-purple:hover { background: #5a32a3; }
.bg-danger { background: #dc3545; }
.bg-danger:hover { background: #c82333; }
.bg-secondary { background: #6c757d; }
.bg-secondary:hover { background: #545b62; }

.inline-form {
    display: inline-block;
}

.payment-info {
    display: flex;
    gap: 20px;
    font-size: 12px;
    color: #6c757d;
    padding-top: 15px;
    border-top: 1px solid #e9ecef;
    margin-bottom: 15px;
}

.show-alert {
    padding: 15px;
    border-radius: 10px;
    margin-top: 15px;
}

.alert-today {
    background: #fff3cd;
    border: 1px solid #ffeaa7;
}

.alert-tomorrow {
    background: #cce7ff;
    border: 1px solid #b3d9ff;
}

.alert-content {
    display: flex;
    align-items: center;
}

.alert-content i {
    margin-right: 10px;
    font-size: 18px;
}

.alert-today .alert-content i { color: #856404; }
.alert-tomorrow .alert-content i { color: #004085; }

.alert-content p {
    margin: 0;
    font-weight: 500;
}

.alert-today .alert-content p { color: #856404; }
.alert-tomorrow .alert-content p { color: #004085; }

.no-bookings {
    text-align: center;
    padding: 60px 30px;
    background: #fff;
    border-radius: 15px;
    border: 1px solid #e9ecef;
}

.no-bookings-icon {
    font-size: 64px;
    color: #e9ecef;
    margin-bottom: 20px;
}

.no-bookings .title {
    font-size: 24px;
    font-weight: 600;
    color: #495057;
    margin-bottom: 15px;
}

.no-bookings p {
    color: #6c757d;
    margin-bottom: 30px;
}

@media (max-width: 768px) {
    .booking-header {
        flex-direction: column;
        gap: 20px;
    }

    .booking-status {
        align-items: flex-start;
    }

    .booking-details-grid {
        grid-template-columns: 1fr;
    }

    .booking-actions {
        justify-content: center;
    }

    .custom-button {
        flex: 1;
        justify-content: center;
        min-width: 120px;
    }
}
</style>
@endpush

@push('scripts')
<script>
// Filter functionality
document.getElementById('status-filter').addEventListener('change', function() {
    filterBookings();
});

document.getElementById('date-filter').addEventListener('change', function() {
    filterBookings();
});

function filterBookings() {
    const statusFilter = document.getElementById('status-filter').value;
    const dateFilter = document.getElementById('date-filter').value;
    const url = new URL(window.location);

    if (statusFilter) {
        url.searchParams.set('status', statusFilter);
    } else {
        url.searchParams.delete('status');
    }

    if (dateFilter) {
        url.searchParams.set('date', dateFilter);
    } else {
        url.searchParams.delete('date');
    }

    window.location.href = url.toString();
}

// Resend confirmation email
function resendConfirmation(bookingId) {
    if (confirm('Do you want to resend the confirmation email?')) {
        fetch(`/bookings/${bookingId}/resend-confirmation`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json',
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Confirmation email sent successfully!');
            } else {
                alert('Failed to send confirmation email. Please try again.');
            }
        })
        .catch(error => {
            alert('An error occurred. Please try again.');
        });
    }
}
</script>
@endpush
