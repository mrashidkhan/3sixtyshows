@extends('admin.layout.layout')

@section('content')
<br><br><br>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h2 style="color: #BE9336">Are you sure you want to delete the coupon "{{ $coupon->coupon_name }}" with a discount of Rs {{ number_format($coupon->coupon_discount, 2) }}?</h2>
                </div>
                <div class="card-body">
                    <form method="post" action="{{ route('coupon.delete', $coupon->id) }}">
                        @csrf
                        @method('POST')
                        <button type="submit" class="btn btn-danger">Yes, delete</button>
                        <a href="{{ route('coupon.list') }}" class="btn btn-secondary">Cancel</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
