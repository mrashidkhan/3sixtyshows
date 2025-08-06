@extends('admin.layout.layout')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2>All Bookings</h2>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <!-- Filter Form -->
                    <form method="GET" action="{{ route('admin.bookings.index') }}" class="form-inline mb-3">
                        <div class="form-group mx-sm-3 mb-2">
                            <label for="status" class="sr-only">Status</label>
                            <select name="status" id="status" class="form-control">
                                <option value="">All Status</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                            </select>
                        </div>

                        <div class="form-group mx-sm-3 mb-2">
                            <label for="show_id" class="sr-only">Show</label>
                            <select name="show_id" id="show_id" class="form-control">
                                <option value="">All Shows</option>
                                @foreach($shows as $show)
                                    <option value="{{ $show->id }}" {{ request('show_id') == $show->id ? 'selected' : '' }}>
                                        {{ $show->title }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group mx-sm-3 mb-2">
                            <label for="search" class="sr-only">Search</label>
                            <input type="text" name="search" id="search" class="form-control" placeholder="Search..." value="{{ request('search') }}">
                        </div>

                        <button type="submit" class="btn btn-primary mb-2">Filter</button>
                        <a href="{{ route('admin.bookings.index') }}" class="btn btn-secondary mb-2 ml-2">Clear</a>
                    </form>

                    <!-- Export Buttons -->
                    <div class="mb-3">
                        <a href="{{ route('admin.bookings.export.csv') }}?{{ http_build_query(request()->all()) }}" class="btn btn-success">
                            <i class="fa fa-download"></i> Export CSV
                        </a>
                        <a href="{{ route('admin.bookings.export.excel') }}?{{ http_build_query(request()->all()) }}" class="btn btn-info">
                            <i class="fa fa-download"></i> Export Excel
                        </a>
                    </div>

                    <!-- Bookings Table -->
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>Booking #</th>
                                    <th>Customer</th>
                                    <th>Show</th>
                                    <th>Tickets</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Payment</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($bookings as $booking)
                                    <tr>
                                        <td>{{ $booking->booking_reference ?? '#' . $booking->id }}</td>
                                        <td>
                                            <strong>{{ $booking->customer->name ?? 'N/A' }}</strong><br>
                                            <small>{{ $booking->customer->email ?? 'N/A' }}</small>
                                        </td>
                                        <td>
                                            <strong>{{ $booking->show->title }}</strong><br>
                                            <small>{{ $booking->show->start_date ? $booking->show->start_date->format('M d, Y H:i') : 'N/A' }}</small>
                                        </td>
                                        <td>{{ $booking->total_tickets ?? 0 }}</td>
                                        <td>${{ number_format($booking->total_amount, 2) }}</td>
                                        <td>
                                            <span class="badge badge-{{ $booking->status == 'confirmed' ? 'success' : ($booking->status == 'cancelled' ? 'danger' : 'warning') }}">
                                                {{ ucfirst($booking->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge badge-{{ $booking->payment_status == 'paid' ? 'success' : 'warning' }}">
                                                {{ ucfirst($booking->payment_status ?? 'pending') }}
                                            </span>
                                        </td>
                                        <td>{{ $booking->created_at->format('M d, Y H:i') }}</td>
                                        <td>
                                            <a href="{{ route('admin.bookings.show', $booking) }}" class="btn btn-sm btn-info">
                                                <i class="fa fa-eye"></i>
                                            </a>
                                            @if($booking->status !== 'cancelled')
                                                <button class="btn btn-sm btn-danger cancel-booking" data-id="{{ $booking->id }}">
                                                    <i class="fa fa-times"></i>
                                                </button>
                                            @endif
                                            @if($booking->payment)
                                                <button class="btn btn-sm btn-warning refund-booking" data-id="{{ $booking->id }}">
                                                    <i class="fa fa-undo"></i>
                                                </button>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center">No bookings found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center">
                        {{ $bookings->appends(request()->all())->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modals -->
<div class="modal fade" id="cancelModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Cancel Booking</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Are you sure you want to cancel this booking?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-danger" id="confirmCancel">Cancel Booking</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="refundModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Process Refund</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="refundForm">
                    <div class="form-group">
                        <label for="refundAmount">Refund Amount</label>
                        <input type="number" step="0.01" class="form-control" id="refundAmount" name="amount">
                        <small class="form-text text-muted">Leave empty to refund full amount</small>
                    </div>
                    <div class="form-group">
                        <label for="refundReason">Reason *</label>
                        <textarea class="form-control" id="refundReason" name="reason" required></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-warning" id="confirmRefund">Process Refund</button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    let currentBookingId = null;

    // Cancel booking
    $('.cancel-booking').click(function() {
        currentBookingId = $(this).data('id');
        $('#cancelModal').modal('show');
    });

    $('#confirmCancel').click(function() {
        if (currentBookingId) {
            $.ajax({
                url: '/admin/bookings/' + currentBookingId + '/status',
                method: 'PATCH',
                data: {
                    status: 'cancelled',
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
        $('#cancelModal').modal('hide');
    });

    // Refund booking
    $('.refund-booking').click(function() {
        currentBookingId = $(this).data('id');
        $('#refundModal').modal('show');
    });

    $('#confirmRefund').click(function() {
        if (currentBookingId) {
            const formData = {
                amount: $('#refundAmount').val(),
                reason: $('#refundReason').val(),
                _token: '{{ csrf_token() }}'
            };

            $.ajax({
                url: '/admin/bookings/' + currentBookingId + '/refund',
                method: 'POST',
                data: formData,
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
        $('#refundModal').modal('hide');
    });
});
</script>
@endsection
