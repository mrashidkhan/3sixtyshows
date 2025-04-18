@extends('admin.layout.layout')

@section('content')
    <div class="row">
        <div class="col-md-12 col-sm-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Shows <small>Manage all shows</small></h2>
                    <ul class="nav navbar-right panel_toolbox">
                        <li>
                            <a href="{{ route('show.create') }}" class="btn btn-success btn-sm">
                                <i class="fa fa-plus"></i> Add New Show
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
                            <form action="{{ route('show.index') }}" method="GET" class="form-inline">
                                <div class="form-group mr-2">
                                    <select name="category" class="form-control form-control-sm">
                                        <option value="">All Categories</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group mr-2">
                                    <select name="status" class="form-control form-control-sm">
                                        <option value="">All Statuses</option>
                                        <option value="upcoming" {{ request('status') == 'upcoming' ? 'selected' : '' }}>Upcoming</option>
                                        <option value="ongoing" {{ request('status') == 'ongoing' ? 'selected' : '' }}>Ongoing</option>
                                        <option value="past" {{ request('status') == 'past' ? 'selected' : '' }}>Past</option>
                                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                    </select>
                                </div>
                                <div class="form-group mr-2">
                                    <select name="is_active" class="form-control form-control-sm">
                                        <option value="">All</option>
                                        <option value="1" {{ request('is_active') == '1' ? 'selected' : '' }}>Active</option>
                                        <option value="0" {{ request('is_active') == '0' ? 'selected' : '' }}>Inactive</option>
                                    </select>
                                </div>
                                <div class="form-group mr-2">
                                    <input type="text" name="search" class="form-control form-control-sm" placeholder="Search titles..." value="{{ request('search') }}">
                                </div>
                                <button type="submit" class="btn btn-primary btn-sm">Filter</button>
                                <a href="{{ route('show.index') }}" class="btn btn-default btn-sm ml-2">Reset</a>
                            </form>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-striped jambo_table bulk_action">
                            <thead>
                                <tr class="headings">
                                    <th class="column-title"><b>ID</b></th>
                                    <th class="column-title"><b>Image</b></th>
                                    <th class="column-title"><b>Title</b></th>
                                    <th class="column-title"><b>Category</b></th>
                                    <th class="column-title"><b>Venue</b></th>
                                    <th class="column-title"><b>Date</b></th>
                                    <th class="column-title"><b>Status</b></th>
                                    <th class="column-title"><b>Price</b></th>
                                    <th class="column-title"><b>Tickets</b></th>
                                    <th class="column-title no-link last"><b>Actions</b></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($shows as $show)
                                <tr class="{{ $loop->even ? 'even' : 'odd' }} pointer">
                                    <td>{{ $show->id }}</td>
                                    <td>
                                        @if($show->featured_image)
                                            <img src="{{ asset('storage/' . $show->featured_image) }}" alt="{{ $show->title }}"
                                                style="width: 60px; height: 40px; object-fit: cover; border-radius: 4px;">
                                        @else
                                            <span class="badge badge-secondary">No Image</span>
                                        @endif
                                    </td>
                                    <td>
                                        {{ $show->title }}
                                        @if($show->is_featured)
                                            <span class="badge badge-warning ml-1">Featured</span>
                                        @endif
                                    </td>
                                    <td>{{ $show->category->name ?? 'None' }}</td>
                                    <td>{{ $show->venue->name ?? 'None' }}</td>
                                    <td>
                                        <small>
                                            <strong>Start:</strong> {{ $show->start_date->format('M d, Y H:i') }}<br>
                                            <strong>End:</strong> {{ $show->end_date->format('M d, Y H:i') }}
                                        </small>
                                    </td>
                                    <td>
                                        @if($show->status == 'upcoming')
                                            <span class="badge badge-info">Upcoming</span>
                                        @elseif($show->status == 'ongoing')
                                            <span class="badge badge-success">Ongoing</span>
                                        @elseif($show->status == 'past')
                                            <span class="badge badge-secondary">Past</span>
                                        @elseif($show->status == 'cancelled')
                                            <span class="badge badge-danger">Cancelled</span>
                                        @endif

                                        @if(!$show->is_active)
                                            <span class="badge badge-dark">Inactive</span>
                                        @endif
                                    </td>
                                    <td>{{ $show->formatted_price }}</td>
                                    <td>
                                        @if($show->available_tickets)
                                            <small>
                                                Sold: {{ $show->sold_tickets ?? 0 }}/{{ $show->available_tickets }}<br>
                                                @if($show->sold_out)
                                                    <span class="badge badge-danger">Sold Out</span>
                                                @else
                                                    <span class="badge badge-success">Available</span>
                                                @endif
                                            </small>
                                        @else
                                            <span class="badge badge-info">Unlimited</span>
                                        @endif
                                    </td>
                                    <td class="last">
                                        <div class="btn-group">
                                            <a href="{{ route('show.edit', $show->id) }}" class="btn btn-info btn-sm" title="Edit">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                            {{-- <a href="{{ route('show.show', $show->id) }}" class="btn btn-primary btn-sm" title="View">
                                                <i class="fa fa-eye"></i> --}}
                                            </a>
                                            <a href="javascript:void(0)" data-id="{{ $show->id }}" class="btn btn-danger btn-sm show-delete" title="Delete">
                                                <i class="fa fa-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="10" class="text-center">No shows found</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>

                        {{-- <div class="d-flex justify-content-center">
                            {{ $shows->appends(request()->query())->links() }}
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
        // Delete show functionality
        var deleteButtons = document.querySelectorAll('.show-delete');

        deleteButtons.forEach(function(button) {
            button.addEventListener('click', function() {
                var id = this.getAttribute('data-id');

                if (confirm('Are you sure you want to delete this show? This action cannot be undone.')) {
                    // Create a form dynamically
                    var form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '/admin/show/' + id;
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
        document.querySelectorAll('select[name="category"], select[name="status"], select[name="is_active"]').forEach(function(select) {
            select.addEventListener('change', function() {
                this.form.submit();
            });
        });
    });
</script>
@endsection
