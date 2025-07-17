@extends('admin.layout.layout')

@section('content')
<div class="row">
    <div class="col-md-12">
        <h2>Photo Galleries</h2>
        <a href="{{ route('photogallery.create') }}" class="btn btn-success">Add New Gallery</a>
        <form action="{{ route('photogallery.list') }}" method="GET" class="form-inline">
            <input type="text" name="search" class="form-control" placeholder="Search..." value="{{ request('search') }}">
            <button type="submit" class="btn btn-primary">Search</button>
        </form>
        <table class="table table-striped mt-3">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($galleries as $gallery)
                <tr>
                    <td>{{ $gallery->id }}</td>
                    <td>{{ $gallery->title }}</td>
                    <td>
                        <a href="{{ route('photogallery.show', $gallery->id) }}" class="btn btn-info">View</a>
                        <a href="{{ route('photogallery.edit', $gallery->id) }}" class="btn btn-warning">Edit</a>
                        <form action="{{ route('photogallery.delete', $gallery->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">Delete</button>
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
