@extends('admin.layout.layout')

@section('content')
<div class="row">
    <div class="col-md-12">
        <h2>Video Galleries</h2>
        <a href="{{ route('videogallery.create') }}" class="btn btn-success">Add New Video Gallery</a>
        <form action="{{ route('videogallery.list') }}" method="GET" class="form-inline">
            <input type="text" name="search" class="form-control" placeholder="Search..." value="{{ request('search') }}">
            <button type="submit" class="btn btn-primary">Search</button>
        </form>
        <table class="table table-striped mt-3">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Video Type</th>
                    <th>Featured</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($galleries as $gallery)
                <tr>
                    <td>{{ $gallery->id }}</td>
                    <td>{{ $gallery->title }}</td>
                    <td>{{ ucfirst($gallery->video_type) }}</td>
                    <td>
                        @if($gallery->is_featured)
                            <span class="badge badge-success">Yes</span>
                        @else
                            <span class="badge badge-secondary">No</span>
                        @endif
                    </td>
                    <td>
                        @if($gallery->is_active)
                            <span class="badge badge-success">Active</span>
                        @else
                            <span class="badge badge-danger">Inactive</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('videogallery.show', $gallery->id) }}" class="btn btn-info">View</a>
                        <a href="{{ route('videogallery.edit', $gallery->id) }}" class="btn btn-warning">Edit</a>
                        <form action="{{ route('videogallery.delete', $gallery->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this video?')">Delete</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        {{ $galleries->links() }}
    </div>
</div>
@endsection
