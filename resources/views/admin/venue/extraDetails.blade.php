@extends('admin.layout.layout')




@section('content')


<div class="container">
  <div class="row justify-content-center">
    <div class="col-md-8">
      <div class="card">
        <div class="card-header bg-primary text-white">
          <h4 class="mb-0">Product Details </h4>
        </div>
        <div class="card-body">
          <form action="" method="post" action="{{route('product.extraDetailsStore', $id)}}" enctype="multipart/form-data">
            @csrf

            <!-- Product Title -->
            <div class="form-group row">
              <label for="title" class="col-md-4 col-form-label text-md-right">Product Title <span class="text-danger">*</span></label>
              <div class="col-md-6">
              <input type="text" value="{{ @$product->productDetails->title}}" id="title" name="title" class="form-control" required placeholder="Enter product title">
              </div>
            </div>

            <!-- Total Items -->
            <div class="form-group row">
              <label for="total_items" class="col-md-4 col-form-label text-md-right">Total Items <span class="text-danger">*</span></label>
              <div class="col-md-6">
                <input type="number" value="{{ @$product->productDetails->total_items }}" id="total_items" name="total_items" class="form-control" required placeholder="Enter total items">
              </div>
            </div>

            <!-- Product Description -->
            <div class="form-group row">
              <label for="description" class="col-md-4 col-form-label text-md-right">Description</label>
              <div class="col-md-6">
                <textarea name="description" class="form-control col-md-7 col-xs-12" rows="5" required=""> {{ @$product->productDetails->description}}</textarea>
              </div>
            </div>

            <!-- Submit Button -->
            <div class="form-group row mb-0">
              <div class="col-md-6 offset-md-4">
                <button type="submit" class="btn btn-success">Submit</button>
              </div>
            </div>

          </form>
        </div>
      </div>
    </div>
  </div>
</div>



@endsection
