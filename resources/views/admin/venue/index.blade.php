@extends('admin.layout.layout')

@section('content')
    <div class="row">
        <div class="col-md-12 col-sm-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Venues <small>Manage all venues</small></h2>
                    <ul class="nav navbar-right panel_toolbox">
                        <li>
                            <a href="{{ route('venue.create') }}" class="btn btn-success btn-sm">
                                <i class="fa fa-plus"></i> Add New Venue
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
                            <form action="{{ route('venues.index') }}" method="GET" class="form-inline">
                                <div class="form-group mr-2">
                                    <input type="text" name="search" class="form-control form-control-sm" placeholder="Search by name or city..." value="{{ request('search') }}">
                                </div>
                                <div class="form-group mr-2">
                                    <select name="country" class="form-control form-control-sm">
                                        <option value="">All Countries</option>
                                        @foreach($countries as $country)
                                            <option value="{{ $country }}" {{ request('country') == $country ? 'selected' : '' }}>
                                                {{ $country }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-primary btn-sm">Filter</button>
                                <a href="{{ route('venues.index') }}" class="btn btn-default btn-sm ml-2">Reset</a>
                            </form>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-striped jambo_table bulk_action">
                            <thead>
                                <tr class="headings">
                                    <th class="column-title"><b>ID</b></th>
                                    <th class="column-title"><b>Name</b></th>
                                    <th class="column-title"><b>Location</b></th>
                                    <th class="column-title"><b>Capacity</b></th>
                                    <th class="column-title"><b>Contact Info</b></th>
                                    <th class="column-title"><b>Shows</b></th>
                                    <th class="column-title no-link last"><b>Actions</b></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($venues as $venue)
                                <tr class="{{ $loop->even ? 'even' : 'odd' }} pointer">
                                    <td>{{ $venue->id }}</td>
                                    <td>
                                        <strong>{{ $venue->name }}</strong><br>
                                        <small class="text-muted"><code>{{ $venue->slug }}</code></small>
                                    </td>
                                    <td>
                                        {{ $venue->city }}, {{ $venue->country }}
                                        @if($venue->state)
                                            <br><small>{{ $venue->state }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        @if($venue->capacity)
                                            {{ number_format($venue->capacity) }} people
                                        @else
                                            <span class="badge badge-secondary">Not specified</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($venue->contact_phone)
                                            <i class="fa fa-phone"></i> {{ $venue->contact_phone }}<br>
                                        @endif
                                        @if($venue->contact_email)
                                            <i class="fa fa-envelope"></i> {{ $venue->contact_email }}
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge badge-info">{{ $venue->shows_count ?? 0 }} shows</span>
                                    </td>
                                    <td class="last">
                                        <div class="btn-group">
                                            <a href="{{ route('venue.edit', $venue->id) }}" class="btn btn-info btn-sm" title="Edit">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                            {{-- <a href="{{ route('venue.show', $venue->id) }}" class="btn btn-primary btn-sm" title="View">
                                                <i class="fa fa-eye"></i>
                                            </a> --}}
                                            <a href="javascript:void(0)" data-id="{{ $venue->id }}" class="btn btn-danger btn-sm venue-delete" title="Delete">
                                                <i class="fa fa-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center">No venues found</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>

                        {{-- <div class="d-flex justify-content-center">
                            {{ $venues->appends(request()->query())->links() }}
                        </div> --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Delete venue functionality
        var deleteButtons = document.querySelectorAll('.venue-delete');

        deleteButtons.forEach(function(button) {
            button.addEventListener('click', function() {
                var id = this.getAttribute('data-id');

                if (confirm('Are you sure you want to delete this venue? This will also affect any associated shows.')) {
                    // Create a form dynamically
                    var form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '/admin/venue/' + id;
                    form.style.display = 'none';

                    // Add CSRF token
                    var csrfToken = document.createElement('input');
                    csrfToken.type = 'hidden';
                    csrfToken.name = '_token';
                    csrfToken.value = '{{ csrf_token() }}';
                    form.appendChild(csrfToken);

                    // Add method field
                    var methodField = document.createElement('input');
                    methodField.type = 'hidden';
                    methodField.name = '_method';
                    methodField.value = 'DELETE';
                    form.appendChild(methodField);

                    // Add to document and submit
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        });

        // Auto-submit filter form when select fields change
        document.querySelectorAll('select[name="country"]').forEach(function(select) {
            select.addEventListener('change', function() {
                this.form.submit();
            });
        });
    });
</script>
@endsection
