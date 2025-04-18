@extends('front.layout.layout')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center" style="margin-top: 100px;">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header text-center bg-primary text-white">
                    <h4>Login</h4>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                        </ul>
                    </div>
                    @endif
                    <form action="{{ route('loginCheck') }}" method="POST">
                        @csrf
                        <div class="form-group mb-3">
                            <input type="email" name="email" id="email" class="form-control" placeholder="Enter your email" required>
                        </div>
                        <div class="form-group mb-3">
                            <input type="password" name="password" id="password" class="form-control" placeholder="Enter your password" required>
                        </div>
                        <div class="form-group mb-3">
                            <button type="submit" class="btn btn-primary w-100">Login</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row justify-content-center" style="margin-top: 20px;">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header text-center">
                    <h4 class="bg-dark text-white">New to Signup here!</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('user_store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <input type="text" class="form-control" id="firstName" name="first_name" placeholder="Enter your first name" required>
                        </div>
                        <div class="mb-3">
                            <input type="text" class="form-control" id="lastName" name="last_name" placeholder="Enter your last name" required>
                        </div>
                        <div class="mb-3">
                            <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email" required>
                        </div>
                        <div class="mb-3">
                            <input type="password" class="form-control" id="password" name="password" placeholder="Enter your password" required>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-dark">Sign Up</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

