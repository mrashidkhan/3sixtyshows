@extends('admin.layout.layout')

@section('content')
<br><br><br>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h2 style="color: #BE9336">Delete Customer</h2>
                </div>
                <div class="card-body">
                    <p>Are you sure you want to delete the customer <strong>{{ $customer->name }}</strong> ({{ $customer->email }})?</p>

                    @if($customer->bookings->count() > 0 || $customer->tickets->count() > 0)
                        <div class="alert alert-warning">
                            <strong>Warning:</strong> This customer has
                            @if($customer->bookings->count() > 0)
                                {{ $customer->bookings->count() }} booking(s)
                            @endif
                            @if($customer->bookings->count() > 0 && $customer->tickets->count() > 0)
                                and
                            @endif
                            @if($customer->tickets->count() > 0)
                                {{ $customer->tickets->count() }} ticket(s)
                            @endif
                            associated with their account. Deleting this customer will also remove all related records.
                        </div>
                    @endif

                    <form method="post" action="{{ route('customer.destroy', $customer->id) }}">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Yes, delete</button>
                        <a href="{{ route('customer.index') }}" class="btn btn-secondary">Cancel</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
