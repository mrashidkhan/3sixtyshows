@extends('admin.layout.layout')

@section('content')

<div class="row">
    <div class="col-md-12 col-sm-12">
        <div class="x_panel">
            <div class="x_title">
                <h2>Add Ticket Type <small>Create a new ticket type for "{{ $show->title }}"</small></h2>
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

                <!-- Show Information Card -->
                <div class="row mb-4">
                    <div class="col-md-12">
                        <div class="card border-left-info">
                            <div class="card-body">
                                <h6 class="card-title">Creating ticket type for:</h6>
                                <p class="card-text">
                                    <strong>{{ $show->title }}</strong><br>
                                    <small class="text-muted">{{ $show->venue->name ?? 'N/A' }} • {{ $show->start_date->format('M d, Y H:i') }}</small>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <form id="ticket-type-form" action="{{ route('admin.ticket-types.store-for-show', $show) }}" class="form-horizontal form-label-left" method="post" novalidate>
                    @csrf

                    <!-- Basic Information -->
                    <h3>Basic Information</h3>
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
                            <a href="{{ route('admin.ticket-types.index', $show) }}" class="btn btn-primary">Cancel</a>
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
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Form validation
        const form = document.getElementById('ticket-type-form');
        const nameInput = document.getElementById('name');
        const priceInput = document.getElementById('price');

        form.addEventListener('submit', function(e) {
            let isValid = true;

            // Validate name
            if (!nameInput.value.trim()) {
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
    });
</script>
@endsection
