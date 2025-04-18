@extends('admin.layout.layout')

@section('content')

<div class="row">
    <div class="col-md-12 col-sm-12">
        <div class="x_panel">
            <div class="x_title">
                <h2>Edit Show <small>Update show details</small></h2>
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
                <form id="show-form" action="{{ route('show.update', $show->id) }}" class="form-horizontal form-label-left" method="post" enctype="multipart/form-data" novalidate>
                    @csrf
                    @method('PUT')

                    <!-- Basic Info Section -->
                    <div class="x_title">
                        <h4>Basic Information</h4>
                        <div class="clearfix"></div>
                    </div>

                    <!-- Title Field -->
                    <div class="item form-group">
                        <label class="col-form-label col-md-3 col-sm-3 label-align" for="title">
                            Title <span class="required">*</span>
                        </label>
                        <div class="col-md-6 col-sm-6">
                            <input type="text" id="title" name="title" required="required" class="form-control" value="{{ old('title', $show->title) }}">
                        </div>
                    </div>

                    <!-- Slug Field -->
                    <div class="item form-group">
                        <label class="col-form-label col-md-3 col-sm-3 label-align" for="slug">
                            Slug <span class="required">*</span>
                        </label>
                        <div class="col-md-6 col-sm-6">
                            <input type="text" id="slug" name="slug" required="required" class="form-control" value="{{ old('slug', $show->slug) }}">
                            <small class="form-text text-muted">The slug will be used in the URL.</small>
                        </div>
                    </div>

                    <!-- Category Field -->
                    <div class="item form-group">
                        <label class="col-form-label col-md-3 col-sm-3 label-align" for="category_id">
                            Category <span class="required">*</span>
                        </label>
                        <div class="col-md-6 col-sm-6">
                            <select id="category_id" name="category_id" required="required" class="form-control">
                                <option value="" disabled>Select a category</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ (old('category_id', $show->category_id) == $category->id) ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Venue Field -->
                    <div class="item form-group">
                        <label class="col-form-label col-md-3 col-sm-3 label-align" for="venue_id">
                            Venue <span class="required">*</span>
                        </label>
                        <div class="col-md-6 col-sm-6">
                            <select id="venue_id" name="venue_id" required="required" class="form-control">
                                <option value="" disabled>Select a venue</option>
                                @foreach($venues as $venue)
                                    <option value="{{ $venue->id }}" {{ (old('venue_id', $show->venue_id) == $venue->id) ? 'selected' : '' }}>
                                        {{ $venue->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Short Description Field -->
                    <div class="item form-group">
                        <label class="col-form-label col-md-3 col-sm-3 label-align" for="short_description">
                            Short Description <span class="required">*</span>
                        </label>
                        <div class="col-md-6 col-sm-6">
                            <textarea id="short_description" name="short_description" class="form-control" rows="2" required="required">{{ old('short_description', $show->short_description) }}</textarea>
                            <small class="form-text text-muted">Brief summary that appears in listings (max 255 characters)</small>
                        </div>
                    </div>

                    <!-- Full Description Field -->
                    <div class="item form-group">
                        <label class="col-form-label col-md-3 col-sm-3 label-align" for="description">
                            Full Description <span class="required">*</span>
                        </label>
                        <div class="col-md-6 col-sm-6">
                            <textarea id="description" name="description" class="form-control" rows="6" required="required">{{ old('description', $show->description) }}</textarea>
                        </div>
                    </div>

                    <!-- Featured Image Field -->
                    <div class="item form-group">
                        <label class="col-form-label col-md-3 col-sm-3 label-align" for="featured_image">
                            Featured Image
                        </label>
                        <div class="col-md-6 col-sm-6">
                            @if($show->featured_image)
                                <div class="current-image mb-2">
                                    <img src="{{ asset('storage/' . $show->featured_image) }}" alt="{{ $show->title }}"
                                         style="max-width: 300px; max-height: 200px; border: 1px solid #ddd; padding: 3px;">
                                    <p class="text-muted mt-1">Current image</p>
                                </div>
                            @endif
                            <input type="file" id="featured_image" name="featured_image" class="form-control" accept="image/*">
                            <small class="form-text text-muted">Leave empty to keep the current image. Recommended size: 1200x800 pixels. Maximum file size: 2MB.</small>
                        </div>
                    </div>

                    <!-- Date & Time Section -->
                    <div class="x_title">
                        <h4>Date & Time</h4>
                        <div class="clearfix"></div>
                    </div>

                    <!-- Start Date Field -->
                    <div class="item form-group">
                        <label class="col-form-label col-md-3 col-sm-3 label-align" for="start_date">
                            Start Date & Time <span class="required">*</span>
                        </label>
                        <div class="col-md-6 col-sm-6">
                            <input type="datetime-local" id="start_date" name="start_date" required="required" class="form-control"
                                value="{{ old('start_date', $show->start_date ? $show->start_date->format('Y-m-d\TH:i') : '') }}">
                        </div>
                    </div>

                    <!-- End Date Field -->
                    <div class="item form-group">
                        <label class="col-form-label col-md-3 col-sm-3 label-align" for="end_date">
                            End Date & Time <span class="required">*</span>
                        </label>
                        <div class="col-md-6 col-sm-6">
                            <input type="datetime-local" id="end_date" name="end_date" required="required" class="form-control"
                                value="{{ old('end_date', $show->end_date ? $show->end_date->format('Y-m-d\TH:i') : '') }}">
                        </div>
                    </div>

                    <!-- Duration Field -->
                    <div class="item form-group">
                        <label class="col-form-label col-md-3 col-sm-3 label-align" for="duration">
                            Duration
                        </label>
                        <div class="col-md-6 col-sm-6">
                            <input type="text" id="duration" name="duration" class="form-control"
                                value="{{ old('duration', $show->duration) }}">
                            <small class="form-text text-muted">e.g. "2 hours 30 minutes"</small>
                        </div>
                    </div>

                    <!-- Ticket Info Section -->
                    <div class="x_title">
                        <h4>Ticket Information</h4>
                        <div class="clearfix"></div>
                    </div>

                    <!-- Price Field -->
                    <div class="item form-group">
                        <label class="col-form-label col-md-3 col-sm-3 label-align" for="price">
                            Price <span class="required">*</span>
                        </label>
                        <div class="col-md-6 col-sm-6">
                            <input type="number" id="price" name="price" required="required" class="form-control" step="0.01" min="0"
                                value="{{ old('price', $show->price) }}">
                            <small class="form-text text-muted">Set to 0 for free events</small>
                        </div>
                    </div>

                    <!-- Available Tickets Field -->
                    <div class="item form-group">
                        <label class="col-form-label col-md-3 col-sm-3 label-align" for="available_tickets">
                            Available Tickets
                        </label>
                        <div class="col-md-6 col-sm-6">
                            <input type="number" id="available_tickets" name="available_tickets" class="form-control" min="0"
                                value="{{ old('available_tickets', $show->available_tickets) }}">
                            <small class="form-text text-muted">Leave empty for unlimited tickets</small>
                        </div>
                    </div>

                    <!-- Sold Tickets Info -->
                    <div class="item form-group">
                        <label class="col-form-label col-md-3 col-sm-3 label-align">
                            Tickets Sold
                        </label>
                        <div class="col-md-6 col-sm-6">
                            <p class="form-control-static">{{ $show->sold_tickets ?? 0 }}</p>
                        </div>
                    </div>

                    <!-- Additional Information Section -->
                    <div class="x_title">
                        <h4>Additional Information</h4>
                        <div class="clearfix"></div>
                    </div>

                    <!-- Performers Field -->
                    <div class="item form-group">
                        <label class="col-form-label col-md-3 col-sm-3 label-align" for="performers">
                            Performers/Artists
                        </label>
                        <div class="col-md-6 col-sm-6">
                            <textarea id="performers" name="performers" class="form-control" rows="3">{{ old('performers', is_array($show->performers) ? implode("\n", $show->performers) : $show->performers) }}</textarea>
                            <small class="form-text text-muted">Enter one performer per line</small>
                        </div>
                    </div>

                    <!-- Additional Info Field -->
                    <div class="item form-group">
                        <label class="col-form-label col-md-3 col-sm-3 label-align" for="additional_info">
                            Additional Information
                        </label>
                        <div class="col-md-6 col-sm-6">
                            <textarea id="additional_info" name="additional_info" class="form-control" rows="4">{{ old('additional_info', is_array($show->additional_info) ? collect($show->additional_info)->map(function($value, $key) { return "$key: $value"; })->implode("\n") : $show->additional_info) }}</textarea>
                            <small class="form-text text-muted">Enter in format "Key: Value" (one per line)</small>
                        </div>
                    </div>

                    <!-- Age Restriction Field -->
                    <div class="item form-group">
                        <label class="col-form-label col-md-3 col-sm-3 label-align" for="age_restriction">
                            Age Restriction
                        </label>
                        <div class="col-md-6 col-sm-6">
                            <input type="text" id="age_restriction" name="age_restriction" class="form-control"
                                value="{{ old('age_restriction', $show->age_restriction) }}">
                            <small class="form-text text-muted">e.g. "18+", "All ages", etc.</small>
                        </div>
                    </div>

                    <!-- Settings Section -->
                    <div class="x_title">
                        <h4>Settings</h4>
                        <div class="clearfix"></div>
                    </div>

                    <!-- Featured Field -->
                    <div class="item form-group">
                        <label class="col-form-label col-md-3 col-sm-3 label-align" for="is_featured">
                            Featured
                        </label>
                        <div class="col-md-6 col-sm-6">
                            <select id="is_featured" name="is_featured" class="form-control">
                                <option value="1" {{ old('is_featured', $show->is_featured) == 1 ? 'selected' : '' }}>Yes</option>
                                <option value="0" {{ old('is_featured', $show->is_featured) == 0 ? 'selected' : '' }}>No</option>
                            </select>
                            <small class="form-text text-muted">Featured shows appear on the homepage</small>
                        </div>
                    </div>

                    <!-- Status Field -->
                    <div class="item form-group">
                        <label class="col-form-label col-md-3 col-sm-3 label-align" for="status">
                            Status <span class="required">*</span>
                        </label>
                        <div class="col-md-6 col-sm-6">
                            <select id="status" name="status" required="required" class="form-control">
                                <option value="upcoming" {{ old('status', $show->status) == 'upcoming' ? 'selected' : '' }}>Upcoming</option>
                                <option value="ongoing" {{ old('status', $show->status) == 'ongoing' ? 'selected' : '' }}>Ongoing</option>
                                <option value="past" {{ old('status', $show->status) == 'past' ? 'selected' : '' }}>Past</option>
                                <option value="cancelled" {{ old('status', $show->status) == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                        </div>
                    </div>

                    <!-- Active Field -->
                    <div class="item form-group">
                        <label class="col-form-label col-md-3 col-sm-3 label-align" for="is_active">
                            Active <span class="required">*</span>
                        </label>
                        <div class="col-md-6 col-sm-6">
                            <select id="is_active" name="is_active" required="required" class="form-control">
                                <option value="1" {{ old('is_active', $show->is_active) == 1 ? 'selected' : '' }}>Yes</option>
                                <option value="0" {{ old('is_active', $show->is_active) == 0 ? 'selected' : '' }}>No</option>
                            </select>
                            <small class="form-text text-muted">Inactive shows won't be visible on the website</small>
                        </div>
                    </div>

                    <div class="ln_solid"></div>

                    <!-- Submit Buttons -->
                    <div class="form-group">
                        <div class="col-md-6 col-sm-6 offset-md-3">
                            <a href="{{ route('show.index') }}" class="btn btn-primary">Cancel</a>
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
    // Auto-generate slug from title if slug is empty
    document.getElementById('title').addEventListener('keyup', function() {
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

    // Ensure form data is properly formatted before submission
    document.getElementById('show-form').addEventListener('submit', function(e) {
        // Process performers - split by newlines and convert to JSON
        const performersField = document.getElementById('performers');
        const performersText = performersField.value.trim();
        if (performersText) {
            const performers = performersText.split('\n').map(item => item.trim()).filter(item => item !== '');
            // Create a hidden field for the JSON value
            const hiddenField = document.createElement('input');
            hiddenField.type = 'hidden';
            hiddenField.name = 'performers_json';
            hiddenField.value = JSON.stringify(performers);
            this.appendChild(hiddenField);
        }

        // Process additional info - split by newlines, parse key:value pairs, and convert to JSON
        const additionalInfoField = document.getElementById('additional_info');
        const additionalInfoText = additionalInfoField.value.trim();
        if (additionalInfoText) {
            const lines = additionalInfoText.split('\n').map(item => item.trim()).filter(item => item !== '');
            const infoObject = {};

            lines.forEach(line => {
                const parts = line.split(':');
                if (parts.length >= 2) {
                    const key = parts[0].trim();
                    const value = parts.slice(1).join(':').trim();
                    if (key && value) {
                        infoObject[key] = value;
                    }
                }
            });

            // Create a hidden field for the JSON value
            const hiddenField = document.createElement('input');
            hiddenField.type = 'hidden';
            hiddenField.name = 'additional_info_json';
            hiddenField.value = JSON.stringify(infoObject);
            this.appendChild(hiddenField);
        }
    });
</script>
@endsection
