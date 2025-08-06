@extends('admin.layout.layout')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Revenue Report</h2>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>Show</th>
                                    <th>Date</th>
                                    <th>Total Bookings</th>
                                    <th>Tickets Sold</th>
                                    <th>Total Revenue</th>
                                    <th>Avg. Ticket Price</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($revenueData as $data)
                                    <tr>
                                        <td>{{ $data->title }}</td>
                                        <td>{{ $data->start_date ? \Carbon\Carbon::parse($data->start_date)->format('M d, Y H:i') : 'N/A' }}</td>
                                        <td>{{ number_format($data->total_bookings) }}</td>
                                        <td>{{ number_format($data->tickets_sold) }}</td>
                                        <td>${{ number_format($data->total_revenue, 2) }}</td>
                                        <td>${{ $data->tickets_sold > 0 ? number_format($data->total_revenue / $data->tickets_sold, 2) : '0.00' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">No revenue data found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                            @if($revenueData->count() > 0)
                            <tfoot class="bg-light">
                                <tr>
                                    <th>Total</th>
                                    <th>-</th>
                                    <th>{{ number_format($revenueData->sum('total_bookings')) }}</th>
                                    <th>{{ number_format($revenueData->sum('tickets_sold')) }}</th>
                                    <th>${{ number_format($revenueData->sum('total_revenue'), 2) }}</th>
                                    <th>-</th>
                                </tr>
                            </tfoot>
                            @endif
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
