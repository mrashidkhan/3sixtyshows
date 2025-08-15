@extends('admin.layout.layout')

@section('content')
    <div class="row">
        <div class="col-md-12 col-sm-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Ticket Types for "{{ $show->title }}" <small>Manage ticket types for this show</small></h2>
                    <ul class="nav navbar-right panel_toolbox">
                        <li>
                            <a href="{{ route('admin.ticket-types.create-for-show', $show) }}" class="btn btn-success btn-sm">
    <i class="fa fa-plus"></i> Add New Ticket Type
</a>
                        </li>
                        <li>
                            <a href="{{ route('show.index') }}" class="btn btn-secondary btn-sm">
                                <i class="fa fa-arrow-left"></i> Back to Shows
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

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    <!-- Show Information Card -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="card border-left-primary">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-3">
                                            @if($show->featured_image)
                                                <img src="{{ asset('storage/' . $show->featured_image) }}" alt="{{ $show->title }}"
                                                    style="width: 100%; max-height: 120px; object-fit: cover; border-radius: 4px;">
                                            @else
                                                <div style="width: 100%; height: 120px; background: #f8f9fa; display: flex; align-items: center; justify-content: center; border-radius: 4px;">
                                                    <i class="fa fa-image fa-2x text-muted"></i>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="col-md-9">
                                            <h5 class="card-title">{{ $show->title }}</h5>
                                            <p class="card-text">
                                                <strong>Venue:</strong> {{ $show->venue->name ?? 'N/A' }}<br>
                                                <strong>Date:</strong> {{ $show->start_date->format('M d, Y H:i') }}<br>
                                                <strong>Status:</strong>
                                                @if($show->status == 'upcoming')
                                                    <span class="badge badge-info">Upcoming</span>
                                                @elseif($show->status == 'ongoing')
                                                    <span class="badge badge-success">Ongoing</span>
                                                @elseif($show->status == 'past')
                                                    <span class="badge badge-secondary">Past</span>
                                                @elseif($show->status == 'cancelled')
                                                    <span class="badge badge-danger">Cancelled</span>
                                                @endif
                                                <strong>Active:</strong>
                                                @if($show->is_active)
                                                    <span class="badge badge-success">Yes</span>
                                                @else
                                                    <span class="badge badge-danger">No</span>
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-striped jambo_table bulk_action">
                            <thead>
                                <tr class="headings">
                                    <th class="column-title"><b>ID</b></th>
                                    <th class="column-title"><b>Name</b></th>
                                    <th class="column-title"><b>Price</b></th>
                                    <th class="column-title"><b>Capacity</b></th>
                                    <th class="column-title"><b>Status</b></th>
                                    <th class="column-title"><b>Display Order</b></th>
                                    <th class="column-title"><b>Description</b></th>
                                    <th class="column-title no-link last"><b>Actions</b></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($ticketTypes as $ticketType)
                                <tr class="{{ $loop->even ? 'even' : 'odd' }} pointer">
                                    <td>{{ $ticketType->id }}</td>
                                    <td>
                                        <strong>{{ $ticketType->name }}</strong>
                                    </td>
                                    <td>
                                        <strong>${{ number_format($ticketType->price, 2) }}</strong>
                                    </td>
                                    <td>
                                        @if($ticketType->capacity)
                                            <span class="badge badge-info">{{ $ticketType->capacity }}</span>
                                        @else
                                            <span class="badge badge-secondary">Unlimited</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($ticketType->is_active)
                                            <span class="badge badge-success">Active</span>
                                        @else
                                            <span class="badge badge-danger">Inactive</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge badge-light">{{ $ticketType->display_order ?? 'No Order' }}</span>
                                    </td>
                                    <td>
                                        @if($ticketType->description)
                                            {{ Str::limit($ticketType->description, 50) }}
                                        @else
                                            <span class="text-muted">No description</span>
                                        @endif
                                    </td>
                                    <td class="last">
                                        <div class="btn-group">
                                            <a href="{{ route('admin.ticket-types.edit', $ticketType) }}" class="btn btn-info btn-sm" title="Edit">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                            <form action="{{ route('admin.ticket-types.delete', $ticketType) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this ticket type? This action cannot be undone.')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm" title="Delete">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center">
                                        <div class="py-4">
                                            <i class="fa fa-ticket fa-3x text-muted mb-3"></i>
                                            <h5>No ticket types found</h5>
                                            <p class="text-muted">
                                                <a href="{{ route('admin.ticket-types.create-for-show', $show) }}" class="btn btn-primary">Create your first ticket type</a>
                                            </p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Auto-dismiss alerts after 5 seconds
        setTimeout(function() {
            $('.alert').fadeOut('slow');
        }, 5000);
    });
</script>
@endsection
