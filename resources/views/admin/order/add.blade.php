@extends('admin.layout.layout')

@section('content')

<div class="container">
    <h1>Create Discount</h1>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('discount.store') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="product_id">Product</label>
            <select name="product_id" id="product_id" class="form-control" required>
                <option value="">Select a product</option>
                @foreach($products as $product)
                    <option value="{{ $product->id }}">{{ $product->product_name }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label for="weight">Weight</label>
            <input type="text" name="weight" id="weight" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="original_price">Original Price</label>
            <input type="number" name="original_price" id="original_price" class="form-control" step="0.01" required>
        </div>

        <div class="form-group">
            <label for="discounted_price">Discounted Price</label>
            <input type="number" name="discounted_price" id="discounted_price" class="form-control" step="0.01" required>
        </div>

        <div class="form-group">
            <label for="is_most_bought">Is Most Bought?</label>
            <select name="is_most_bought" id="is_most_bought" class="form-control" required>
                <option value="0">No</option>
                <option value="1">Yes</option>
            </select>
        </div>

        <button type="submit" class="btn btn-success">Create Discount</button>
        <a href="{{ route('discount.list') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>

@endsection
