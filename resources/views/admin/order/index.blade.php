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
            <th><b>Order ID</b></th>
            <th><b>Customer Name</b></th>
            <th><b>Payment Method</b></th>
            <th><b>Billing Address</b></th>
            <th><b>Items</b></th>
            <th><b>Grand Total</b></th>
            <th><b>Paid</b></th>
            <th><b>Delivered</b></th>
            <th><b>Delivery Date</b></th>
            <th><b>Actions</b></th>
        </tr>
    </thead>
    <tbody>
        @foreach($orders as $key => $order)
        <tr>
            <td>{{ $order->id }}</td>
            <td>{{ $order->customer->first_name . ' ' . $order->customer->last_name ?? 'N/A' }}</td>
            <td>{{ $order->payment_method }}</td>
            <td>{{ $order->billing_address }}</td>
            <td>
                <ul>
                    @foreach($order->items as $item)
                        <li>{{ $item->discount->product->product_name }} ({{ $item->quantity }} x Rs {{ number_format($item->price, 2) }})</li>
                    @endforeach
                </ul>
            </td>
            <td>Rs {{ number_format($order->grand_total, 2) }}</td>
            <td>
                <input type="checkbox" class="paid-switch" data-order-id="{{ $order->id }}" {{ $order->paid ? 'checked' : '' }}>
            </td>
            <td>
                <input type="checkbox" class="delivered-switch" data-order-id="{{ $order->id }}" {{ $order->delivered ? 'checked' : '' }}>
            </td>
            <td>
                <input type="date" class="delivery-date" data-order-id="{{ $order->id }}" value="{{ $order->delivery_date }}">
            </td>
            <td>
                <button class="btn btn-primary update-button" data-order-id="{{ $order->id }}" disabled>Update</button>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

@endsection

@push('footer-script')
<script>
    document.querySelectorAll('.paid-switch, .delivered-switch, .delivery-date').forEach(function(element) {
        element.addEventListener('change', function() {
            const orderId = this.getAttribute('data-order-id');
            const updateButton = document.querySelector(`.update-button[data-order-id="${orderId}"]`);
            updateButton.disabled = false; // Enable the update button when any field changes
        });
    });

    document.querySelectorAll('.update-button').forEach(function(button) {
        button.addEventListener('click', function() {
            const orderId = this.getAttribute('data-order-id');
            const paid = document.querySelector(`.paid-switch[data-order-id="${orderId}"]`).checked;
            const delivered = document.querySelector(`.delivered-switch[data-order-id="${orderId}"]`).checked;
            const deliveryDate = document.querySelector(`.delivery-date[data-order-id="${orderId}"]`).value;

            // Make an AJAX request to update the order
            fetch(`/orders/${orderId}/update`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    paid: paid,
                    delivered: delivered,
                    delivery_date: deliveryDate
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Order updated successfully!');
                    location.reload(); // Reload the page to see the updated values
                } else {
                    alert('Failed to update order.');
                }
            })
            .catch(error => console.error('Error:', error));
        });
    });
</script>
@endpush
