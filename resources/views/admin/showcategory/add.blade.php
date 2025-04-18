@extends('admin.layout.layout')

@section('content')

<div class="row">
    <div class="col-md-12 col-sm-12">
        <div class="x_panel">
            <div class="x_title">
                <h2>Add Show Category <small>Create a new show category</small></h2>
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
                <form id="show-category-form" action="{{ route('showcategory.store') }}" class="form-horizontal form-label-left" method="post" enctype="multipart/form-data" novalidate>
                    @csrf
                    <div class="item form-group">
                        <label class="col-form-label col-md-3 col-sm-3 label-align" for="name">
                            Name <span class="required">*</span>
                        </label>
                        <div class="col-md-6 col-sm-6">
                            <input type="text" id="name" name="name" required="required" class="form-control" value="{{ old('name') }}">
                        </div>
                    </div>
                    <br>
                    <div class="item form-group">
                        <label class="col-form-label col-md-3 col-sm-3 label-align" for="slug">
                            Slug <span class="required">*</span>
                        </label>
                        <div class="col-md-6 col-sm-6">
                            <input type="text" id="slug" name="slug" required="required" class="form-control" value="{{ old('slug') }}">
                            <small class="form-text text-muted">The slug will be used in the URL. If left empty, it will be generated automatically from the name.</small>
                        </div>
                    </div>
                    <br>
                    <div class="item form-group">
                        <label class="col-form-label col-md-3 col-sm-3 label-align" for="description">
                            Description <span class="required">*</span>
                        </label>
                        <div class="col-md-6 col-sm-6">
                            <textarea id="description" name="description" class="form-control" rows="4">{{ old('description') }}</textarea>
                        </div>
                    </div>
                    <br>
                    <div class="item form-group">
                        <label class="col-form-label col-md-3 col-sm-3 label-align" for="image">
                            Image
                        </label>
                        <div class="col-md-6 col-sm-6">
                            <input type="file" id="image" name="image" class="form-control" accept="image/*">
                            <small class="form-text text-muted">Recommended size: 800x600 pixels. Maximum file size: 2MB.</small>
                        </div>
                    </div>
                    <br>
                    <div class="item form-group">
                        <label class="col-form-label col-md-3 col-sm-3 label-align" for="is_active">
                            Status <span class="required">*</span>
                        </label>
                        <div class="col-md-6 col-sm-6">
                            <select id="is_active" name="is_active" required="required" class="form-control">
                                <option value="" disabled selected>Select status</option>
                                <option value="1" {{ old('is_active') == '1' ? 'selected' : '' }}>Active</option>
                                <option value="0" {{ old('is_active') == '0' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                    </div>
                    <div class="ln_solid"></div>
                    <div class="form-group">
                        <div class="col-md-6 col-sm-6 offset-md-3">
                            <button type="button" class="btn btn-primary" onclick="window.history.back();">Cancel</button>
                            <button type="reset" class="btn btn-secondary">Reset</button>
                            <button type="submit" class="btn btn-success">Submit</button>
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
    // Auto-generate slug from name
    document.getElementById('name').addEventListener('keyup', function() {
        const nameValue = this.value;
        const slugInput = document.getElementById('slug');

        // Only auto-generate if the slug field is empty or hasn't been manually edited
        if (!slugInput.value || slugInput._autoGenerated) {
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
