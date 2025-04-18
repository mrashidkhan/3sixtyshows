@extends('admin.layout.layout')

@section('content')

@if(session('success'))
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

<table class="table table-striped table-bordered">
    <thead>
      <tr>
        <th><b>S.No</b></th>
        <th><b>Product Name</b></th>
        <th><b>Weight</b></th>
        <th><b>Original Price</b></th>
        <th><b>Discounted Price</b></th>
        <th><b>Most Bought</b></th>
        <th><b>Actions</b></th>
      </tr>
    </thead>
    <tbody>
        @foreach($discounts as $key => $discount)
        <tr>
            <td>{{ $key + 1 }}</td>
            <td>{{ $discount->product->product_name ?? 'N/A' }}</td> <!-- Assuming you have a product relationship -->
            <td>{{ number_format($discount->weight,0) }} Grams</td>
            <td>Rs {{ number_format($discount->original_price, 2) }}</td>
            <td>Rs {{ number_format($discount->discounted_price, 2) }}</td>
            <td>{{ $discount->is_most_bought ? 'Yes' : 'No' }}</td>
            <td>
                <a href="{{ route('discount.edit', $discount->id) }}" style="font-size:17px; padding:5px;">
                    <i class="fa fa-edit"></i>
                </a>
                {{-- <a href="javascript:void(0);" style="font-size:17px;padding:5px;" data-id="{{ $discount->id }}" class="discount_delete">
                    <i class="fa fa-trash"></i>
                </a> --}}
                <a href="{{ route('discount.delete', $discount->id) }}" style="font-size:17px;padding:5px;" class="btn-sm mr-1 small btn-danger"> <i class="fa fa-trash"></i></a>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

@endsection

{{-- @push('footer-script')
<script>
    $('.discount_delete').on('click', function() {
        if (confirm('Are you sure to delete this discount?')) {
            var id = $(this).data('id');
            $.ajax({
                url: '{{ route("discount.delete") }}', // Adjust the route name as necessary
                method: "POST",
                data: {
                    _token: '{{ csrf_token() }}',
                    id: id
                },
                success: function(data) {
                    location.reload();
                },
            });
        }
    });
</script>
@endpush --}}
