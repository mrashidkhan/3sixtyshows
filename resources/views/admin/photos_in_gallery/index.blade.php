@extends('admin.layout.layout')

{{-- @extends('layouts.app') --}}

@section('content')
<div class="container">
    <h1>Photos in Gallery</h1>
    <a href="{{ route('photosingallery.create') }}" class="btn btn-primary mb-3">Add New Photo</a>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Image</th>
                <th>Description</th>
                <th>Display Order</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($photos as $photo)
                <tr>
                    <td>{{ $photo->id }}</td>
                    <td>
                        <img src="{{ asset($photo->image) }}" alt="{{ $photo->description }}" style="width: 100px; height: auto;">
                    </td>
                    <td>{{ $photo->description }}</td>
                    <td>{{ $photo->display_order }}</td>
                    <td>{{ $photo->is_active ? 'Active' : 'Inactive' }}</td>
                    <td>
                        <a href="{{ route('photosingallery.edit', $photo->id) }}" class="btn btn-warning btn-sm">Edit</a>
                        <form action="{{ route('photosingallery.delete', $photo->id) }}" method="POST" style="display:inline;">
                            @csrf
                            {{-- @method('DELETE') --}}
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this photo?');">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{ $photos->links() }} <!-- Pagination links -->
</div>
@endsection
