@extends('admin.layout.layout')

@section('content')

<div class="container">
    <h1>Edit Discount</h1>

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('discount.update', $discount->id) }}" method="POST">
        @csrf
        @method('PUT') <!-- This is important for PUT requests -->

        <div class="form-group">
            <label for="product_id">Product</label>
            <select name="product_id" id="product_id" class="form-control" required>
                <option value="">Select a product</option>
                @foreach($products as $product)
                    <option value="{{ $product->id }}" {{ $product->id == $discount->product_id ? 'selected' : '' }}>
                        {{ $product->product_name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label for="weight">Weight</label>
            <input type="text" name="weight" id="weight" class="form-control" value="{{ old('weight', $discount->weight) }}" required>
        </div>

        <div class="form-group">
            <label for="orignal_price">Original Price</label>
            <input type="number" name="original_price" id="original_price" class="form-control" step="0.01" value="{{ old('original_price', $discount->original_price) }}" required>
        </div>

        <div class="form-group">
            <label for="discounted_price">Discounted Price</label>
            <input type="number" name="discounted_price" id="discounted_price" class="form-control" step="0.01" value="{{ old('discounted_price', $discount->discounted_price) }}" required>
        </div>

        <div class="form-group">
            <label for="is_most_bought">Is Most Bought?</label>
            <select name="is_most_bought" id="is_most_bought" class="form-control" required>
                <option value="0" {{ $discount->is_most_bought ? '' : 'selected' }}>No</option>
                <option value="1" {{ $discount->is_most_bought ? 'selected' : '' }}>Yes</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Update Discount</button>
        <a href="{{ route('discount.list') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>

@endsection
