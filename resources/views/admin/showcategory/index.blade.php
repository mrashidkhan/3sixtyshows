@extends('admin.layout.layout')

@section('content')
    <div class="row">
        <div class="col-md-12 col-sm-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Show Categories <small>Manage all show categories</small></h2>
                    <ul class="nav navbar-right panel_toolbox">
                        <li>
                            <a href="{{ route('showcategory.create') }}" class="btn btn-success btn-sm">
                                <i class="fa fa-plus"></i> Add New Category
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

                    <div class="table-responsive">
                        <table class="table table-striped jambo_table bulk_action">
                            <thead>
                                <tr class="headings">
                                    <th class="column-title"><b>S.No</b></th>
                                    <th class="column-title"><b>ID</b></th>
                                    <th class="column-title"><b>Image</b></th>
                                    <th class="column-title"><b>Name</b></th>
                                    <th class="column-title"><b>Slug</b></th>
                                    <th class="column-title"><b>Description</b></th>
                                    <th class="column-title"><b>Status</b></th>
                                    <th class="column-title"><b>Created Date</b></th>
                                    <th class="column-title no-link last"><b>Actions</b></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($showCategories as $key => $category)
                                <tr class="{{ $key % 2 == 0 ? 'even' : 'odd' }} pointer">
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ $category->id }}</td>
                                    <td>
                                        @if($category->image)
                                            <img src="{{ asset('storage/' . $category->image) }}" alt="{{ $category->name }}"
                                                style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;">
                                        @else
                                            <span class="badge badge-secondary">No Image</span>
                                        @endif
                                    </td>
                                    <td>{{ $category->name }}</td>
                                    <td><code>{{ $category->slug }}</code></td>
                                    <td>{{ Str::limit($category->description, 50) }}</td>
                                    <td>
                                        @if($category->is_active)
                                            <span class="badge badge-success">Active</span>
                                        @else
                                            <span class="badge badge-danger">Inactive</span>
                                        @endif
                                    </td>
                                    <td>{{ $category->created_at->format('M d, Y') }}</td>
                                    <td class="last">
                                        <a href="{{ route('showcategory.edit', $category->id) }}" class="btn btn-info btn-sm" title="Edit">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                        <a href="javascript:void(0)" data-id="{{ $category->id }}" class="btn btn-danger btn-sm showcategory-delete" title="Delete">
                                            <i class="fa fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach

                                @if(count($showCategories) == 0)
                                <tr>
                                    <td colspan="9" class="text-center">No show categories found</td>
                                </tr>
                                @endif
                            </tbody>
                        </table>

                        {{-- <div class="d-flex justify-content-center">
                            {{ $showCategories->links() }}
                        </div> --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection



<script>
document.addEventListener('DOMContentLoaded', function() {
    var deleteButtons = document.querySelectorAll('.showcategory-delete');

    deleteButtons.forEach(function(button) {
        button.addEventListener('click', function() {
            var id = this.getAttribute('data-id');

            if (confirm('Are you sure you want to delete this category?')) {
                // Create a form dynamically
                var form = document.createElement('form');
                form.method = 'POST';
                form.action = '/showcategory/delete/' + id;

                // Add CSRF token
                var csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = '{{ csrf_token() }}';
                form.appendChild(csrfToken);

                // Add to document and submit
                document.body.appendChild(form);
                form.submit();
            }
        });
    });
});
</script>


