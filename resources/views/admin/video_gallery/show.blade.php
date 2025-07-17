@extends('admin.layout.layout')

@section('content')
<div class="row">
    <div class="col-md-12 col-sm-12">
        <div class="x_panel">
            <div class="x_title">
                <h2>Video Gallery Details</h2>
                <div class="clearfix"></div>
            </div>
            <div class="x_content">
                <div class="row">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header">
                                <h4>{{ $gallery->title }}</h4>
                            </div>
                            <div class="card-body">
                                <table class="table table-bordered">
                                    <tr>
                                        <th style="width: 30%">ID</th>
                                        <td>{{ $gallery->id }}</td>
                                    </tr>
                                    <tr>
                                        <th>Title</th>
                                        <td>{{ $gallery->title }}</td>
                                    </tr>
                                    <tr>
                                        <th>Video Type</th>
                                        <td>{{ ucfirst($gallery->video_type) }}</td>
                                    </tr>
                                    <tr>
                                        <th>Video URL</th>
                                        <td>
                                            <a href="{{ $gallery->video_url }}" target="_blank">{{ $gallery->video_url }}</a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Description</th>
                                        <td>{{ $gallery->description ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Show</th>
                                        <td>{{ $gallery->show ? $gallery->show->title : 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Display Order</th>
                                        <td>{{ $gallery->display_order }}</td>
                                    </tr>
                                    <tr>
                                        <th>Featured</th>
                                        <td>
                                            @if($gallery->is_featured)
                                                <span class="badge badge-success">Yes</span>
                                            @else
                                                <span class="badge badge-secondary">No</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Status</th>
                                        <td>
                                            @if($gallery->is_active)
                                                <span class="badge badge-success">Active</span>
                                            @else
                                                <span class="badge badge-danger">Inactive</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Created At</th>
                                        <td>{{ $gallery->created_at->format('F d, Y h:i A') }}</td>
                                    </tr>
                                    <tr>
                                        <th>Updated At</th>
                                        <td>{{ $gallery->updated_at->format('F d, Y h:i A') }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">
                                <h4>Video Preview</h4>
                            </div>
                            <div class="card-body text-center">
                                @if($gallery->video_type == 'youtube')
                                    <div class="embed-responsive embed-responsive-16by9">
                                        @php
                                            // Extract YouTube video ID from URL
                                            $videoId = '';
                                            if (preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $gallery->video_url, $match)) {
                                                $videoId = $match[1];
                                            }
                                        @endphp
                                        <iframe class="embed-responsive-item" src="https://www.youtube.com/embed/{{ $videoId }}" allowfullscreen></iframe>
                                    </div>
                                @elseif($gallery->video_type == 'vimeo')
                                    <div class="embed-responsive embed-responsive-16by9">
                                        @php
                                            // Extract Vimeo video ID from URL
                                            $videoId = '';
                                            if (preg_match('/(?:vimeo\.com\/)([0-9]+)/', $gallery->video_url, $match)) {
                                                $videoId = $match[1];
                                            }
                                        @endphp
                                        <iframe class="embed-responsive-item" src="https://player.vimeo.com/video/{{ $videoId }}" allowfullscreen></iframe>
                                    </div>
                                @else
                                    <p>Video preview not available</p>
                                @endif

                                @if($gallery->thumbnail)
                                    <div class="mt-3">
                                        <h5>Thumbnail</h5>
                                        <img src="{{ asset('storage/' . $gallery->thumbnail) }}" alt="{{ $gallery->title }}" class="img-fluid">
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="mt-3">
                            <a href="{{ route('videogallery.list') }}" class="btn btn-primary">Back to List</a>
                            <a href="{{ route('videogallery.edit', $gallery->id) }}" class="btn btn-warning">Edit</a>
                            <form action="{{ route('videogallery.delete', $gallery->id) }}" method="POST" style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this video?')">Delete</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
