@extends('admin.layout.layout')

@section('content')

<script>
    // Immediately hide/show the redirect URL field based on checkbox state
    document.addEventListener('DOMContentLoaded', function() {
      // Set initial state on page load
      setTimeout(function() {
        var checkbox = document.getElementById('redirect-checkbox');
        var container = document.getElementById('redirect-url-container');
        if (checkbox && container) {
          container.style.display = checkbox.checked ? 'block' : 'none';
          console.log('Initial redirect setup - Checkbox checked:', checkbox.checked);
        }
      }, 100); // Small delay to ensure elements are loaded
    });
    </script>

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

                    <!-- Redirect Options (New Section) -->
                    <div class="x_title">
                        <h4>Redirect Settings</h4>
                        <div class="clearfix"></div>
                    </div>

                    <div class="item form-group">
                        <label class="col-form-label col-md-3 col-sm-3 label-align" for="redirect">
                            Enable Redirect
                        </label>
                        <div class="col-md-6 col-sm-6">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" id="redirect-checkbox" name="redirect" value="1"
    {{ old('redirect') ? 'checked' : '' }}
    onclick="document.getElementById('redirect-url-container').style.display = this.checked ? 'block' : 'none';">
                                    Redirect users to external URL when clicking on this show
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="item form-group redirect-url-group" id="redirect-url-container">
                        <label class="col-form-label col-md-3 col-sm-3 label-align" for="redirect_url">
                            Redirect URL
                        </label>
                        <div class="col-md-6 col-sm-6">
                            <input type="url" id="redirect_url" name="redirect_url" class="form-control" value="{{ old('redirect_url') }}">
                            <small class="form-text text-muted">Enter the full URL including http:// or https://</small>
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
    // Wait for DOM to be fully loaded
    window.addEventListener('DOMContentLoaded', function() {
        console.log('DOM loaded, initializing scripts...');

        // Auto-generate slug from title
        var titleInput = document.getElementById('title');
        var slugInput = document.getElementById('slug');

        if (titleInput && slugInput) {
            titleInput.addEventListener('keyup', function() {
                if (!slugInput.value || slugInput._autoGenerated) {
                    var slug = this.value
                        .toLowerCase()
                        .replace(/[^\w\s-]/g, '')
                        .replace(/\s+/g, '-')
                        .replace(/--+/g, '-')
                        .trim();

                    slugInput.value = slug;
                    slugInput._autoGenerated = true;
                }
            });

            slugInput.addEventListener('input', function() {
                this._autoGenerated = false;
            });
        }

        // Redirect URL toggling - Direct DOM manipulation approach
        var redirectCheckbox = document.getElementById('redirect-checkbox');
        var redirectUrlGroup = document.querySelector('.redirect-url-group');
        var redirectUrlInput = document.getElementById('redirect_url');

        if (redirectCheckbox && redirectUrlGroup && redirectUrlInput) {
            console.log('Found redirect elements, setting up toggle...');

            // Force initial state
            redirectUrlGroup.style.display = redirectCheckbox.checked ? 'block' : 'none';

            // Add listener using inline function for simplicity
            redirectCheckbox.addEventListener('click', function() {
                console.log('Checkbox clicked, checked:', this.checked);
                redirectUrlGroup.style.display = this.checked ? 'block' : 'none';

                if (!this.checked) {
                    redirectUrlInput.value = '';
                }
            });
        } else {
            console.error('Could not find redirect elements!');
            console.log('Checkbox:', redirectCheckbox);
            console.log('URL group:', redirectUrlGroup);
            console.log('URL input:', redirectUrlInput);
        }

        // Form submission - Parse performers and additional info as JSON
        var form = document.getElementById('show-form');
        var performersInput = document.getElementById('performers');
        var additionalInfoInput = document.getElementById('additional_info');

        if (form && performersInput && additionalInfoInput) {
            form.addEventListener('submit', function(e) {
                // if (performersInput.value) {
                //     var performers = performersInput.value.split('\n').filter(function(line) {
                //         return line.trim().length > 0;
                //     });
                //     performersInput.value = JSON.stringify(performers);
                // }

                if (additionalInfoInput.value) {
                    var additionalInfo = {};
                    additionalInfoInput.value.split('\n').forEach(function(line) {
                        if (line.trim().length > 0) {
                            var parts = line.split(':');
                            if (parts.length >= 2) {
                                var key = parts[0].trim();
                                var value = parts.slice(1).join(':').trim();
                                additionalInfo[key] = value;
                            }
                        }
                    });
                    additionalInfoInput.value = JSON.stringify(additionalInfo);
                }
            });
        }
    });
</script>
@endsection
