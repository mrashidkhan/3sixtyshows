@extends('admin.layout.layout')

@section('content')
<div class="row">
    <div class="col-md-12 col-sm-12">
        <div class="x_panel">
            <div class="x_title">
                <h2>Edit Photo in Gallery</h2>
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
                <form action="{{ route('photosingallery.update', [$photo->id]) }}" method="POST" enctype="multipart/form-data" class="form-horizontal form-label-left">
                    @csrf
                    {{-- @method('PUT') --}}

                    <!-- Current Image Field -->
                    <div class="item form-group">
                        <label class="col-form-label col-md-3 col-sm-3 label-align">
                            Current Image
                        </label>
                        <div class="col-md-6 col-sm-6">
                            <div class="current-image mb-2">
                                <img src="{{ asset($photo->image) }}" alt="Current Photo"
                                     style="max-width: 200px; max-height: 150px; border: 1px solid #ddd; padding: 3px;">
                                <p class="text-muted mt-1">Current image</p>
                            </div>
                        </div>
                    </div>
                    <br>

                    <!-- New Image Field -->
                    <div class="item form-group">
                        <label class="col-form-label col-md-3 col-sm-3 label-align" for="image">
                            New Image
                        </label>
                        <div class="col-md-6 col-sm-6">
                            <input type="file" id="image" name="image" class="form-control" accept="image/*">
                            <small class="form-text text-muted">Leave empty to keep the current image. Allowed formats: JPEG, PNG, JPG, GIF. Maximum size: 2MB.</small>
                        </div>
                    </div>
                    <br>

                    <!-- Preview image -->
                    <div class="item form-group">
                        <label class="col-form-label col-md-3 col-sm-3 label-align">Preview</label>
                        <div class="col-md-6 col-sm-6">
                            <img id="image-preview" src="" alt="Preview" style="max-width: 200px; max-height: 150px; display: none; border: 1px solid #ddd; padding: 3px;">
                        </div>
                    </div>
                    <br>

                    <!-- Description Field -->
                    <div class="item form-group">
                        <label class="col-form-label col-md-3 col-sm-3 label-align" for="description">
                            Description
                        </label>
                        <div class="col-md-6 col-sm-6">
                            <textarea id="description" name="description" class="form-control" rows="3">{{ old('description', $photo->description) }}</textarea>
                        </div>
                    </div>
                    <br>

                    <!-- Display Order Field -->
                    <div class="item form-group">
                        <label class="col-form-label col-md-3 col-sm-3 label-align" for="display_order">
                            Display Order
                        </label>
                        <div class="col-md-6 col-sm-6">
                            <input type="number" id="display_order" name="display_order" class="form-control" value="{{ old('display_order', $photo->display_order) }}">
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
                                    <input type="checkbox" name="is_active" value="1" class="flat" {{ old('is_active', $photo->is_active) ? 'checked' : '' }}>
                                    Active
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="ln_solid"></div>

                    <!-- Submit Buttons -->
                    <div class="form-group">
                        <div class="col-md-6 col-sm-6 offset-md-3">
                            <a href="{{ route('photosingallery.list', $photo->id) }}" class="btn btn-primary">Cancel</a>
                            <button type="reset" class="btn btn-secondary">Reset</button>
                            <button type="submit" class="btn btn-success">Update Photo</button>
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
                const reader = new FileReader();
                reader.onload = function(e) {
                    imagePreview.src = e.target.result;
                    imagePreview.style.display = 'block';
                };
                reader.readAsDataURL(this.files[0]);
            } else {
                imagePreview.src = '';
                imagePreview.style.display = 'none';
            }
        });
    });
</script>
@endsection
