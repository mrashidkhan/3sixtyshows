@extends('admin.layout.layout')

@section('content')

<div class="row">
    <div class="col-md-12 col-sm-12">
        <div class="x_panel">
            <div class="x_title">
                <h2>Edit Ticket Type <small>Update ticket type "{{ $ticketType->name }}"</small></h2>
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
                                <h6 class="card-title">Editing ticket type for:</h6>
                                <p class="card-text">
                                    <strong>{{ $show->title }}</strong><br>
                                    <small class="text-muted">{{ $show->venue->name ?? 'N/A' }} • {{ $show->start_date->format('M d, Y H:i') }}</small>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <form id="ticket-type-form" action="{{ route('admin.ticket-types.update', $ticketType) }}" class="form-horizontal form-label-left" method="post" novalidate>
                    @csrf
                    @method('PUT')

                    <!-- Basic Information -->
                    <div class="x_title">
                        <h4>Basic Information</h4>
                        <div class="clearfix"></div>
                    </div>

                    <div class="item form-group">
                        <label class="col-form-label col-md-3 col-sm-3 label-align" for="name">
                            Ticket Type Name <span class="required">*</span>
                        </label>
                        <div class="col-md-6 col-sm-6">
                            <input type="text" id="name" name="name" required="required" class="form-control" value="{{ old('name', $ticketType->name) }}" placeholder="e.g., General Admission, VIP, Early Bird">
                        </div>
                    </div>

                    <div class="item form-group">
                        <label class="col-form-label col-md-3 col-sm-3 label-align" for="description">
                            Description
                        </label>
                        <div class="col-md-6 col-sm-6">
                            <textarea id="description" name="description" class="form-control" rows="3" placeholder="Brief description of what this ticket type includes">{{ old('description', $ticketType->description) }}</textarea>
                        </div>
                    </div>

                    <!-- Pricing and Capacity -->
                    <div class="x_title">
                        <h4>Pricing and Capacity</h4>
                        <div class="clearfix"></div>
                    </div>

                    <div class="item form-group">
                        <label class="col-form-label col-md-3 col-sm-3 label-align" for="price">
                            Price <span class="required">*</span>
                        </label>
                        <div class="col-md-6 col-sm-6">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">$</span>
                                </div>
                                <input type="number" id="price" name="price" required="required" class="form-control" step="0.01" min="0" value="{{ old('price', $ticketType->price) }}" placeholder="0.00">
                            </div>
                            <small class="form-text text-muted">Set to 0 for free tickets.</small>
                        </div>
                    </div>

                    <div class="item form-group">
                        <label class="col-form-label col-md-3 col-sm-3 label-align" for="capacity">
                            Capacity
                        </label>
                        <div class="col-md-6 col-sm-6">
                            <input type="number" id="capacity" name="capacity" class="form-control" min="1" value="{{ old('capacity', $ticketType->capacity) }}" placeholder="Leave empty for unlimited">
                            <small class="form-text text-muted">Maximum number of tickets available for this type. Leave empty for unlimited capacity.</small>
                        </div>
                    </div>

                    <!-- Current Sales Information -->
                    <div class="item form-group">
                        <label class="col-form-label col-md-3 col-sm-3 label-align">
                            Current Sales
                        </label>
                        <div class="col-md-6 col-sm-6">
                            <div class="form-control-static">
                                @php
                                    $soldTickets = $ticketType->tickets()->count();
                                    $availableTickets = $ticketType->capacity ? ($ticketType->capacity - $soldTickets) : 'Unlimited';
                                @endphp
                                <strong>Sold:</strong> {{ $soldTickets }} tickets<br>
                                <strong>Available:</strong> {{ $availableTickets }} tickets
                                @if($ticketType->capacity && $soldTickets > 0)
                                    <br><small class="text-warning"><i class="fa fa-warning"></i> Reducing capacity below {{ $soldTickets }} will affect already sold tickets.</small>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Settings -->
                    <div class="x_title">
                        <h4>Settings</h4>
                        <div class="clearfix"></div>
                    </div>

                    <div class="item form-group">
                        <label class="col-form-label col-md-3 col-sm-3 label-align" for="display_order">
                            Display Order
                        </label>
                        <div class="col-md-6 col-sm-6">
                            <input type="number" id="display_order" name="display_order" class="form-control" min="0" value="{{ old('display_order', $ticketType->display_order ?? 0) }}">
                            <small class="form-text text-muted">Order in which this ticket type appears (0 = first, higher numbers appear later).</small>
                        </div>
                    </div>

                    <div class="item form-group">
                        <label class="col-form-label col-md-3 col-sm-3 label-align" for="is_active">
                            Active Status <span class="required">*</span>
                        </label>
                        <div class="col-md-6 col-sm-6">
                            <select id="is_active" name="is_active" required="required" class="form-control">
                                <option value="1" {{ old('is_active', $ticketType->is_active) == 1 ? 'selected' : '' }}>Active</option>
                                <option value="0" {{ old('is_active', $ticketType->is_active) == 0 ? 'selected' : '' }}>Inactive</option>
                            </select>
                            <small class="form-text text-muted">Inactive ticket types won't be visible to customers.</small>
                        </div>
                    </div>

                    <!-- Timestamp Information -->
                    <div class="x_title">
                        <h4>Record Information</h4>
                        <div class="clearfix"></div>
                    </div>

                    <div class="item form-group">
                        <label class="col-form-label col-md-3 col-sm-3 label-align">
                            Created
                        </label>
                        <div class="col-md-6 col-sm-6">
                            <p class="form-control-static">{{ $ticketType->created_at->format('F d, Y h:i A') }}</p>
                        </div>
                    </div>

                    <div class="item form-group">
                        <label class="col-form-label col-md-3 col-sm-3 label-align">
                            Last Updated
                        </label>
                        <div class="col-md-6 col-sm-6">
                            <p class="form-control-static">{{ $ticketType->updated_at->format('F d, Y h:i A') }}</p>
                        </div>
                    </div>

                    <div class="ln_solid"></div>

                    <div class="form-group">
                        <div class="col-md-6 col-sm-6 offset-md-3">
                            <a href="{{ route('admin.ticket-types.index', $show) }}" class="btn btn-primary">Cancel</a>
                            <button type="reset" class="btn btn-secondary">Reset</button>
                            <button type="submit" class="btn btn-success">Update Ticket Type</button>
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
        const capacityInput = document.getElementById('capacity');

        form.addEventListener('submit', function(e) {
            let isValid = true;

            // Validate name
            if (!nameInput.value.trim()) {
                alert('Please enter a ticket type name.');
                nameInput.focus();
                isValid = false;
            }

            // Validate price
            if (!priceInput.value || parseFloat(priceInput.value) < 0) {
                alert('Please enter a valid price (0 or greater).');
                priceInput.focus();
                isValid = false;
            }

            // Validate capacity against sold tickets
            const soldTickets = {{ $ticketType->tickets()->count() }};
            if (capacityInput.value && parseInt(capacityInput.value) < soldTickets) {
                if (!confirm(`Warning: You are trying to set capacity to ${capacityInput.value} but ${soldTickets} tickets have already been sold. This may cause issues. Do you want to continue?`)) {
                    capacityInput.focus();
                    isValid = false;
                }
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
