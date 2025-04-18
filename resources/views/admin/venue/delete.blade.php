@extends('admin.layout.layout')

@section('content')
<br><br><br>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h2 style="color: #1ABB9C">Are you sure you want to delete Product"{{ $product->product_name}}"?</h2>
                </div>
                <div class="card-body">
                    <form method="post">
                        @csrf
                        @method('POST')
                        <button type="submit" class="btn btn-danger">Yes, delete</button>
                        <a href="{{ route('product.list') }}" class="btn btn-secondary">Cancel</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


