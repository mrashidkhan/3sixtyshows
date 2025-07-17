@extends('admin.layout.layout')

@section('content')
<div class="row">
    <div class="col-md-12 col-sm-12">
        <div class="x_panel">
            <div class="x_title">
                <h2>Videos in Gallery Management</h2>
                <div class="clearfix"></div>
            </div>
            <div class="x_content">
                <div class="row mb-3">
                    <div class="col-md-12">
                        <a href="{{ route('videosingallery.create') }}" class="btn btn-success">
                            <i class="fa fa-plus"></i> Add New Video
                        </a>
                    </div>
                </div>

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <strong>Success!</strong> {{ session('success') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <strong>Error!</strong> {{ session('error') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                <div class="table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead class="thead-dark">
                            <tr>
                                <th>ID</th>
                                <th>Thumbnail</th>
                                <th>Video Gallery</th>
                                <th>Description</th>
                                <th>Youtube link</th>
                                {{-- <th>Status</th> --}}
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($videos as $video)
                                <tr>
                                    <td>{{ $video->id }}</td>
                                    <td>
                                        @if($video->image)
                                            <img src="{{ asset($video->image) }}"
                                                 alt="Video thumbnail"
                                                 style="width: 80px; height: 60px; object-fit: cover; border-radius: 4px; border: 1px solid #ddd;">
                                        @else
                                            <div style="width: 80px; height: 60px; background-color: #f8f9fa; border: 1px solid #ddd; border-radius: 4px; display: flex; align-items: center; justify-content: center;">
                                                <small class="text-muted">No Image</small>
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        <strong>{{ $video->videoGallery->title ?? 'N/A' }}</strong>
                                        @if($video->videoGallery)
                                            <br><small class="text-muted">ID: {{ $video->video_gallery_id }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        @if($video->description)
                                            {{ Str::limit($video->description, 50) }}
                                        @else
                                            <span class="text-muted">No description</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge badge-info">{{ $video->youtubelink }}</span>
                                    </td>
                                     {{-- <td>
                                        <span class="badge badge-info">{{ $video->display_order }}</span>
                                    </td> --}}
                                    {{--
                                    <td>
                                        @if($video->is_active)
                                            <span class="badge badge-success">Active</span>
                                        @else
                                            <span class="badge badge-secondary">Inactive</span>
                                        @endif
                                    </td> --}}
                                    <td>
                                        <small>{{ $video->created_at->format('M d, Y') }}</small>
                                        <br>
                                        <small class="text-muted">{{ $video->created_at->format('H:i') }}</small>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('videosingallery.show', $video->id) }}"
                                               class="btn btn-info btn-sm"
                                               title="View Details">
                                                <i class="fa fa-eye"></i>
                                            </a>
                                            <a href="{{ route('videosingallery.edit', $video->id) }}"
                                               class="btn btn-warning btn-sm"
                                               title="Edit">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                            <form action="{{ route('videosingallery.delete', $video->id) }}"
                                                  method="POST"
                                                  style="display:inline;"
                                                  onsubmit="return confirm('Are you sure you want to delete this video? This action cannot be undone.');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="btn btn-danger btn-sm"
                                                        title="Delete">
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
                                            <i class="fa fa-video-camera fa-3x text-muted mb-3"></i>
                                            <h5 class="text-muted">No videos found</h5>
                                            <p class="text-muted">Get started by adding your first video to the gallery.</p>
                                            <a href="{{ route('videosingallery.create') }}" class="btn btn-primary">
                                                <i class="fa fa-plus"></i> Add First Video
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($videos->hasPages())
                    <div class="row">
                        <div class="col-md-12">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <p class="text-muted">
                                        Showing {{ $videos->firstItem() }} to {{ $videos->lastItem() }} of {{ $videos->total() }} results
                                    </p>
                                </div>
                                <div>
                                    {{ $videos->links() }}
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Auto-hide alerts after 5 seconds
    document.addEventListener('DOMContentLoaded', function() {
        const alerts = document.querySelectorAll('.alert:not(.alert-permanent)');
        alerts.forEach(function(alert) {
            setTimeout(function() {
                if (alert.classList.contains('show')) {
                    alert.classList.remove('show');
                    setTimeout(function() {
                        alert.remove();
                    }, 150);
                }
            }, 5000);
        });
    });
</script>
@endsection
