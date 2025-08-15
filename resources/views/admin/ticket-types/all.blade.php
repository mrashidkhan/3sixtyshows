@extends('admin.layout.layout')

@section('content')
    <div class="row">
        <div class="col-md-12 col-sm-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2>All Ticket Types <small>Manage ticket types across all shows</small></h2>
                    <ul class="nav navbar-right panel_toolbox">
                        <li>
                            <a href="{{ route('show.index') }}" class="btn btn-secondary btn-sm">
                                <i class="fa fa-calendar"></i> Manage by Show
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

                    <!-- Filter Options -->
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">
                                        <button class="btn btn-link" type="button" data-toggle="collapse" data-target="#filterCollapse" aria-expanded="false">
                                            <i class="fa fa-filter"></i> Filters
                                        </button>
                                    </h5>
                                </div>
                                <div id="filterCollapse" class="collapse">
                                    <div class="card-body">
                                        <form action="{{ route('admin.ticket-types.all') }}" method="GET" id="filterForm">
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label for="show">Show</label>
                                                        <select name="show" id="show" class="form-control form-control-sm">
                                                            <option value="">All Shows</option>
                                                            @foreach($shows as $show)
                                                                <option value="{{ $show->id }}" {{ request('show') == $show->id ? 'selected' : '' }}>
                                                                    {{ $show->title }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        <label for="status">Status</label>
                                                        <select name="status" id="status" class="form-control form-control-sm">
                                                            <option value="">All</option>
                                                            <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Active</option>
                                                            <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Inactive</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label for="search">Search</label>
                                                        <input type="text" name="search" id="search" class="form-control form-control-sm" placeholder="Search ticket types..." value="{{ request('search') }}">
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label>&nbsp;</label>
                                                        <div>
                                                            <button type="submit" class="btn btn-primary btn-sm">
                                                                <i class="fa fa-search"></i> Apply Filters
                                                            </button>
                                                            <a href="{{ route('admin.ticket-types.all') }}" class="btn btn-default btn-sm">
                                                                <i class="fa fa-refresh"></i> Reset
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
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
                                    <th class="column-title"><b>Show</b></th>
                                    <th class="column-title"><b>Ticket Type</b></th>
                                    <th class="column-title"><b>Price</b></th>
                                    <th class="column-title"><b>Capacity</b></th>
                                    <th class="column-title"><b>Sold</b></th>
                                    <th class="column-title"><b>Status</b></th>
                                    <th class="column-title no-link last"><b>Actions</b></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($ticketTypes as $ticketType)
                                <tr class="{{ $loop->even ? 'even' : 'odd' }} pointer">
                                    <td>{{ $ticketType->id }}</td>
                                    <td>
                                        <div>
                                            <strong>{{ $ticketType->show->title }}</strong>
                                            <br>
                                            <small class="text-muted">
                                                {{ $ticketType->show->venue->name ?? 'N/A' }} •
                                                {{ $ticketType->show->start_date->format('M d, Y') }}
                                            </small>
                                        </div>
                                    </td>
                                    <td>
                                        <strong>{{ $ticketType->name }}</strong>
                                        @if($ticketType->description)
                                            <br>
                                            <small class="text-muted">{{ Str::limit($ticketType->description, 30) }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        <strong>{{ $ticketType->formatted_price }}</strong>
                                    </td>
                                    <td>
                                        @if($ticketType->capacity)
                                            <span class="badge badge-info">{{ $ticketType->capacity }}</span>
                                        @else
                                            <span class="badge badge-secondary">Unlimited</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge badge-light">{{ $ticketType->sold_tickets }}</span>
                                        @if($ticketType->capacity)
                                            <br>
                                            <small class="text-muted">{{ $ticketType->available_tickets }} available</small>
                                        @endif
                                    </td>
                                    <td>
                                        @if($ticketType->is_active)
                                            <span class="badge badge-success">Active</span>
                                        @else
                                            <span class="badge badge-danger">Inactive</span>
                                        @endif
                                    </td>
                                    <td class="last">
                                        <div class="btn-group">
                                            <a href="{{ route('admin.ticket-types.edit', $ticketType) }}" class="btn btn-info btn-sm" title="Edit">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                            <a href="{{ route('admin.ticket-types.index', $ticketType->show) }}" class="btn btn-primary btn-sm" title="View Show Tickets">
                                                <i class="fa fa-eye"></i>
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
                                                @if(request()->anyFilled(['show', 'status', 'search']))
                                                    Try adjusting your filters or <a href="{{ route('admin.ticket-types.all') }}">clear all filters</a>
                                                @else
                                                    <a href="{{ route('show.index') }}" class="btn btn-primary">Go to Shows to create ticket types</a>
                                                @endif
                                            </p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>

                        <!-- Pagination -->
                        @if($ticketTypes->hasPages())
                            <div class="d-flex justify-content-center">
                                {{ $ticketTypes->appends(request()->query())->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Auto-submit filter form when select fields change
        document.querySelectorAll('#filterForm select').forEach(function(select) {
            select.addEventListener('change', function() {
                this.form.submit();
            });
        });

        // Search on Enter key
        document.getElementById('search').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                this.form.submit();
            }
        });

        // Auto-dismiss alerts after 5 seconds
        setTimeout(function() {
            $('.alert').fadeOut('slow');
        }, 5000);
    });
</script>
@endsection
