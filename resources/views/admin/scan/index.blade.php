@extends('admin.layout.layout')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Ticket Scanner</h2>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h4>Scan Ticket</h4>
                                </div>
                                <div class="card-body">
                                    <form id="scanForm">
                                        <div class="form-group">
                                            <label for="ticket_code">Ticket Code / QR Code</label>
                                            <input type="text" class="form-control" id="ticket_code" name="ticket_code"
                                                   placeholder="Enter ticket code or scan QR code" required>
                                        </div>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fa fa-search"></i> Validate Ticket
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h4>Scan Result</h4>
                                </div>
                                <div class="card-body" id="scanResult">
                                    <p class="text-muted">No ticket scanned yet.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Scans -->
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4>Recent Scans</h4>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-striped" id="recentScans">
                                            <thead>
                                                <tr>
                                                    <th>Time</th>
                                                    <th>Ticket Code</th>
                                                    <th>Customer</th>
                                                    <th>Show</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <!-- Recent scans will be populated here -->
                                            </tbody>
                                        </table>
                                    </div>
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
$(document).ready(function() {
    $('#scanForm').on('submit', function(e) {
        e.preventDefault();

        const ticketCode = $('#ticket_code').val();

        if (!ticketCode) {
            alert('Please enter a ticket code');
            return;
        }

        $.ajax({
            url: '{{ route("admin.scan.validate") }}',
            method: 'POST',
            data: {
                ticket_code: ticketCode,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    $('#scanResult').html(`
                        <div class="alert alert-success">
                            <h5><i class="fa fa-check"></i> Valid Ticket</h5>
                            <p><strong>Booking:</strong> ${response.ticket_info.booking_reference}</p>
                            <p><strong>Customer:</strong> ${response.ticket_info.customer_name}</p>
                            <p><strong>Show:</strong> ${response.ticket_info.show_title}</p>
                        </div>
                    `);

                    // Add to recent scans
                    const newRow = `
                        <tr>
                            <td>${new Date().toLocaleString()}</td>
                            <td>${ticketCode}</td>
                            <td>${response.ticket_info.customer_name}</td>
                            <td>${response.ticket_info.show_title}</td>
                            <td><span class="badge badge-success">Valid</span></td>
                        </tr>
                    `;
                    $('#recentScans tbody').prepend(newRow);
                } else {
                    $('#scanResult').html(`
                        <div class="alert alert-danger">
                            <h5><i class="fa fa-times"></i> Invalid Ticket</h5>
                            <p>${response.message}</p>
                        </div>
                    `);

                    // Add to recent scans
                    const newRow = `
                        <tr>
                            <td>${new Date().toLocaleString()}</td>
                            <td>${ticketCode}</td>
                            <td>-</td>
                            <td>-</td>
                            <td><span class="badge badge-danger">Invalid</span></td>
                        </tr>
                    `;
                    $('#recentScans tbody').prepend(newRow);
                }

                // Clear the input
                $('#ticket_code').val('').focus();
            },
            error: function() {
                $('#scanResult').html(`
                    <div class="alert alert-danger">
                        <h5><i class="fa fa-exclamation-triangle"></i> Error</h5>
                        <p>An error occurred while validating the ticket.</p>
                    </div>
                `);
            }
        });
    });

    // Focus on ticket code input
    $('#ticket_code').focus();
});
</script>
@endsection
