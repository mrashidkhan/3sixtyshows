{{-- @extends('front.layout.layout') --}}
@extends('layouts.master')

@section('content')
    <div class="container mt-5">
        <div class="row justify-content-center" style="margin-top: 100px;">
            <div class="col-md-6">

                @if (session('success'))
            <div class="alert alert-success"
                style="color: black; background-color: #d4edda; border: 1px solid #c3e6cb; padding: 15px; border-radius: 5px; margin-bottom: 20px;">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger"
                style="color: black; background-color: #f8d7da; border: 1px solid #f5c6cb; padding: 15px; border-radius: 5px; margin-bottom: 20px;">
                <ul style="list-style-type: none; padding-left: 0;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
                <div class="card shadow mt-5">
                    <div class="card-header text-center bg-primary text-white">
                        <h4 class="mt-4">Login here!</h4>
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
                        <form action="{{ route('logincheck') }}" method="POST">
                            @csrf
                            <div class="form-group mb-3">
                                <input type="email" name="emaillogin" id="emaillogin" class="form-control"
                                    placeholder="Enter your email" required>
                            </div>
                            <div class="form-group mb-3">
                                <input type="password" name="passwordlogin" id="passwordlogin" class="form-control"
                                    placeholder="Enter your password" required>
                            </div>
                            <div class="form-group mb-3">
                                <button type="submit" class="btn btn-primary w-100">Login</button>
                            </div>
                        </form>
                        @if (session('success'))
            <div class="alert alert-success"
                style="color: black; background-color: #d4edda; border: 1px solid #c3e6cb; padding: 15px; border-radius: 5px; margin-bottom: 20px;">
                {{ session('success') }}
            </div>
        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="row justify-content-center" style="margin-top: 20px;">
            <div class="col-md-6">
                <div class="card mt-5">
                    <div class="card-header text-center">
                        <h4 class="bg-dark text-white">Signup here!</h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('user_store') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <input type="text" class="form-control" id="firstName" name="first_name"
                                    placeholder="Enter your first name" required>
                            </div>
                            <div class="mb-3">
                                <input type="text" class="form-control" id="lastName" name="last_name"
                                    placeholder="Enter your last name" required>
                            </div>
                            <div class="mb-3">
                                <input type="email" class="form-control" id="email" name="email"
                                    placeholder="Enter your email" required>
                            </div>
                            <div class="mb-3">
                                <input type="password" class="form-control" id="password" name="password"
                                    placeholder="Enter your password" required>
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
