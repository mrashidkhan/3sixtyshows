@extends('admin.layout.layout')

@section('content')

<div class="row">
    <div class="col-md-12 col-sm-12">
        <div class="x_panel">
            <div class="x_title">
                <h2>Edit Video Gallery <small>Update Video gallery details</small></h2>
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
                <form id="edit-video-gallery-form" action="{{ route('videogallery.update', $gallery->id) }}" class="form-horizontal form-label-left" method="post" enctype="multipart/form-data" novalidate>
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

                    <!-- Video Type Field -->
                    <div class="item form-group">
                        <label class="col-form-label col-md-3 col-sm-3 label-align" for="video_type">
                            Video Type <span class="required">*</span>
                        </label>
                        <div class="col-md-6 col-sm-6">
                            <select name="video_type" id="video_type" class="form-control" required>
                                <option value="">Select Type</option>
                                <option value="youtube" {{ $gallery->video_type == 'youtube' ? 'selected' : '' }}>YouTube</option>
                                <option value="vimeo" {{ $gallery->video_type == 'vimeo' ? 'selected' : '' }}>Vimeo</option>
                                <option value="other" {{ $gallery->video_type == 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                        </div>
                    </div>
                    <br>

                    <!-- Video URL Field -->
                    <div class="item form-group">
                        <label class="col-form-label col-md-3 col-sm-3 label-align" for="video_url">
                            Video URL <span class="required">*</span>
                        </label>
                        <div class="col-md-6 col-sm-6">
                            <input type="text" id="video_url" name="video_url" required="required" class="form-control" value="{{ $gallery->video_url }}">
                            <small class="form-text text-muted">Enter YouTube or Vimeo URL</small>
                        </div>
                    </div>
                    <br>

                    <!-- Thumbnail Field -->
                    <div class="item form-group">
                        <label class="col-form-label col-md-3 col-sm-3 label-align" for="thumbnail">
                            Thumbnail
                        </label>
                        <div class="col-md-6 col-sm-6">
                            @if($gallery->thumbnail)
                                <div class="current-image mb-2">
                                    <img src="{{ asset('storage/' . $gallery->thumbnail) }}" alt="{{ $gallery->title }}"
                                         style="max-width: 200px; max-height: 150px; border: 1px solid #ddd; padding: 3px;">
                                    <p class="text-muted mt-1">Current thumbnail</p>
                                </div>
                            @endif
                            <input type="file" id="thumbnail" name="thumbnail" class="form-control" accept="image/*">
                            <small class="form-text text-muted">Leave empty to keep the current thumbnail. Maximum file size: 2MB.</small>
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
                            <a href="{{ route('videogallery.list') }}" class="btn btn-primary">Cancel</a>
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
