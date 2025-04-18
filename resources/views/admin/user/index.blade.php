@extends('admin.layout.layout')

@section('content')

<table class="table table-striped table-bordered">
    <thead>
        <tr>
            <th><b>S.No</b></th>
            <th><b>Name</b></th>
            <th><b>Email</b></th>
            <th><b>Created Date</b></th>
            <th><b>Action</b></th>
        </tr>
    </thead>
    <tbody>
        @foreach($users as $key => $user)
        <tr>
            <td>{{ $key + 1 }}</td>
            <td>{{ $user->name }}</td>
            <td>{{ $user->email }}</td>
            <td>{{ $user->created_at}}</td> <!-- Added proper date formatting -->
            <td>
                <a href="javascript:void(0);" 
                   style="font-size:17px;padding:5px;" 
                   data-id="{{ $user->id }}" 
                   class="delete">
                   <i class="fa fa-trash"></i>
                </a>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

@endsection

@push('footer-script')
<script>
    $(document).on('click', '.delete', function() { // Changed '.delete' to '$(document).on' for dynamic binding
        if (confirm('Are you sure you want to delete this user?')) {
            var id = $(this).data('id');
            $.ajax({
                url: '{{ route("user.delete") }}',
                method: "POST",
                data: {
                    _token: '{{ csrf_token() }}',
                    id: id
                },
                success: function(response) {
                    if (response.success) { // Check if response indicates success
                        alert('User deleted successfully!');
                        location.reload();
                    } else {
                        alert('An error occurred. Please try again.');
                    }
                },
                error: function(xhr) { // Added error handling
                    alert('An unexpected error occurred.');
                }
            });
        }
    });
</script>
@endpush
