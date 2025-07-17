@extends('admin.layout.layout')

@section('content')

<div class="row">
    <div class="col-md-12 col-sm-12">
        <div class="x_panel">
            <div class="x_title">
                <h2>Edit Photo Gallery <small>Update gallery details</small></h2>
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
                <form id="edit-photo-gallery-form" action="{{ route('photogallery.update', $gallery->id) }}" class="form-horizontal form-label-left" method="post" enctype="multipart/form-data" novalidate>
                    @csrf
                    @method('PUT')

                    <!-- Show Field -->
                    <div class="item form-group">
                        <label class="col-form-label col-md-3 col-sm-3 label-align" for="show_id">
                            Show
                        </label>
                        <div class="col-md-6 col-sm-6">
                            <select name="show_id" id="show_id" class="form-control">
                                <option value="">Select Show</option>
                                @foreach($shows as $show)
                                    <option value="{{ $show->id }}" {{ $gallery->show_id == $show->id ? 'selected' : '' }}>{{ $show->title }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <br>

                    <!-- Title Field -->
                    <div class="item form-group">
                        <label class="col-form-label col-md-3 col-sm-3 label-align" for="title">
                            Gallery Title <span class="required">*</span>
                        </label>
                        <div class="col-md-6 col-sm-6">
                            <input type="text" id="title" name="title" required="required" class="form-control" value="{{ $gallery->title }}">
                        </div>
                    </div>
                    <br>

                    <!-- Image Field -->
                    <div class="item form-group">
                        <label class="col-form-label col-md-3 col-sm-3 label-align" for="image">
                            Image
                        </label>
                        <div class="col-md-6 col-sm-6">
                            @if($gallery->image)
                                <div class="current-image mb-2">
                                    <img src="{{ asset('storage/' . $gallery->image) }}" alt="{{ $gallery->title }}"
                                         style="max-width: 200px; max-height: 150px; border: 1px solid #ddd; padding: 3px;">
                                    <p class="text-muted mt-1">Current image</p>
                                </div>
                            @endif
                            <input type="file" id="image" name="image" class="form-control" accept="image/*">
                            <small class="form-text text-muted">Leave empty to keep the current image. Maximum file size: 2MB.</small>
                        </div>
                    </div>
                    <br>

                    <!-- Description Field -->
                    <div class="item form-group">
                        <label class="col-form-label col-md-3 col-sm-3 label-align" for="description">
                            Description
                        </label>
                        <div class="col-md-6 col-sm-6">
                            <textarea id="description" name="description" class="form-control" rows="4">{{ $gallery->description }}</textarea>
                        </div>
                    </div>
                    <br>

                    <!-- Display Order Field -->
                    <div class="item form-group">
                        <label class="col-form-label col-md-3 col-sm-3 label-align" for="display_order">
                            Display Order
                        </label>
                        <div class="col-md-6 col-sm-6">
                            <input type="number" id="display_order" name="display_order" class="form-control" value="{{ $gallery->display_order }}">
                        </div>
                    </div>
                    <br>

                    <!-- Is Featured Field -->
                    <div class="item form-group">
                        <label class="col-form-label col-md-3 col-sm-3 label-align" for="is_featured">
                            Featured
                        </label>
                        <div class="col-md-6 col-sm-6">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="is_featured" value="1" class="flat" {{ $gallery->is_featured ? 'checked' : '' }}>
                                    Mark as featured
                                </label>
                            </div>
                        </div>
                    </div>
                    <br>

                    <!-- Status Field -->
                    <div class="item form-group">
                        <label class="col-form-label col-md-3 col-sm-3 label-align" for="is_active">
                            Status <span class="required">*</span>
                        </label>
                        <div class="col-md-6 col-sm-6">
                            <select id="is_active" name="is_active" required="required" class="form-control">
                                <option value="" disabled>Select status</option>
                                <option value="1" {{ $gallery->is_active == 1 ? 'selected' : '' }}>Active</option>
                                <option value="0" {{ $gallery->is_active == 0 ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                    </div>

                    <div class="ln_solid"></div>

                    <!-- Submit Buttons -->
                    <div class="form-group">
                        <div class="col-md-6 col-sm-6 offset-md-3">
                            <a href="{{ route('photogallery.list') }}" class="btn btn-primary">Cancel</a>
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
