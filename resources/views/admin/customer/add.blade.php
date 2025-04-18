@extends('admin.layout.layout')

@section('content')

<div class="row">
    <div class="col-md-12 col-sm-12">
        <div class="x_panel">
            <div class="x_title">
                <h2>Add Customer <small>Create a new customer</small></h2>
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
                <form id="customer-form" action="{{ route('customer.store') }}" class="form-horizontal form-label-left" method="post" novalidate>
                    @csrf

                    <!-- Basic Information -->
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

                    <!-- Email Field -->
                    <div class="item form-group">
                        <label class="col-form-label col-md-3 col-sm-3 label-align" for="email">
                            Email <span class="required">*</span>
                        </label>
                        <div class="col-md-6 col-sm-6">
                            <input type="email" id="email" name="email" required="required" class="form-control" value="{{ old('email') }}">
                        </div>
                    </div>

                    <!-- Phone Field -->
                    <div class="item form-group">
                        <label class="col-form-label col-md-3 col-sm-3 label-align" for="phone">
                            Phone
                        </label>
                        <div class="col-md-6 col-sm-6">
                            <input type="text" id="phone" name="phone" class="form-control" value="{{ old('phone') }}">
                        </div>
                    </div>

                    <!-- Password Field -->
                    <div class="item form-group">
                        <label class="col-form-label col-md-3 col-sm-3 label-align" for="password">
                            Password <span class="required">*</span>
                        </label>
                        <div class="col-md-6 col-sm-6">
                            <input type="password" id="password" name="password" required="required" class="form-control">
                        </div>
                    </div>

                    <!-- Password Confirmation Field -->
                    <div class="item form-group">
                        <label class="col-form-label col-md-3 col-sm-3 label-align" for="password_confirmation">
                            Confirm Password <span class="required">*</span>
                        </label>
                        <div class="col-md-6 col-sm-6">
                            <input type="password" id="password_confirmation" name="password_confirmation" required="required" class="form-control">
                        </div>
                    </div>

                    <!-- Address Information -->
                    <div class="x_title">
                        <h4>Address Information</h4>
                        <div class="clearfix"></div>
                    </div>

                    <!-- Address Field -->
                    <div class="item form-group">
                        <label class="col-form-label col-md-3 col-sm-3 label-align" for="address">
                            Address
                        </label>
                        <div class="col-md-6 col-sm-6">
                            <input type="text" id="address" name="address" class="form-control" value="{{ old('address') }}">
                        </div>
                    </div>

                    <!-- City Field -->
                    <div class="item form-group">
                        <label class="col-form-label col-md-3 col-sm-3 label-align" for="city">
                            City
                        </label>
                        <div class="col-md-6 col-sm-6">
                            <input type="text" id="city" name="city" class="form-control" value="{{ old('city') }}">
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
                            Country
                        </label>
                        <div class="col-md-6 col-sm-6">
                            <input type="text" id="country" name="country" class="form-control" value="{{ old('country') }}">
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
