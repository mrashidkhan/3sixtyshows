@extends('admin.layout.layout')

@section('content')
<div class="row">
    <div class="col-md-12 col-sm-12">
        <div class="x_panel">
            <div class="x_title">
                <h2>Edit Video in Gallery</h2>
                <div class="clearfix"></div>
            </div>
            <div class="x_content">
                <br>
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <br>
                <form action="{{ route('videosingallery.update', $video->id) }}" method="POST" enctype="multipart/form-data" class="form-horizontal form-label-left">
                    @csrf
                    {{-- @method('PUT') --}}

                    <!-- Video Gallery Information -->
                    <div class="item form-group">
                        <label class="col-form-label col-md-3 col-sm-3 label-align">
                            Video Gallery
                        </label>
                        <div class="col-md-6 col-sm-6">
                            <p class="form-control-static">{{ $video->videoGallery->title ?? 'N/A' }}</p>
                            <input type="hidden" name="video_gallery_id" value="{{ $video->video_gallery_id }}">
                        </div>
                    </div>
                    <br>

                    <!-- Current Thumbnail -->
                    <div class="item form-group">
                        <label class="col-form-label col-md-3 col-sm-3 label-align">
                            Current Thumbnail
                        </label>
                        <div class="col-md-6 col-sm-6">
                            @if($video->image)
                                <div class="current-image mb-2">
                                    <img src="{{ asset($video->image) }}" alt="Current video thumbnail"
                                         style="max-width: 200px; max-height: 150px; border: 1px solid #ddd; padding: 3px; border-radius: 4px;">
                                    <p class="text-muted mt-1">Current thumbnail image</p>
                                </div>
                            @else
                                <p class="text-muted">No thumbnail image uploaded</p>
                            @endif
                        </div>
                    </div>
                    <br>

                    <!-- New Thumbnail Field -->
                    <div class="item form-group">
                        <label class="col-form-label col-md-3 col-sm-3 label-align" for="image">
                            New Thumbnail
                        </label>
                        <div class="col-md-6 col-sm-6">
                            <input type="file" id="image" name="image" class="form-control" accept="image/*">
                            <small class="form-text text-muted">Leave empty to keep current thumbnail. Allowed formats: JPEG, PNG, JPG, GIF, WEBP. Maximum size: 2MB.</small>
                        </div>
                    </div>
                    <br>

                    <!-- Preview new thumbnail -->
                    <div class="item form-group">
                        <label class="col-form-label col-md-3 col-sm-3 label-align">New Thumbnail Preview</label>
                        <div class="col-md-6 col-sm-6">
                            <img id="image-preview" src="" alt="Preview" style="max-width: 200px; max-height: 150px; display: none; border: 1px solid #ddd; padding: 3px; border-radius: 4px;">
                        </div>
                    </div>
                    <br>

                    <!-- Description Field -->
                    <div class="item form-group">
                        <label class="col-form-label col-md-3 col-sm-3 label-align" for="description">
                            Video Description
                        </label>
                        <div class="col-md-6 col-sm-6">
                            <textarea id="description" name="description" class="form-control" rows="3" placeholder="Enter video description...">{{ old('description', $video->description) }}</textarea>
                        </div>
                    </div>
                    <br>

                    <!-- YouTube Link Field -->
                    <div class="item form-group">
                        <label class="col-form-label col-md-3 col-sm-3 label-align" for="youtubelink">
                            YouTube Link
                        </label>
                        <div class="col-md-6 col-sm-6">
                            <input type="url" id="youtubelink" name="youtubelink" class="form-control" placeholder="Enter YouTube link..." value="{{ old('youtubelink', $video->youtubelink) }}">
                            <small class="form-text text-muted">Optional: Enter a YouTube video URL if this video is hosted on YouTube.</small>
                        </div>
                    </div>
                    <br>

                    <!-- Display Order Field -->
                    <div class="item form-group">
                        <label class="col-form-label col-md-3 col-sm-3 label-align" for="display_order">
                            Display Order
                        </label>
                        <div class="col-md-6 col-sm-6">
                            <input type="number" id="display_order" name="display_order" class="form-control" value="{{ old('display_order', $video->display_order) }}" min="0">
                            <small class="form-text text-muted">Order in which this video should appear in the gallery (0 = first).</small>
                        </div>
                    </div>
                    <br>

                    <!-- Status Field -->
                    <div class="item form-group">
                        <label class="col-form-label col-md-3 col-sm-3 label-align" for="is_active">
                            Status
                        </label>
                        <div class="col-md-6 col-sm-6">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="is_active" value="1" class="flat" {{ old('is_active', $video->is_active) ? 'checked' : '' }}>
                                    Active (Check to make video visible)
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="ln_solid"></div>

                    <!-- Submit Buttons -->
                    <div class="form-group">
                        <div class="col-md-6 col-sm-6 offset-md-3">
                            <a href="{{ route('videosingallery.list') }}" class="btn btn-secondary">Cancel</a>
                            <button type="reset" class="btn btn-warning">Reset</button>
                            <button type="submit" class="btn btn-success">Update Video</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Image preview functionality
        const imageInput = document.getElementById('image');
        const imagePreview = document.getElementById('image-preview');

        imageInput.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                const file = this.files[0];

                // Validate file size (2MB = 2048KB)
                if (file.size > 2048 * 1024) {
                    alert('File size must be less than 2MB');
                    this.value = '';
                    imagePreview.style.display = 'none';
                    return;
                }

                const reader = new FileReader();
                reader.onload = function(e) {
                    imagePreview.src = e.target.result;
                    imagePreview.style.display = 'block';
                };
                reader.readAsDataURL(file);
            } else {
                imagePreview.src = '';
                imagePreview.style.display = 'none';
            }
        });

        // Reset button functionality
        const resetButton = document.querySelector('button[type="reset"]');
        resetButton.addEventListener('click', function() {
            imagePreview.src = '';
            imagePreview.style.display = 'none';
        });
    });
</script>
@endsection
