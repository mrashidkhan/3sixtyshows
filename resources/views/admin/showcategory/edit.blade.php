@extends('admin.layout.layout')

@section('content')

    <div class="row">
        <div class="col-md-12 col-sm-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Edit Show Category <small>Update category details</small></h2>
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
                    <form id="edit-show-category-form" action="{{ route('showcategory.update', $showCategory->id) }}"
                        class="form-horizontal form-label-left" method="post" enctype="multipart/form-data" novalidate>
                        @csrf
                        @method('PUT') <!-- Specify the PUT method for updates -->

                        <!-- Name Field -->
                        <div class="item form-group">
                            <label class="col-form-label col-md-3 col-sm-3 label-align" for="name">
                                Name <span class="required">*</span>
                            </label>
                            <div class="col-md-6 col-sm-6">
                                <input type="text" id="name" value="{{ $showCategory->name }}"
                                    name="name" required="required" class="form-control">
                            </div>
                        </div>

                        <br>

                        <!-- Slug Field -->
                        <div class="item form-group">
                            <label class="col-form-label col-md-3 col-sm-3 label-align" for="slug">
                                Slug <span class="required">*</span>
                            </label>
                            <div class="col-md-6 col-sm-6">
                                <input type="text" id="slug" value="{{ $showCategory->slug }}"
                                    name="slug" required="required" class="form-control">
                                <small class="form-text text-muted">The slug will be used in the URL.</small>
                            </div>
                        </div>

                        <br>

                        <!-- Description Field -->
                        <div class="item form-group">
                            <label class="col-form-label col-md-3 col-sm-3 label-align" for="description">
                                Description
                            </label>
                            <div class="col-md-6 col-sm-6">
                                <textarea id="description" name="description" class="form-control" rows="4">{{ $showCategory->description }}</textarea>
                            </div>
                        </div>

                        <br>

                        <!-- Image Field -->
                        <div class="item form-group">
                            <label class="col-form-label col-md-3 col-sm-3 label-align" for="image">
                                Image
                            </label>
                            <div class="col-md-6 col-sm-6">
                                @if($showCategory->image)
                                    <div class="current-image mb-2">
                                        <img src="{{ asset('storage/' . $showCategory->image) }}" alt="{{ $showCategory->name }}"
                                             style="max-width: 200px; max-height: 150px; border: 1px solid #ddd; padding: 3px;">
                                        <p class="text-muted mt-1">Current image</p>
                                    </div>
                                @endif
                                <input type="file" id="image" name="image" class="form-control" accept="image/*">
                                <small class="form-text text-muted">Leave empty to keep the current image. Recommended size: 800x600 pixels. Maximum file size: 2MB.</small>
                            </div>
                        </div>

                        <br>

                        <!-- Status Field -->
                        <div class="item form-group">
                            <label class="col-form-label col-md-3 col-sm-3 label-align" for="is_active">
                                Status <span class="required">*</span>
                            </label>
                            <div class="col-md-6 col-sm-6">
                                <select required="required" name="is_active" id="is_active" class="form-control">
                                    <option value="" disabled>Select status</option>
                                    <option value="1" {{ $showCategory->is_active == 1 ? 'selected' : '' }}>Active</option>
                                    <option value="0" {{ $showCategory->is_active == 0 ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>
                        </div>

                        <div class="ln_solid"></div>

                        <!-- Submit Button -->
                        <div class="form-group">
                            <div class="col-md-6 col-sm-6 offset-md-3">
                                <a href="{{ route('showcategory.list') }}" class="btn btn-primary">Cancel</a>
                                <button type="reset" class="btn btn-secondary">Reset</button>
                                <button type="submit" class="btn btn-success">Update</button>
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
    // Auto-generate slug from name if slug is empty
    document.getElementById('name').addEventListener('keyup', function() {
        const slugInput = document.getElementById('slug');

        // Only auto-generate if the slug field is empty or hasn't been manually edited
        if (!slugInput.value || slugInput._autoGenerated) {
            const nameValue = this.value;
            const slug = nameValue
                .toLowerCase()
                .replace(/[^\w\s-]/g, '') // Remove special characters
                .replace(/\s+/g, '-')     // Replace spaces with hyphens
                .replace(/--+/g, '-')     // Replace multiple hyphens with single hyphen
                .trim();                  // Trim leading/trailing spaces

            slugInput.value = slug;
            slugInput._autoGenerated = true;
        }
    });

    // Mark slug as manually edited
    document.getElementById('slug').addEventListener('input', function() {
        this._autoGenerated = false;
    });
</script>
@endsection
