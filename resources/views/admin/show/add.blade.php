@extends('admin.layout.layout')

@section('content')

<div class="row">
    <div class="col-md-12 col-sm-12">
        <div class="x_panel">
            <div class="x_title">
                <h2>Add Show <small>Create a new show</small></h2>
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
                <form id="show-form" action="{{ route('show.store') }}" class="form-horizontal form-label-left" method="post" enctype="multipart/form-data" novalidate>
                    @csrf

                    <!-- Basic Information -->
                    <h3>Basic Information</h3>
                    <div class="item form-group">
                        <label class="col-form-label col-md-3 col-sm-3 label-align" for="title">
                            Title <span class="required">*</span>
                        </label>
                        <div class="col-md-6 col-sm-6">
                            <input type="text" id="title" name="title" required="required" class="form-control" value="{{ old('title') }}">
                        </div>
                    </div>

                    <div class="item form-group">
                        <label class="col-form-label col-md-3 col-sm-3 label-align" for="slug">
                            Slug <span class="required">*</span>
                        </label>
                        <div class="col-md-6 col-sm-6">
                            <input type="text" id="slug" name="slug" required="required" class="form-control" value="{{ old('slug') }}">
                            <small class="form-text text-muted">The slug will be used in the URL. If left empty, it will be generated automatically from the title.</small>
                        </div>
                    </div>

                    <div class="item form-group">
                        <label class="col-form-label col-md-3 col-sm-3 label-align" for="category_id">
                            Category <span class="required">*</span>
                        </label>
                        <div class="col-md-6 col-sm-6">
                            <select id="category_id" name="category_id" required="required" class="form-control">
                                <option value="" disabled selected>Select Category</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="item form-group">
                        <label class="col-form-label col-md-3 col-sm-3 label-align" for="venue_id">
                            Venue <span class="required">*</span>
                        </label>
                        <div class="col-md-6 col-sm-6">
                            <select id="venue_id" name="venue_id" required="required" class="form-control">
                                <option value="" disabled selected>Select Venue</option>
                                @foreach($venues as $venue)
                                    <option value="{{ $venue->id }}" {{ old('venue_id') == $venue->id ? 'selected' : '' }}>
                                        {{ $venue->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="item form-group">
                        <label class="col-form-label col-md-3 col-sm-3 label-align" for="short_description">
                            Short Description <span class="required">*</span>
                        </label>
                        <div class="col-md-6 col-sm-6">
                            <textarea id="short_description" name="short_description" required="required" class="form-control" rows="3">{{ old('short_description') }}</textarea>
                        </div>
                    </div>

                    <div class="item form-group">
                        <label class="col-form-label col-md-3 col-sm-3 label-align" for="description">
                            Full Description <span class="required">*</span>
                        </label>
                        <div class="col-md-6 col-sm-6">
                            <textarea id="description" name="description" required="required" class="form-control" rows="6">{{ old('description') }}</textarea>
                        </div>
                    </div>

                    <div class="item form-group">
                        <label class="col-form-label col-md-3 col-sm-3 label-align" for="featured_image">
                            Featured Image <span class="required">*</span>
                        </label>
                        <div class="col-md-6 col-sm-6">
                            <input type="file" id="featured_image" name="featured_image" class="form-control" accept="image/*" required="required">
                            <small class="form-text text-muted">Recommended size: 1200x800 pixels. Maximum file size: 2MB.</small>
                        </div>
                    </div>

                    <!-- Dates and Tickets -->
                    <h3 class="mt-4">Dates and Tickets</h3>

                    <div class="item form-group">
                        <label class="col-form-label col-md-3 col-sm-3 label-align" for="start_date">
                            Start Date <span class="required">*</span>
                        </label>
                        <div class="col-md-6 col-sm-6">
                            <input type="datetime-local" id="start_date" name="start_date" required="required" class="form-control" value="{{ old('start_date') }}">
                        </div>
                    </div>

                    <div class="item form-group">
                        <label class="col-form-label col-md-3 col-sm-3 label-align" for="end_date">
                            End Date <span class="required">*</span>
                        </label>
                        <div class="col-md-6 col-sm-6">
                            <input type="datetime-local" id="end_date" name="end_date" required="required" class="form-control" value="{{ old('end_date') }}">
                        </div>
                    </div>

                    <div class="item form-group">
                        <label class="col-form-label col-md-3 col-sm-3 label-align" for="price">
                            Price
                        </label>
                        <div class="col-md-6 col-sm-6">
                            <input type="number" id="price" name="price" step="0.01" min="0" class="form-control" value="{{ old('price') }}">
                            <small class="form-text text-muted">Leave empty or set to 0 for free events.</small>
                        </div>
                    </div>

                    <div class="item form-group">
                        <label class="col-form-label col-md-3 col-sm-3 label-align" for="available_tickets">
                            Available Tickets
                        </label>
                        <div class="col-md-6 col-sm-6">
                            <input type="number" id="available_tickets" name="available_tickets" min="0" class="form-control" value="{{ old('available_tickets') }}">
                            <small class="form-text text-muted">Leave empty for unlimited tickets.</small>
                        </div>
                    </div>

                    <div class="item form-group">
                        <label class="col-form-label col-md-3 col-sm-3 label-align" for="duration">
                            Duration (minutes)
                        </label>
                        <div class="col-md-6 col-sm-6">
                            <input type="number" id="duration" name="duration" min="0" class="form-control" value="{{ old('duration') }}">
                        </div>
                    </div>

                    <!-- Additional Information -->
                    <h3 class="mt-4">Additional Information</h3>

                    <div class="item form-group">
                        <label class="col-form-label col-md-3 col-sm-3 label-align" for="performers">
                            Performers
                        </label>
                        <div class="col-md-6 col-sm-6">
                            <textarea id="performers" name="performers" class="form-control" rows="3">{{ old('performers') }}</textarea>
                            <small class="form-text text-muted">Enter one performer per line.</small>
                        </div>
                    </div>

                    <div class="item form-group">
                        <label class="col-form-label col-md-3 col-sm-3 label-align" for="additional_info">
                            Additional Info
                        </label>
                        <div class="col-md-6 col-sm-6">
                            <textarea id="additional_info" name="additional_info" class="form-control" rows="3">{{ old('additional_info') }}</textarea>
                            <small class="form-text text-muted">Enter in format "Title: Description" (one per line).</small>
                        </div>
                    </div>

                    <div class="item form-group">
                        <label class="col-form-label col-md-3 col-sm-3 label-align" for="age_restriction">
                            Age Restriction
                        </label>
                        <div class="col-md-6 col-sm-6">
                            <input type="text" id="age_restriction" name="age_restriction" class="form-control" value="{{ old('age_restriction') }}">
                            <small class="form-text text-muted">E.g., "18+", "All ages", etc.</small>
                        </div>
                    </div>

                    <!-- Settings -->
                    <h3 class="mt-4">Settings</h3>

                    <div class="item form-group">
                        <label class="col-form-label col-md-3 col-sm-3 label-align">
                            Featured Show
                        </label>
                        <div class="col-md-6 col-sm-6">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="is_featured" value="1" {{ old('is_featured') ? 'checked' : '' }}>
                                    Display this show in featured areas
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="item form-group">
                        <label class="col-form-label col-md-3 col-sm-3 label-align" for="status">
                            Status <span class="required">*</span>
                        </label>
                        <div class="col-md-6 col-sm-6">
                            <select id="status" name="status" required="required" class="form-control">
                                <option value="" disabled selected>Select Status</option>
                                <option value="upcoming" {{ old('status') == 'upcoming' ? 'selected' : '' }}>Upcoming</option>
                                <option value="ongoing" {{ old('status') == 'ongoing' ? 'selected' : '' }}>Ongoing</option>
                                <option value="past" {{ old('status') == 'past' ? 'selected' : '' }}>Past</option>
                                <option value="cancelled" {{ old('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                        </div>
                    </div>

                    <div class="item form-group">
                        <label class="col-form-label col-md-3 col-sm-3 label-align" for="is_active">
                            Active <span class="required">*</span>
                        </label>
                        <div class="col-md-6 col-sm-6">
                            <select id="is_active" name="is_active" required="required" class="form-control">
                                <option value="" disabled selected>Select option</option>
                                <option value="1" {{ old('is_active') == '1' ? 'selected' : '' }}>Active</option>
                                <option value="0" {{ old('is_active') == '0' ? 'selected' : '' }}>Inactive</option>
                            </select>
                            <small class="form-text text-muted">Inactive shows won't be visible on the website even if they're upcoming.</small>
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
    // Auto-generate slug from title
    document.getElementById('title').addEventListener('keyup', function() {
        const titleValue = this.value;
        const slugInput = document.getElementById('slug');

        // Only auto-generate if the slug field is empty or hasn't been manually edited
        if (!slugInput.value || slugInput._autoGenerated) {
            const slug = titleValue
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

    // Parse performers and additional info as JSON
    document.getElementById('show-form').addEventListener('submit', function(e) {
        const performersText = document.getElementById('performers').value;
        const additionalInfoText = document.getElementById('additional_info').value;

        // Convert performers to array
        if (performersText) {
            const performers = performersText.split('\n').filter(line => line.trim().length > 0);
            document.getElementById('performers').value = JSON.stringify(performers);
        }

        // Convert additional info to object
        if (additionalInfoText) {
            const additionalInfo = {};
            additionalInfoText.split('\n').forEach(line => {
                if (line.trim().length > 0) {
                    const parts = line.split(':');
                    if (parts.length >= 2) {
                        const key = parts[0].trim();
                        const value = parts.slice(1).join(':').trim();
                        additionalInfo[key] = value;
                    }
                }
            });
            document.getElementById('additional_info').value = JSON.stringify(additionalInfo);
        }
    });
</script>
@endsection
