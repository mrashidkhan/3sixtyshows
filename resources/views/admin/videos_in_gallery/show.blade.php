@extends('admin.layout.layout')

@section('content')
<div class="row">
    <div class="col-md-12 col-sm-12">
        <div class="x_panel">
            <div class="x_title">
                <h2>Video Details</h2>
                <div class="clearfix"></div>
            </div>
            <div class="x_content">
                <div class="row">
                    <!-- Video Information -->
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header">
                                <h4>Video Information</h4>
                            </div>
                            <div class="card-body">
                                <table class="table table-bordered">
                                    <tr>
                                        <th style="width: 30%">Video ID</th>
                                        <td>{{ $video->id }}</td>
                                    </tr>
                                    <tr>
                                        <th>Video Gallery</th>
                                        <td>
                                            @if($video->videoGallery)
                                                <a href="{{ route('videogallery.show', $video->videoGallery->id) }}" class="btn btn-link p-0">
                                                    <strong>{{ $video->videoGallery->title }}</strong>
                                                </a>
                                                <br>
                                                <small class="text-muted">Gallery ID: {{ $video->video_gallery_id }}</small>
                                            @else
                                                <span class="text-danger">Gallery not found</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Description</th>
                                        <td>
                                            @if($video->description)
                                                {{ $video->description }}
                                            @else
                                                <span class="text-muted">No description provided</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Display Order</th>
                                        <td>
                                            <span class="badge badge-info">{{ $video->display_order }}</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Status</th>
                                        <td>
                                            @if($video->is_active)
                                                <span class="badge badge-success">Active</span>
                                            @else
                                                <span class="badge badge-secondary">Inactive</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Created At</th>
                                        <td>
                                            {{ $video->created_at->format('F d, Y \a\t g:i A') }}
                                            <br>
                                            <small class="text-muted">{{ $video->created_at->diffForHumans() }}</small>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Last Updated</th>
                                        <td>
                                            {{ $video->updated_at->format('F d, Y \a\t g:i A') }}
                                            <br>
                                            <small class="text-muted">{{ $video->updated_at->diffForHumans() }}</small>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Video Thumbnail -->
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">
                                <h4>Video Thumbnail</h4>
                            </div>
                            <div class="card-body text-center">
                                @if($video->image)
                                    <img src="{{ asset($video->image) }}"
                                         alt="Video thumbnail"
                                         class="img-fluid rounded shadow-sm"
                                         style="max-width: 100%; max-height: 300px; border: 1px solid #ddd;">
                                    <div class="mt-2">
                                        <a href="{{ asset($video->image) }}"
                                           target="_blank"
                                           class="btn btn-sm btn-outline-primary">
                                            <i class="fa fa-external-link"></i> View Full Size
                                        </a>
                                    </div>
                                @else
                                    <div class="text-center py-4">
                                        <i class="fa fa-image fa-3x text-muted mb-3"></i>
                                        <p class="text-muted">No thumbnail image uploaded</p>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Gallery Information Card -->
                        @if($video->videoGallery)
                        <div class="card mt-3">
                            <div class="card-header">
                                <h4>Gallery Information</h4>
                            </div>
                            <div class="card-body">
                                <h5>{{ $video->videoGallery->title }}</h5>
                                @if($video->videoGallery->description)
                                    <p class="text-muted">{{ Str::limit($video->videoGallery->description, 100) }}</p>
                                @endif
                                <div class="mt-2">
                                    <a href="{{ route('videogallery.show', $video->videoGallery->id) }}"
                                       class="btn btn-sm btn-primary">
                                        <i class="fa fa-eye"></i> View Gallery
                                    </a>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="row mt-4">
                    <div class="col-md-12">
                        <div class="ln_solid"></div>
                        <div class="form-group">
                            <div class="text-center">
                                <a href="{{ route('videosingallery.list') }}" class="btn btn-secondary">
                                    <i class="fa fa-arrow-left"></i> Back to List
                                </a>
                                <a href="{{ route('videosingallery.edit', $video->id) }}" class="btn btn-warning">
                                    <i class="fa fa-edit"></i> Edit Video
                                </a>
                                <form action="{{ route('videosingallery.delete', $video->id) }}"
                                      method="POST"
                                      style="display:inline;"
                                      onsubmit="return confirm('Are you sure you want to delete this video? This action cannot be undone.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger">
                                        <i class="fa fa-trash"></i> Delete Video
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
