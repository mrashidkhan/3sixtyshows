@extends('layouts.master')

@section('content')
    <div class="container" style="margin-top: 120px; padding-top: 20px;">
        <!-- Success Messages -->
        @if (session('success'))
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="alert alert-success alert-dismissible fade show custom-alert" role="alert">
                        <i class="fas fa-check-circle me-2"></i>
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                </div>
            </div>
        @endif

        <!-- Add this after the success message section -->
@if(session('booking_message'))
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="alert alert-info alert-dismissible fade show custom-alert" role="alert">
                <i class="fas fa-ticket-alt me-2"></i>
                {{ session('booking_message') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    </div>
@endif

@if(session('info'))
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="alert alert-info alert-dismissible fade show custom-alert" role="alert">
                <i class="fas fa-info-circle me-2"></i>
                {{ session('info') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    </div>
@endif

        <!-- General Error Messages -->
        @if ($errors->has('error'))
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="alert alert-danger alert-dismissible fade show custom-alert" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        {{ $errors->first('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                </div>
            </div>
        @endif

        <div class="row justify-content-center" style="margin-top: 30px;">
            <!-- Login Form -->
            <div class="col-md-6">
                <div class="card shadow custom-card">
                    <div class="card-header text-center bg-primary text-white">
                        <h4 class="mb-0 py-2">
                            <i class="fas fa-sign-in-alt me-2"></i>Login Here!
                        </h4>
                    </div>
                    <div class="card-body p-4">
                        <!-- Login Specific Errors -->
                        @if ($errors->has('login_error'))
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-circle me-2"></i>
                                {{ $errors->first('login_error') }}
                            </div>
                        @endif

                        <form action="{{ route('logincheck') }}" method="POST" id="loginForm">
                            @csrf
                            <div class="form-group mb-3">
                                <label for="emaillogin" class="form-label">Email Address</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-envelope"></i>
                                    </span>
                                    <input type="email"
                                           name="emaillogin"
                                           id="emaillogin"
                                           class="form-control @error('emaillogin') is-invalid @enderror"
                                           placeholder="Enter your email"
                                           value="{{ old('emaillogin') }}"
                                           required>
                                </div>
                                @error('emaillogin')
                                    <div class="text-danger small mt-1">
                                        <i class="fas fa-exclamation-circle"></i> {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="form-group mb-3">
                                <label for="passwordlogin" class="form-label">Password</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-lock"></i>
                                    </span>
                                    <input type="password"
                                           name="passwordlogin"
                                           id="passwordlogin"
                                           class="form-control @error('passwordlogin') is-invalid @enderror"
                                           placeholder="Enter your password"
                                           required>
                                    <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                                @error('passwordlogin')
                                    <div class="text-danger small mt-1">
                                        <i class="fas fa-exclamation-circle"></i> {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="form-group mb-3">
                                <button type="submit" class="btn btn-primary w-100" id="loginBtn">
                                    <i class="fas fa-sign-in-alt me-2"></i>Login
                                </button>
                            </div>
                        </form>

                        <div class="text-center">
                            <p class="mb-0">Don't have an account? <a href="#signupSection" class="text-decoration-none">Sign up below</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Signup Form -->
        <div class="row justify-content-center" style="margin-top: 30px; margin-bottom: 50px;" id="signupSection">
            <div class="col-md-6">
                <div class="card shadow custom-card">
                    <div class="card-header text-center bg-dark text-white">
                        <h4 class="mb-0 py-2">
                            <i class="fas fa-user-plus me-2"></i>Sign Up Here!
                        </h4>
                    </div>
                    <div class="card-body p-4">
                        <!-- Registration Specific Errors -->
                        @if ($errors->has('first_name') || $errors->has('last_name') || $errors->has('email') || $errors->has('password'))
                            <div class="alert alert-danger">
                                <h6><i class="fas fa-exclamation-triangle me-2"></i>Please fix the following errors:</h6>
                                <ul class="mb-0">
                                    @error('first_name')<li>{{ $message }}</li>@enderror
                                    @error('last_name')<li>{{ $message }}</li>@enderror
                                    @error('email')<li>{{ $message }}</li>@enderror
                                    @error('password')<li>{{ $message }}</li>@enderror
                                </ul>
                            </div>
                        @endif

                        <form action="{{ route('user_store') }}" method="POST" id="signupFormElement">
                            @csrf
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="firstName" class="form-label">First Name</label>
                                    <input type="text"
                                           class="form-control @error('first_name') is-invalid @enderror"
                                           id="firstName"
                                           name="first_name"
                                           placeholder="First name"
                                           value="{{ old('first_name') }}"
                                           required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="lastName" class="form-label">Last Name</label>
                                    <input type="text"
                                           class="form-control @error('last_name') is-invalid @enderror"
                                           id="lastName"
                                           name="last_name"
                                           placeholder="Last name"
                                           value="{{ old('last_name') }}"
                                           required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">Email Address</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-envelope"></i>
                                    </span>
                                    <input type="email"
                                           class="form-control @error('email') is-invalid @enderror"
                                           id="email"
                                           name="email"
                                           placeholder="Enter your email"
                                           value="{{ old('email') }}"
                                           required>
                                </div>
                                @error('email')
                                    <div class="text-danger small mt-1">
                                        <i class="fas fa-exclamation-circle"></i> {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-lock"></i>
                                    </span>
                                    <input type="password"
                                           class="form-control @error('password') is-invalid @enderror"
                                           id="password"
                                           name="password"
                                           placeholder="Enter your password (min 6 characters)"
                                           required>
                                    <button class="btn btn-outline-secondary" type="button" id="toggleSignupPassword">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                                <small class="text-muted">Password must be at least 6 characters long.</small>
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-dark" id="signupBtn">
                                    <i class="fas fa-user-plus me-2"></i>Sign Up
                                </button>
                            </div>
                        </form>

                        <div class="text-center mt-3">
                            <p class="mb-0">Already have an account? <a href="#loginSection" class="text-decoration-none scroll-to-login">Login above</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Content area proper spacing */
        .container {
            position: relative;
            z-index: 1;
        }

        /* Custom alert positioning */
        .custom-alert {
            border-radius: 10px;
            margin-bottom: 20px;
            position: relative;
            z-index: 10;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        /* Card styling */
        .custom-card {
            border: none;
            border-radius: 15px;
            position: relative;
            z-index: 5;
        }

        .card-header {
            border-radius: 15px 15px 0 0 !important;
        }

        .form-control:focus {
            border-color: #80bdff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }

        .input-group-text {
            background-color: #f8f9fa;
            border-right: none;
        }

        .input-group .form-control {
            border-left: none;
        }

        .input-group .form-control:focus {
            border-left: none;
        }

        .btn {
            border-radius: 8px;
            font-weight: 500;
        }

        /* Ensure content is below header */
        body {
            padding-top: 0;
        }

        /* Header shouldn't overlap content */
        .header-section {
            position: relative;
            z-index: 999;
        }

        /* Smooth scrolling for anchor links */
        html {
            scroll-behavior: smooth;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .container {
                margin-top: 100px !important;
                padding-top: 15px !important;
            }

            .custom-card {
                margin: 0 10px;
            }
        }

        /* Loading spinner animation */
        .fa-spin {
            animation: fa-spin 1s infinite linear;
        }

        @keyframes fa-spin {
            0% {
                transform: rotate(0deg);
            }
            100% {
                transform: rotate(360deg);
            }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Toggle password visibility for login
            const togglePassword = document.getElementById('togglePassword');
            const passwordField = document.getElementById('passwordlogin');

            if (togglePassword && passwordField) {
                togglePassword.addEventListener('click', function() {
                    const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
                    passwordField.setAttribute('type', type);

                    const icon = this.querySelector('i');
                    icon.classList.toggle('fa-eye');
                    icon.classList.toggle('fa-eye-slash');
                });
            }

            // Toggle password visibility for signup
            const toggleSignupPassword = document.getElementById('toggleSignupPassword');
            const signupPasswordField = document.getElementById('password');

            if (toggleSignupPassword && signupPasswordField) {
                toggleSignupPassword.addEventListener('click', function() {
                    const type = signupPasswordField.getAttribute('type') === 'password' ? 'text' : 'password';
                    signupPasswordField.setAttribute('type', type);

                    const icon = this.querySelector('i');
                    icon.classList.toggle('fa-eye');
                    icon.classList.toggle('fa-eye-slash');
                });
            }

            // Form submission loading states
            const loginForm = document.getElementById('loginForm');
            const loginBtn = document.getElementById('loginBtn');

            if (loginForm && loginBtn) {
                loginForm.addEventListener('submit', function() {
                    loginBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Logging in...';
                    loginBtn.disabled = true;
                });
            }

            const signupFormElement = document.getElementById('signupFormElement');
            const signupBtn = document.getElementById('signupBtn');

            if (signupFormElement && signupBtn) {
                signupFormElement.addEventListener('submit', function() {
                    signupBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Creating Account...';
                    signupBtn.disabled = true;
                });
            }

            // Auto-dismiss alerts after 5 seconds
            const alerts = document.querySelectorAll('.alert-dismissible');
            alerts.forEach(function(alert) {
                setTimeout(function() {
                    const closeBtn = alert.querySelector('.btn-close');
                    if (closeBtn) {
                        closeBtn.click();
                    }
                }, 5000);
            });

            // Smooth scroll to login section
            const scrollToLoginLinks = document.querySelectorAll('.scroll-to-login');
            scrollToLoginLinks.forEach(function(link) {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    document.querySelector('#loginForm').scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    });
                });
            });

            // If there are registration errors, scroll to signup form
            @if ($errors->has('first_name') || $errors->has('last_name') || $errors->has('email') || $errors->has('password'))
                setTimeout(function() {
                    document.querySelector('#signupSection').scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    });
                }, 100);
            @endif
        });
    </script>
@endsection
