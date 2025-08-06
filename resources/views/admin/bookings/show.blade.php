@extends('admin.layout.layout')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Booking Details - {{ $booking->booking_reference ?? '#' . $booking->id }}</h2>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <div class="row">
                        <!-- Booking Information -->
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h4>Booking Information</h4>
                                </div>
                                <div class="card-body">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>Booking Reference:</strong></td>
                                            <td>{{ $booking->booking_reference ?? '#' . $booking->id }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Status:</strong></td>
                                            <td>
                                                <span class="badge badge-{{ $booking->status == 'confirmed' ? 'success' : ($booking->status == 'cancelled' ? 'danger' : 'warning') }}">
                                                    {{ ucfirst($booking->status) }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Total Tickets:</strong></td>
                                            <td>{{ $booking->total_tickets ?? 0 }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Total Amount:</strong></td>
                                            <td>${{ number_format($booking->total_amount, 2) }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Payment Status:</strong></td>
                                            <td>
                                                <span class="badge badge-{{ $booking->payment_status == 'paid' ? 'success' : 'warning' }}">
                                                    {{ ucfirst($booking->payment_status ?? 'pending') }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Booking Date:</strong></td>
                                            <td>{{ $booking->created_at->format('M d, Y H:i') }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Customer Information -->
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h4>Customer Information</h4>
                                </div>
                                <div class="card-body">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>Name:</strong></td>
                                            <td>{{ $booking->customer->name ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Email:</strong></td>
                                            <td>{{ $booking->customer->email ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Phone:</strong></td>
                                            <td>{{ $booking->customer->phone ?? 'N/A' }}</td>
                                        </tr>
                                        @if($booking->user)
                                        <tr>
                                            <td><strong>User Account:</strong></td>
                                            <td>{{ $booking->user->name }} ({{ $booking->user->email }})</td>
                                        </tr>
                                        @endif
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <!-- Show Information -->
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4>Show Information</h4>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <table class="table table-borderless">
                                                <tr>
                                                    <td><strong>Show Title:</strong></td>
                                                    <td>{{ $booking->show->title }}</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Start Date:</strong></td>
                                                    <td>{{ $booking->show->start_date ? $booking->show->start_date->format('M d, Y H:i') : 'N/A' }}</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>End Date:</strong></td>
                                                    <td>{{ $booking->show->end_date ? $booking->show->end_date->format('M d, Y H:i') : 'N/A' }}</td>
                                                </tr>
                                            </table>
                                        </div>
                                        <div class="col-md-6">
                                            @if($booking->show->venue)
                                            <table class="table table-borderless">
                                                <tr>
                                                    <td><strong>Venue:</strong></td>
                                                    <td>{{ $booking->show->venue->name }}</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Address:</strong></td>
                                                    <td>{{ $booking->show->venue->address ?? 'N/A' }}</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Capacity:</strong></td>
                                                    <td>{{ $booking->show->venue->capacity ?? 'N/A' }}</td>
                                                </tr>
                                            </table>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($booking->payment)
                    <div class="row mt-4">
                        <!-- Payment Information -->
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4>Payment Information</h4>
                                </div>
                                <div class="card-body">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>Payment ID:</strong></td>
                                            <td>{{ $booking->payment->id }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Amount:</strong></td>
                                            <td>${{ number_format($booking->payment->amount, 2) }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Payment Method:</strong></td>
                                            <td>{{ ucfirst($booking->payment->payment_method) }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Transaction ID:</strong></td>
                                            <td>{{ $booking->payment->transaction_id ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Payment Status:</strong></td>
                                            <td>
                                                <span class="badge badge-{{ $booking->payment->status == 'completed' ? 'success' : 'warning' }}">
                                                    {{ ucfirst($booking->payment->status) }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Paid At:</strong></td>
                                            <td>{{ $booking->payment->paid_at ? $booking->payment->paid_at->format('M d, Y H:i') : 'N/A' }}</td>
                                        </tr>
                                        @if($booking->payment->refund_amount)
                                        <tr>
                                            <td><strong>Refund Amount:</strong></td>
                                            <td>${{ number_format($booking->payment->refund_amount, 2) }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Refunded At:</strong></td>
                                            <td>{{ $booking->payment->refunded_at ? $booking->payment->refunded_at->format('M d, Y H:i') : 'N/A' }}</td>
                                        </tr>
                                        @endif
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    @if($booking->tickets && $booking->tickets->count() > 0)
                    <div class="row mt-4">
                        <!-- Tickets -->
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4>Tickets</h4>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Ticket #</th>
                                                    <th>Type</th>
                                                    <th>Seat</th>
                                                    <th>Price</th>
                                                    <th>Status</th>
                                                    <th>QR Code</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($booking->tickets as $ticket)
                                                <tr>
                                                    <td>{{ $ticket->ticket_number ?? '#' . $ticket->id }}</td>
                                                    <td>{{ $ticket->ticket_type ?? 'General' }}</td>
                                                    <td>{{ $ticket->seat_number ?? 'General Admission' }}</td>
                                                    <td>${{ number_format($ticket->price, 2) }}</td>
                                                    <td>
                                                        <span class="badge badge-{{ $ticket->status == 'valid' ? 'success' : 'secondary' }}">
                                                            {{ ucfirst($ticket->status ?? 'valid') }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        @if($ticket->qr_code)
                                                            <img src="{{ $ticket->qr_code }}" alt="QR Code" width="50">
                                                        @else
                                                            <span class="text-muted">N/A</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Actions -->
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4>Actions</h4>
                                </div>
                                <div class="card-body">
                                    <a href="{{ route('admin.bookings.index') }}" class="btn btn-secondary">
                                        <i class="fa fa-arrow-left"></i> Back to Bookings
                                    </a>

                                    @if($booking->status !== 'cancelled')
                                        <button class="btn btn-danger ml-2" onclick="updateStatus('cancelled')">
                                            <i class="fa fa-times"></i> Cancel Booking
                                        </button>
                                    @endif

                                    @if($booking->status === 'pending')
                                        <button class="btn btn-success ml-2" onclick="updateStatus('confirmed')">
                                            <i class="fa fa-check"></i> Confirm Booking
                                        </button>
                                    @endif

                                    @if($booking->payment && $booking->payment->status === 'completed')
                                        <button class="btn btn-warning ml-2" onclick="processRefund()">
                                            <i class="fa fa-undo"></i> Process Refund
                                        </button>
                                    @endif

                                    <button class="btn btn-info ml-2" onclick="resendConfirmation()">
                                        <i class="fa fa-envelope"></i> Resend Confirmation
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function updateStatus(status) {
    if (confirm('Are you sure you want to update this booking status?')) {
        $.ajax({
            url: '{{ route("admin.bookings.update-status", $booking) }}',
            method: 'PATCH',
            data: {
                status: status,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    location.reload();
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function() {
                alert('An error occurred');
            }
        });
    }
}

function processRefund() {
    const amount = prompt('Enter refund amount (leave empty for full refund):');
    const reason = prompt('Enter refund reason:');

    if (reason) {
        $.ajax({
            url: '{{ route("admin.bookings.refund", $booking) }}',
            method: 'POST',
            data: {
                amount: amount || null,
                reason: reason,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    alert('Refund processed successfully');
                    location.reload();
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function() {
                alert('An error occurred');
            }
        });
    }
}

function resendConfirmation() {
    $.ajax({
        url: '{{ route("admin.bookings.resend-confirmation", $booking) }}',
        method: 'POST',
        data: {
            _token: '{{ csrf_token() }}'
        },
        success: function(response) {
            if (response.success) {
                alert('Confirmation email sent successfully');
            } else {
                alert('Error: ' + response.message);
            }
        },
        error: function() {
            alert('An error occurred');
        }
    });
}
</script>
@endsection
