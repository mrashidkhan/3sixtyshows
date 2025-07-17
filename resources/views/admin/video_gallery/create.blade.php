@extends('admin.layout.layout')

@section('content')
<div class="row">
    <div class="col-md-12">
        <h2>Add New Video Gallery</h2>
        <form action="{{ route('videogallery.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="form-group">
                <label for="title">Gallery Title</label>
                <input type="text" name="title" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="show_id">Show</label>
                <select name="show_id" class="form-control">
                    <option value="">Select Show</option>
                    @foreach($shows as $show)
                        <option value="{{ $show->id }}">{{ $show->title }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="video_type">Video Type</label>
                <select name="video_type" class="form-control" required>
                    <option value="">Select Type</option>
                    <option value="youtube">YouTube</option>
                    <option value="vimeo">Vimeo</option>
                    <option value="other">Other</option>
                </select>
            </div>

            <div class="form-group">
                <label for="video_url">Video URL</label>
                <input type="text" name="video_url" class="form-control" required>
                <small class="form-text text-muted">Enter YouTube or Vimeo URL</small>
            </div>

            <div class="form-group">
                <label for="thumbnail">Thumbnail Image</label>
                <input type="file" name="thumbnail" class="form-control">
                <small class="form-text text-muted">Optional. If not provided, system will try to use video thumbnail.</small>
            </div>

            <div class="form-group">
                <label for="description">Description</label>
                <textarea name="description" class="form-control" rows="3"></textarea>
            </div>

            <div class="form-group">
                <label for="display_order">Display Order</label>
                <input type="number" name="display_order" class="form-control" value="0">
            </div>

            <div class="form-group">
                <div class="custom-control custom-checkbox">
                    <input type="checkbox" class="custom-control-input" id="is_featured" name="is_featured" value="1">
                    <label class="custom-control-label" for="is_featured">Featured</label>
                </div>
            </div>

            <div class="form-group">
                <div class="custom-control custom-checkbox">
                    <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" value="1" checked>
                    <label class="custom-control-label" for="is_active">Active</label>
                </div>
            </div>

            <button type="submit" class="btn btn-success">Create Gallery</button>
            <a href="{{ route('videogallery.list') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>
@endsection
