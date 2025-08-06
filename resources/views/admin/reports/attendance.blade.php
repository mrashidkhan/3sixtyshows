@extends('admin.layout.layout')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Attendance Report</h2>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>Show</th>
                                    <th>Date</th>
                                    <th>Total Capacity</th>
                                    <th>Tickets Sold</th>
                                    <th>Attendance Rate</th>
                                    <th>Revenue</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($shows as $show)
                                    @php
                                        $ticketsSold = $show->bookings->sum('total_tickets');
                                        $revenue = $show->bookings->sum('total_amount');
                                        $capacity = $show->venue->capacity ?? 0;
                                        $attendanceRate = $capacity > 0 ? ($ticketsSold / $capacity) * 100 : 0;
                                    @endphp
                                    <tr>
                                        <td>{{ $show->title }}</td>
                                        <td>{{ $show->start_date ? $show->start_date->format('M d, Y H:i') : 'N/A' }}</td>
                                        <td>{{ number_format($capacity) }}</td>
                                        <td>{{ number_format($ticketsSold) }}</td>
                                        <td>
                                            <span class="badge badge-{{ $attendanceRate >= 80 ? 'success' : ($attendanceRate >= 50 ? 'warning' : 'danger') }}">
                                                {{ number_format($attendanceRate, 1) }}%
                                            </span>
                                        </td>
                                        <td>${{ number_format($revenue, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">No shows found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
