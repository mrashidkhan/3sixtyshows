@extends('admin.layout.layout')

@section('content')
<div class="row">
    <div class="col-md-12 col-sm-12">
        <div class="x_panel">
            <div class="x_title">
                <h2>Add New Video to Gallery</h2>
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
                <form action="{{ route('videosingallery.store') }}" method="POST" enctype="multipart/form-data" class="form-horizontal form-label-left">
                    @csrf

                    <!-- Video Gallery Selection -->
                    <div class="item form-group">
                        <label class="col-form-label col-md-3 col-sm-3 label-align" for="video_gallery_id">
                            Video Gallery <span class="required">*</span>
                        </label>
                        <div class="col-md-6 col-sm-6">
                            <select name="video_gallery_id" class="form-control" required>
                                <option value="">Select Video Gallery</option>
                                @foreach($galleries as $gallery)
                                    <option value="{{ $gallery->id }}" {{ old('video_gallery_id') == $gallery->id ? 'selected' : '' }}>
                                        {{ $gallery->title }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <br>

                    <!-- Image Field -->
                    <div class="item form-group">
                        <label class="col-form-label col-md-3 col-sm-3 label-align" for="image">
                            Video thumbnail Image <span class="required">*</span>
                        </label>
                        <div class="col-md-6 col-sm-6">
                            <input type="file" id="image" name="image[]" required="required" class="form-control" accept="image/*" multiple>
                            <small class="form-text text-muted">Allowed formats: JPEG, PNG, JPG, GIF. Maximum size: 2MB.</small>
                        </div>
                    </div>
                    <br>

                    <!-- Preview image -->
                    <div class="item form-group">
                        <label class="col-form-label col-md-3 col-sm-3 label-align">Thumbnail Preview</label>
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
                            <textarea id="description" name="description" class="form-control" rows="3" placeholder="Enter video description...">{{ old('description') }}</textarea>
                        </div>
                    </div>
                    <br>

                    <!-- YouTube Field -->
<div class="item form-group">
    <label class="col-form-label col-md-3 col-sm-3 label-align" for="youtubelink">
        YouTube Link
    </label>
    <div class="col-md-6 col-sm-6">
        <input type="url" id="youtubelink" name="youtubelink" class="form-control" placeholder="Enter YouTube link..." value="{{ old('youtubelink') }}" required>
    </div>
</div>

                    <br>

                    <!-- Display Order Field -->
                    <div class="item form-group">
                        <label class="col-form-label col-md-3 col-sm-3 label-align" for="display_order">
                            Display Order
                        </label>
                        <div class="col-md-6 col-sm-6">
                            <input type="number" id="display_order" name="display_order" class="form-control" value="{{ old('display_order', 0) }}" min="0">
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
                                    <input type="checkbox" name="is_active" value="1" class="flat" {{ old('is_active', 1) ? 'checked' : '' }}>
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
                            <button type="submit" class="btn btn-success">Add Video</button>
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
    });
</script>
@endsection
