@extends('admin.layout.layout')

@section('content')

<div class="row">
    <div class="col-md-12 col-sm-12">
        <div class="x_panel">
            <div class="x_title">
                <h2>Show Details <small>View show information</small></h2>
                <div class="clearfix"></div>
            </div>
            <div class="x_content">
                <br>

                <!-- Action Buttons -->
                <div class="text-right mb-4">
                    <a href="{{ route('show.edit', $show->id) }}" class="btn btn-primary btn-sm">
                        <i class="fa fa-edit"></i> Edit Show
                    </a>
                    <a href="{{ route('show.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fa fa-arrow-left"></i> Back to List
                    </a>
                </div>

                <!-- Basic Info Section -->
                <div class="x_title">
                    <h4>Basic Information</h4>
                    <div class="clearfix"></div>
                </div>

                <!-- Title Field -->
                <div class="item form-group">
                    <label class="col-form-label col-md-3 col-sm-3 label-align">
                        Title
                    </label>
                    <div class="col-md-6 col-sm-6">
                        <p class="form-control-static">{{ $show->title }}</p>
                    </div>
                </div>

                <!-- Slug Field -->
                <div class="item form-group">
                    <label class="col-form-label col-md-3 col-sm-3 label-align">
                        Slug
                    </label>
                    <div class="col-md-6 col-sm-6">
                        <p class="form-control-static">{{ $show->slug }}</p>
                    </div>
                </div>

                <!-- Category Field -->
                <div class="item form-group">
                    <label class="col-form-label col-md-3 col-sm-3 label-align">
                        Category
                    </label>
                    <div class="col-md-6 col-sm-6">
                        <p class="form-control-static">{{ $show->category->name ?? 'N/A' }}</p>
                    </div>
                </div>

                <!-- Venue Field -->
                <div class="item form-group">
                    <label class="col-form-label col-md-3 col-sm-3 label-align">
                        Venue
                    </label>
                    <div class="col-md-6 col-sm-6">
                        <p class="form-control-static">{{ $show->venue->name ?? 'N/A' }}</p>
                    </div>
                </div>

                <!-- Short Description Field -->
                <div class="item form-group">
                    <label class="col-form-label col-md-3 col-sm-3 label-align">
                        Short Description
                    </label>
                    <div class="col-md-6 col-sm-6">
                        <p class="form-control-static">{{ $show->short_description }}</p>
                    </div>
                </div>

                <!-- Full Description Field -->
                <div class="item form-group">
                    <label class="col-form-label col-md-3 col-sm-3 label-align">
                        Full Description
                    </label>
                    <div class="col-md-6 col-sm-6">
                        <div class="form-control-static">
                            {!! nl2br(e($show->description)) !!}
                        </div>
                    </div>
                </div>

                <!-- Featured Image Field -->
                <div class="item form-group">
                    <label class="col-form-label col-md-3 col-sm-3 label-align">
                        Featured Image
                    </label>
                    <div class="col-md-6 col-sm-6">
                        @if($show->featured_image)
                            <img src="{{ asset('storage/' . $show->featured_image) }}" alt="{{ $show->title }}"
                                 style="max-width: 100%; max-height: 300px; border: 1px solid #ddd; padding: 3px;">
                        @else
                            <p class="form-control-static">No image available</p>
                        @endif
                    </div>
                </div>

                <!-- Date & Time Section -->
                <div class="x_title">
                    <h4>Date & Time</h4>
                    <div class="clearfix"></div>
                </div>

                <!-- Start Date Field -->
                <div class="item form-group">
                    <label class="col-form-label col-md-3 col-sm-3 label-align">
                        Start Date & Time
                    </label>
                    <div class="col-md-6 col-sm-6">
                        <p class="form-control-static">{{ $show->start_date->format('F d, Y h:i A') }}</p>
                    </div>
                </div>

                <!-- End Date Field -->
                <div class="item form-group">
                    <label class="col-form-label col-md-3 col-sm-3 label-align">
                        End Date & Time
                    </label>
                    <div class="col-md-6 col-sm-6">
                        <p class="form-control-static">{{ $show->end_date ? $show->end_date->format('F d, Y h:i A') : 'N/A' }}</p>
                    </div>
                </div>

                <!-- Duration Field -->
                <div class="item form-group">
                    <label class="col-form-label col-md-3 col-sm-3 label-align">
                        Duration
                    </label>
                    <div class="col-md-6 col-sm-6">
                        <p class="form-control-static">{{ $show->duration ?? 'N/A' }}</p>
                    </div>
                </div>

                <!-- Ticket Info Section -->
                <div class="x_title">
                    <h4>Ticket Information</h4>
                    <div class="clearfix"></div>
                </div>

                <!-- Price Field -->
                <div class="item form-group">
                    <label class="col-form-label col-md-3 col-sm-3 label-align">
                        Price
                    </label>
                    <div class="col-md-6 col-sm-6">
                        <p class="form-control-static">{{ $show->formatted_price }}</p>
                    </div>
                </div>

                <!-- Available Tickets Field -->
                <div class="item form-group">
                    <label class="col-form-label col-md-3 col-sm-3 label-align">
                        Available Tickets
                    </label>
                    <div class="col-md-6 col-sm-6">
                        <p class="form-control-static">{{ $show->available_tickets ?? 'Unlimited' }}</p>
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
                    <label class="col-form-label col-md-3 col-sm-3 label-align">
                        Performers/Artists
                    </label>
                    <div class="col-md-6 col-sm-6">
                        @if(is_array($show->performers) && count($show->performers) > 0)
                            <ul class="list-unstyled">
                                @foreach($show->performers as $performer)
                                    <li>{{ $performer }}</li>
                                @endforeach
                            </ul>
                        @elseif(is_string($show->performers) && !empty($show->performers))
                            <p class="form-control-static">{{ $show->performers }}</p>
                        @else
                            <p class="form-control-static">No performers listed</p>
                        @endif
                    </div>
                </div>

                <!-- Additional Info Field -->
                <div class="item form-group">
                    <label class="col-form-label col-md-3 col-sm-3 label-align">
                        Additional Information
                    </label>
                    <div class="col-md-6 col-sm-6">
                        @if(is_array($show->additional_info) && count($show->additional_info) > 0)
                            <dl>
                                @foreach($show->additional_info as $key => $value)
                                    <dt>{{ $key }}</dt>
                                    <dd>{{ $value }}</dd>
                                @endforeach
                            </dl>
                        @else
                            <p class="form-control-static">No additional information</p>
                        @endif
                    </div>
                </div>

                <!-- Age Restriction Field -->
                <div class="item form-group">
                    <label class="col-form-label col-md-3 col-sm-3 label-align">
                        Age Restriction
                    </label>
                    <div class="col-md-6 col-sm-6">
                        <p class="form-control-static">{{ $show->age_restriction ?? 'None' }}</p>
                    </div>
                </div>

                <!-- Settings Section -->
                <div class="x_title">
                    <h4>Settings</h4>
                    <div class="clearfix"></div>
                </div>

                <!-- Featured Field -->
                <div class="item form-group">
                    <label class="col-form-label col-md-3 col-sm-3 label-align">
                        Featured
                    </label>
                    <div class="col-md-6 col-sm-6">
                        <p class="form-control-static">
                            @if($show->is_featured)
                                <span class="badge badge-success">Yes</span>
                            @else
                                <span class="badge badge-secondary">No</span>
                            @endif
                        </p>
                    </div>
                </div>

                <!-- Status Field -->
                <div class="item form-group">
                    <label class="col-form-label col-md-3 col-sm-3 label-align">
                        Status
                    </label>
                    <div class="col-md-6 col-sm-6">
                        <p class="form-control-static">
                            @if($show->status == 'upcoming')
                                <span class="badge badge-info">Upcoming</span>
                            @elseif($show->status == 'ongoing')
                                <span class="badge badge-success">Ongoing</span>
                            @elseif($show->status == 'past')
                                <span class="badge badge-secondary">Past</span>
                            @elseif($show->status == 'cancelled')
                                <span class="badge badge-danger">Cancelled</span>
                            @endif
                        </p>
                    </div>
                </div>

                <!-- Active Field -->
                <div class="item form-group">
                    <label class="col-form-label col-md-3 col-sm-3 label-align">
                        Active
                    </label>
                    <div class="col-md-6 col-sm-6">
                        <p class="form-control-static">
                            @if($show->is_active)
                                <span class="badge badge-success">Yes</span>
                            @else
                                <span class="badge badge-danger">No</span>
                            @endif
                        </p>
                    </div>
                </div>

                <!-- Redirect Settings Section -->
                <div class="x_title">
                    <h4>Redirect Settings</h4>
                    <div class="clearfix"></div>
                </div>

                <!-- Redirect Field -->
                <div class="item form-group">
                    <label class="col-form-label col-md-3 col-sm-3 label-align">
                        Redirect Enabled
                    </label>
                    <div class="col-md-6 col-sm-6">
                        <p class="form-control-static">
                            @if($show->redirect)
                                <span class="badge badge-success">Yes</span>
                            @else
                                <span class="badge badge-secondary">No</span>
                            @endif
                        </p>
                    </div>
                </div>

                <!-- Redirect URL Field -->
                @if($show->redirect && $show->redirect_url)
                <div class="item form-group">
                    <label class="col-form-label col-md-3 col-sm-3 label-align">
                        Redirect URL
                    </label>
                    <div class="col-md-6 col-sm-6">
                        <p class="form-control-static">
                            <a href="{{ $show->redirect_url }}" target="_blank">{{ $show->redirect_url }}</a>
                        </p>
                    </div>
                </div>
                @endif

                <div class="ln_solid"></div>

                <!-- Action Buttons -->
                <div class="form-group">
                    <div class="col-md-6 offset-md-3">
                        <a href="{{ route('show.index') }}" class="btn btn-secondary">Back to List</a>
                        <a href="{{ route('show.edit', $show->id) }}" class="btn btn-primary">Edit</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
