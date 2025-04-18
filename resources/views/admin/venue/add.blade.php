@extends('admin.layout.layout')

@section('content')

<div class="row">
    <div class="col-md-12 col-sm-12">
        <div class="x_panel">
            <div class="x_title">
                <h2>Add Venue <small>Create a new venue</small></h2>
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
                <form id="venue-form" action="{{ route('venue.store') }}" class="form-horizontal form-label-left" method="post" enctype="multipart/form-data" novalidate>
                    @csrf

                    <!-- Basic Information Section -->
                    <div class="x_title">
                        <h4>Basic Information</h4>
                        <div class="clearfix"></div>
                    </div>

                    <!-- Name Field -->
                    <div class="item form-group">
                        <label class="col-form-label col-md-3 col-sm-3 label-align" for="name">
                            Name <span class="required">*</span>
                        </label>
                        <div class="col-md-6 col-sm-6">
                            <input type="text" id="name" name="name" required="required" class="form-control" value="{{ old('name') }}">
                        </div>
                    </div>

                    <!-- Slug Field -->
                    <div class="item form-group">
                        <label class="col-form-label col-md-3 col-sm-3 label-align" for="slug">
                            Slug <span class="required">*</span>
                        </label>
                        <div class="col-md-6 col-sm-6">
                            <input type="text" id="slug" name="slug" required="required" class="form-control" value="{{ old('slug') }}">
                            <small class="form-text text-muted">The slug will be used in the URL. If left empty, it will be generated automatically from the name.</small>
                        </div>
                    </div>

                    <!-- Description Field -->
                    <div class="item form-group">
                        <label class="col-form-label col-md-3 col-sm-3 label-align" for="description">
                            Description <span class="required">*</span>
                        </label>
                        <div class="col-md-6 col-sm-6">
                            <textarea id="description" name="description" class="form-control" rows="4" required="required">{{ old('description') }}</textarea>
                        </div>
                    </div>

                    <!-- Capacity Field -->
                    <div class="item form-group">
                        <label class="col-form-label col-md-3 col-sm-3 label-align" for="capacity">
                            Capacity
                        </label>
                        <div class="col-md-6 col-sm-6">
                            <input type="number" id="capacity" name="capacity" class="form-control" value="{{ old('capacity') }}" min="0">
                            <small class="form-text text-muted">Maximum number of people the venue can accommodate</small>
                        </div>
                    </div>

                    <!-- Address Section -->
                    <div class="x_title">
                        <h4>Address Information</h4>
                        <div class="clearfix"></div>
                    </div>

                    <!-- Address Field -->
                    <div class="item form-group">
                        <label class="col-form-label col-md-3 col-sm-3 label-align" for="address">
                            Address <span class="required">*</span>
                        </label>
                        <div class="col-md-6 col-sm-6">
                            <input type="text" id="address" name="address" required="required" class="form-control" value="{{ old('address') }}">
                        </div>
                    </div>

                    <!-- City Field -->
                    <div class="item form-group">
                        <label class="col-form-label col-md-3 col-sm-3 label-align" for="city">
                            City <span class="required">*</span>
                        </label>
                        <div class="col-md-6 col-sm-6">
                            <input type="text" id="city" name="city" required="required" class="form-control" value="{{ old('city') }}">
                        </div>
                    </div>

                    <!-- State Field -->
                    <div class="item form-group">
                        <label class="col-form-label col-md-3 col-sm-3 label-align" for="state">
                            State
                        </label>
                        <div class="col-md-6 col-sm-6">
                            <input type="text" id="state" name="state" class="form-control" value="{{ old('state') }}">
                        </div>
                    </div>

                    <!-- Country Field -->
                    <div class="item form-group">
                        <label class="col-form-label col-md-3 col-sm-3 label-align" for="country">
                            Country <span class="required">*</span>
                        </label>
                        <div class="col-md-6 col-sm-6">
                            <input type="text" id="country" name="country" required="required" class="form-control" value="{{ old('country') }}">
                        </div>
                    </div>

                    <!-- Postal Code Field -->
                    <div class="item form-group">
                        <label class="col-form-label col-md-3 col-sm-3 label-align" for="postal_code">
                            Postal Code
                        </label>
                        <div class="col-md-6 col-sm-6">
                            <input type="text" id="postal_code" name="postal_code" class="form-control" value="{{ old('postal_code') }}">
                        </div>
                    </div>

                    <!-- Location Section -->
                    <div class="x_title">
                        <h4>Map Coordinates (Optional)</h4>
                        <div class="clearfix"></div>
                    </div>

                    <!-- Latitude Field -->
                    <div class="item form-group">
                        <label class="col-form-label col-md-3 col-sm-3 label-align" for="latitude">
                            Latitude
                        </label>
                        <div class="col-md-6 col-sm-6">
                            <input type="text" id="latitude" name="latitude" class="form-control" value="{{ old('latitude') }}" placeholder="e.g. 40.7128">
                            <small class="form-text text-muted">Used for map placement (decimal format)</small>
                        </div>
                    </div>

                    <!-- Longitude Field -->
                    <div class="item form-group">
                        <label class="col-form-label col-md-3 col-sm-3 label-align" for="longitude">
                            Longitude
                        </label>
                        <div class="col-md-6 col-sm-6">
                            <input type="text" id="longitude" name="longitude" class="form-control" value="{{ old('longitude') }}" placeholder="e.g. -74.0060">
                            <small class="form-text text-muted">Used for map placement (decimal format)</small>
                        </div>
                    </div>

                    <!-- Contact Section -->
                    <div class="x_title">
                        <h4>Contact Information</h4>
                        <div class="clearfix"></div>
                    </div>

                    <!-- Contact Email Field -->
                    <div class="item form-group">
                        <label class="col-form-label col-md-3 col-sm-3 label-align" for="contact_email">
                            Email
                        </label>
                        <div class="col-md-6 col-sm-6">
                            <input type="email" id="contact_email" name="contact_email" class="form-control" value="{{ old('contact_email') }}">
                        </div>
                    </div>

                    <!-- Contact Phone Field -->
                    <div class="item form-group">
                        <label class="col-form-label col-md-3 col-sm-3 label-align" for="contact_phone">
                            Phone
                        </label>
                        <div class="col-md-6 col-sm-6">
                            <input type="text" id="contact_phone" name="contact_phone" class="form-control" value="{{ old('contact_phone') }}">
                        </div>
                    </div>

                    <!-- Website Field -->
                    <div class="item form-group">
                        <label class="col-form-label col-md-3 col-sm-3 label-align" for="website">
                            Website
                        </label>
                        <div class="col-md-6 col-sm-6">
                            <input type="url" id="website" name="website" class="form-control" value="{{ old('website') }}" placeholder="https://example.com">
                        </div>
                    </div>

                    <div class="ln_solid"></div>

                    <!-- Submit Buttons -->
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
