@extends('admin.layout.layout')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Sales Report</h2>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <!-- Date Filter Form -->
                    <form method="GET" action="{{ route('admin.reports.sales') }}" class="form-inline mb-4">
                        <div class="form-group mx-sm-3 mb-2">
                            <label for="date_from" class="sr-only">From Date</label>
                            <input type="date" name="date_from" id="date_from" class="form-control"
                                   value="{{ $dateFrom->format('Y-m-d') }}" placeholder="From Date">
                        </div>

                        <div class="form-group mx-sm-3 mb-2">
                            <label for="date_to" class="sr-only">To Date</label>
                            <input type="date" name="date_to" id="date_to" class="form-control"
                                   value="{{ $dateTo->format('Y-m-d') }}" placeholder="To Date">
                        </div>

                        <button type="submit" class="btn btn-primary mb-2">Generate Report</button>
                    </form>

                    <!-- Summary Cards -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body">
                                    <h5 class="card-title">Total Revenue</h5>
                                    <h3>${{ number_format($totalRevenue, 2) }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <h5 class="card-title">Total Bookings</h5>
                                    <h3>{{ number_format($totalBookings) }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-info text-white">
                                <div class="card-body">
                                    <h5 class="card-title">Total Tickets</h5>
                                    <h3>{{ number_format($totalTickets) }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-white">
                                <div class="card-body">
                                    <h5 class="card-title">Avg. Order Value</h5>
                                    <h3>${{ $totalBookings > 0 ? number_format($totalRevenue / $totalBookings, 2) : '0.00' }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Daily Sales Chart -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4>Daily Sales Data</h4>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-striped table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>Date</th>
                                                    <th>Bookings</th>
                                                    <th>Tickets Sold</th>
                                                    <th>Revenue</th>
                                                    <th>Avg. Order Value</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($salesData as $data)
                                                    <tr>
                                                        <td>{{ \Carbon\Carbon::parse($data->date)->format('M d, Y') }}</td>
                                                        <td>{{ number_format($data->total_bookings) }}</td>
                                                        <td>{{ number_format($data->total_tickets) }}</td>
                                                        <td>${{ number_format($data->total_revenue, 2) }}</td>
                                                        <td>${{ $data->total_bookings > 0 ? number_format($data->total_revenue / $data->total_bookings, 2) : '0.00' }}</td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="5" class="text-center">No sales data found for the selected period</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                            @if($salesData->count() > 0)
                                            <tfoot class="bg-light">
                                                <tr>
                                                    <th>Total</th>
                                                    <th>{{ number_format($totalBookings) }}</th>
                                                    <th>{{ number_format($totalTickets) }}</th>
                                                    <th>${{ number_format($totalRevenue, 2) }}</th>
                                                    <th>${{ $totalBookings > 0 ? number_format($totalRevenue / $totalBookings, 2) : '0.00' }}</th>
                                                </tr>
                                            </tfoot>
                                            @endif
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Export Options -->
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4>Export Options</h4>
                                </div>
                                <div class="card-body">
                                    <a href="{{ route('admin.bookings.export.csv') }}?date_from={{ $dateFrom->format('Y-m-d') }}&date_to={{ $dateTo->format('Y-m-d') }}"
                                       class="btn btn-success">
                                        <i class="fa fa-download"></i> Export to CSV
                                    </a>
                                    <a href="{{ route('admin.bookings.export.excel') }}?date_from={{ $dateFrom->format('Y-m-d') }}&date_to={{ $dateTo->format('Y-m-d') }}"
                                       class="btn btn-info">
                                        <i class="fa fa-download"></i> Export to Excel
                                    </a>
                                    <button class="btn btn-primary" onclick="window.print()">
                                        <i class="fa fa-print"></i> Print Report
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
// Chart.js implementation could go here for visual charts
// For now, we're showing tabular data
</script>
@endsection
