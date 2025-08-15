@extends('admin.layout.layout')

@section('content')

<div class="row">
    <div class="col-md-12 col-sm-12">
        <div class="x_panel">
            <div class="x_title">
                <h2>Add Ticket Type <small>Create a new ticket type</small></h2>
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

                <form id="ticket-type-form" action="{{ route('admin.ticket-types.store') }}" class="form-horizontal form-label-left" method="post" novalidate>
                    @csrf

                    <!-- Show Selection -->
                    <h3>Show Selection</h3>
                    <div class="item form-group">
                        <label class="col-form-label col-md-3 col-sm-3 label-align" for="show_id">
                            Select Show <span class="required">*</span>
                        </label>
                        <div class="col-md-6 col-sm-6">
                            <select id="show_id" name="show_id" required="required" class="form-control select2-show">
                                <option value="" disabled selected>Choose a show...</option>
                                @foreach($shows as $show)
                                    <option value="{{ $show->id }}" {{ old('show_id') == $show->id ? 'selected' : '' }}
                                            data-title="{{ $show->title }}"
                                            data-venue="{{ $show->venue->name ?? 'No Venue' }}"
                                            data-date="{{ $show->start_date->format('M d, Y H:i') }}">
                                        {{ $show->title }} - {{ $show->venue->name ?? 'No Venue' }} ({{ $show->start_date->format('M d, Y') }})
                                    </option>
                                @endforeach
                            </select>
                            <small class="form-text text-muted">Type to search for shows by title or venue name.</small>
                        </div>
                    </div>

                    <!-- Selected Show Info -->
                    <div id="selected-show-info" class="item form-group" style="display: none;">
                        <label class="col-form-label col-md-3 col-sm-3 label-align">
                            Selected Show
                        </label>
                        <div class="col-md-6 col-sm-6">
                            <div class="card border-left-info">
                                <div class="card-body">
                                    <h6 class="card-title" id="show-title"></h6>
                                    <p class="card-text">
                                        <small class="text-muted">
                                            <span id="show-venue"></span> • <span id="show-date"></span>
                                        </small>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Basic Information -->
                    <h3 class="mt-4">Ticket Type Information</h3>
                    <div class="item form-group">
                        <label class="col-form-label col-md-3 col-sm-3 label-align" for="name">
                            Ticket Type Name <span class="required">*</span>
                        </label>
                        <div class="col-md-6 col-sm-6">
                            <input type="text" id="name" name="name" required="required" class="form-control" value="{{ old('name') }}" placeholder="e.g., General Admission, VIP, Early Bird">
                        </div>
                    </div>

                    <div class="item form-group">
                        <label class="col-form-label col-md-3 col-sm-3 label-align" for="description">
                            Description
                        </label>
                        <div class="col-md-6 col-sm-6">
                            <textarea id="description" name="description" class="form-control" rows="3" placeholder="Brief description of what this ticket type includes">{{ old('description') }}</textarea>
                        </div>
                    </div>

                    <!-- Pricing and Capacity -->
                    <h3 class="mt-4">Pricing and Capacity</h3>
                    <div class="item form-group">
                        <label class="col-form-label col-md-3 col-sm-3 label-align" for="price">
                            Price <span class="required">*</span>
                        </label>
                        <div class="col-md-6 col-sm-6">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">$</span>
                                </div>
                                <input type="number" id="price" name="price" required="required" class="form-control" step="0.01" min="0" value="{{ old('price') }}" placeholder="0.00">
                            </div>
                            <small class="form-text text-muted">Set to 0 for free tickets.</small>
                        </div>
                    </div>

                    <div class="item form-group">
                        <label class="col-form-label col-md-3 col-sm-3 label-align" for="capacity">
                            Capacity
                        </label>
                        <div class="col-md-6 col-sm-6">
                            <input type="number" id="capacity" name="capacity" class="form-control" min="1" value="{{ old('capacity') }}" placeholder="Leave empty for unlimited">
                            <small class="form-text text-muted">Maximum number of tickets available for this type. Leave empty for unlimited capacity.</small>
                        </div>
                    </div>

                    <!-- Settings -->
                    <h3 class="mt-4">Settings</h3>
                    <div class="item form-group">
                        <label class="col-form-label col-md-3 col-sm-3 label-align" for="display_order">
                            Display Order
                        </label>
                        <div class="col-md-6 col-sm-6">
                            <input type="number" id="display_order" name="display_order" class="form-control" min="0" value="{{ old('display_order', 0) }}">
                            <small class="form-text text-muted">Order in which this ticket type appears (0 = first, higher numbers appear later).</small>
                        </div>
                    </div>

                    <div class="item form-group">
                        <label class="col-form-label col-md-3 col-sm-3 label-align">
                            Active Status
                        </label>
                        <div class="col-md-6 col-sm-6">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                                    Make this ticket type available for sale
                                </label>
                            </div>
                            <small class="form-text text-muted">Inactive ticket types won't be visible to customers.</small>
                        </div>
                    </div>

                    <div class="ln_solid"></div>

                    <div class="form-group">
                        <div class="col-md-6 col-sm-6 offset-md-3">
                            <a href="{{ route('admin.ticket-types.all') }}" class="btn btn-primary">Cancel</a>
                            <button type="reset" class="btn btn-secondary">Reset</button>
                            <button type="submit" class="btn btn-success">Create Ticket Type</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<!-- Include Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<!-- Include Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize Select2 for show selection
        $('.select2-show').select2({
            placeholder: 'Search for a show by title or venue...',
            allowClear: true,
            width: '100%',
            ajax: {
                url: "{{ route('admin.ticket-types.search-shows') }}",
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        q: params.term,
                        page: params.page
                    };
                },
                processResults: function (data, params) {
                    return {
                        results: data.results,
                        pagination: data.pagination
                    };
                },
                cache: true
            },
            minimumInputLength: 0,
            templateResult: function(data) {
                if (data.loading) return data.text;
                if (!data.title) return data.text;

                return $('<div>' +
                    '<div><strong>' + data.title + '</strong></div>' +
                    '<div><small class="text-muted">' + data.venue + ' • ' + data.date + '</small></div>' +
                    '</div>');
            },
            templateSelection: function(data) {
                return data.title || data.text;
            }
        });

        // Show selected show information
        $('#show_id').on('change', function() {
            const selectedOption = $(this).find('option:selected');
            if (selectedOption.val()) {
                $('#show-title').text(selectedOption.data('title'));
                $('#show-venue').text(selectedOption.data('venue'));
                $('#show-date').text(selectedOption.data('date'));
                $('#selected-show-info').show();
            } else {
                $('#selected-show-info').hide();
            }
        });

        // Form validation
        const form = document.getElementById('ticket-type-form');
        const showSelect = document.getElementById('show_id');
        const nameInput = document.getElementById('name');
        const priceInput = document.getElementById('price');

        form.addEventListener('submit', function(e) {
            let isValid = true;

            // Validate show selection
            if (!showSelect.value) {
                alert('Please select a show.');
                showSelect.focus();
                isValid = false;
            }

            // Validate name
            else if (!nameInput.value.trim()) {
                alert('Please enter a ticket type name.');
                nameInput.focus();
                isValid = false;
            }

            // Validate price
            else if (!priceInput.value || parseFloat(priceInput.value) < 0) {
                alert('Please enter a valid price (0 or greater).');
                priceInput.focus();
                isValid = false;
            }

            if (!isValid) {
                e.preventDefault();
                return false;
            }
        });

        // Auto-format price input
        priceInput.addEventListener('blur', function() {
            if (this.value && !isNaN(this.value)) {
                this.value = parseFloat(this.value).toFixed(2);
            }
        });

        // Reset form functionality
        document.querySelector('button[type="reset"]').addEventListener('click', function() {
            $('.select2-show').val(null).trigger('change');
            $('#selected-show-info').hide();
        });
    });
</script>
@endsection
