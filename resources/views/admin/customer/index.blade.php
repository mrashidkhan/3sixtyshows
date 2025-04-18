@extends('admin.layout.layout')

@section('content')
    <div class="row">
        <div class="col-md-12 col-sm-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Customers <small>Manage all customers</small></h2>
                    <ul class="nav navbar-right panel_toolbox">
                        <li>
                            <a href="{{ route('customer.create') }}" class="btn btn-success btn-sm">
                                <i class="fa fa-plus"></i> Add New Customer
                            </a>
                        </li>
                    </ul>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    <!-- Filter Options -->
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <form action="{{ route('customer.index') }}" method="GET" class="form-inline">
                                <div class="form-group mr-2">
                                    <input type="text" name="search" class="form-control form-control-sm" placeholder="Search by name or email..." value="{{ request('search') }}">
                                </div>
                                <div class="form-group mr-2">
                                    <select name="booking_status" class="form-control form-control-sm">
                                        <option value="">All Customers</option>
                                        <option value="with_bookings" {{ request('booking_status') == 'with_bookings' ? 'selected' : '' }}>With Bookings</option>
                                        <option value="no_bookings" {{ request('booking_status') == 'no_bookings' ? 'selected' : '' }}>No Bookings</option>
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-primary btn-sm">Filter</button>
                                <a href="{{ route('customer.index') }}" class="btn btn-default btn-sm ml-2">Reset</a>
                            </form>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-striped jambo_table bulk_action">
                            <thead>
                                <tr class="headings">
                                    <th class="column-title"><b>ID</b></th>
                                    <th class="column-title"><b>Name</b></th>
                                    <th class="column-title"><b>Email</b></th>
                                    <th class="column-title"><b>Phone</b></th>
                                    <th class="column-title"><b>Location</b></th>
                                    <th class="column-title"><b>Bookings</b></th>
                                    <th class="column-title"><b>Tickets</b></th>
                                    <th class="column-title"><b>Registered</b></th>
                                    <th class="column-title no-link last"><b>Actions</b></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($customers as $customer)
                                <tr class="{{ $loop->even ? 'even' : 'odd' }} pointer">
                                    <td>{{ $customer->id }}</td>
                                    <td>{{ $customer->name }}</td>
                                    <td>{{ $customer->email }}</td>
                                    <td>{{ $customer->phone ?? 'N/A' }}</td>
                                    <td>
                                        @if($customer->city && $customer->country)
                                            {{ $customer->city }}, {{ $customer->country }}
                                        @elseif($customer->country)
                                            {{ $customer->country }}
                                        @elseif($customer->city)
                                            {{ $customer->city }}
                                        @else
                                            <span class="text-muted">Not provided</span>
                                        @endif
                                    </td>
                                    {{-- <td>
                                        <span class="badge badge-info">{{ $customer->bookings->count() }}</span>
                                    </td> --}}
                                    <td>
                                        <span class="badge badge-primary">{{ $customer->tickets->count() }}</span>
                                    </td>
                                    <td>{{ $customer->created_at->format('M d, Y') }}</td>
                                    <td class="last">
                                        <div class="btn-group">
                                            <a href="{{ route('customer.edit', $customer->id) }}" class="btn btn-info btn-sm" title="Edit">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                            <a href="{{ route('customer.show', $customer->id) }}" class="btn btn-primary btn-sm" title="View">
                                                <i class="fa fa-eye"></i>
                                            </a>
                                            <a href="{{ route('customer.delete', $customer->id) }}" class="btn btn-danger btn-sm" title="Delete">
                                                <i class="fa fa-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="9" class="text-center">No customers found</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>

                        {{-- <div class="d-flex justify-content-center">
                            {{ $customers->appends(request()->query())->links() }}
                        </div> --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
