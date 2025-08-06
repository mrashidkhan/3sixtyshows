@extends('layouts.master')

@section('content')

<!-- ==========Banner-Section========== -->
<section class="banner-section" style="padding-top:150px; padding-bottom:0px;">
    <div class="banner-bg bg_img bg-fixed" data-background="{{ asset('assets/images/banner/banner01.jpg') }}"></div>
    <div class="container">
        <div class="banner-content">
             <h1 class="title cd-headline clip" style="font-size:52px;">
                <span class="d-block" style="width:100%;">Contact Us</span>
            </h1>
            {{-- <p style="font-size:25px">Get in Touch with 3Sixty Shows - We're Here to Help!</p> --}}
        </div>
    </div>
</section>

<!-- ==========Contact Section========== -->
<section class="contact-section padding-top padding-bottom">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="section-header-3 text-center">
                    <span class="cate">Let's Connect</span>
                    {{-- <h2 class="title">We'd Love to Hear From You</h2> --}}
                    <p>Whether you have questions about upcoming shows, need assistance with bookings, or want to discuss partnerships, our team is here to help make your entertainment experience unforgettable.</p>
                </div>
            </div>
        </div>

        <div class="contact-wrapper">
            <div class="row g-5">
                <!-- Contact Information -->
                <div class="col-lg-6">
                    <div class="contact-info">
                        <div class="contact-info-area">
                            <div class="contact-info-header">
                                <h4 class="title">Get In Touch</h4>
                                <p>Ready to create unforgettable moments? Reach out to us through any of the channels below.</p>
                            </div>

                            <div class="contact-info-body">
                                <div class="info-item">
                                    <div class="info-icon">
                                        <i class="flaticon-location"></i>
                                    </div>
                                    <div class="info-content">
                                        <h6>Our Location</h6>
                                        <span>Dallas, Texas, United States</span>
                                    </div>
                                </div>

                                <div class="info-item">
                                    <div class="info-icon">
                                        <i class="flaticon-telephone"></i>
                                    </div>
                                    <div class="info-content">
                                        <h6>Phone Number</h6>
                                        <span>+1 (555) 360-SHOW</span>
                                    </div>
                                </div>

                                <div class="info-item">
                                    <div class="info-icon">
                                        <i class="flaticon-email"></i>
                                    </div>
                                    <div class="info-content">
                                        <h6>Email Address</h6>
                                        <span>info@3sixtyshows.com</span>
                                    </div>
                                </div>

                                <div class="info-item">
                                    <div class="info-icon">
                                        <i class="flaticon-clock"></i>
                                    </div>
                                    <div class="info-content">
                                        <h6>Business Hours</h6>
                                        <span>Mon - Fri: 9:00 AM - 6:00 PM<br>Weekends: 10:00 AM - 4:00 PM</span>
                                    </div>
                                </div>
                            </div>

                            <div class="contact-info-footer">
                                <h6>Follow Our Journey</h6>
                                <ul class="social-icons">
                                    <li><a href="#"><i class="fab fa-facebook-f"></i></a></li>
                                    <li><a href="#"><i class="fab fa-twitter"></i></a></li>
                                    <li><a href="#"><i class="fab fa-instagram"></i></a></li>
                                    <li><a href="#"><i class="fab fa-linkedin-in"></i></a></li>
                                    <li><a href="#"><i class="fab fa-youtube"></i></a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Contact Form -->
                <div class="col-lg-6">
                    <div class="contact-form-area">
                        <div class="contact-form-header">
                            <h4 class="title">Send Us a Message</h4>
                            <p>Have a question or want to work with us? Drop us a line and we'll get back to you promptly.</p>
                        </div>

                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <strong><i class="fas fa-check-circle"></i> Success!</strong> {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        @if(session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <strong><i class="fas fa-exclamation-triangle"></i> Error!</strong> {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <form action="{{ route('contact.store') }}" method="POST" class="contact-form">
                            @csrf
                            <div class="form-group">
                                <div class="form-row">
                                    <div class="col-md-6">
                                        <input type="text" placeholder="Your Name *" name="name" value="{{ old('name') }}" class="@error('name') is-invalid @enderror" required>
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <input type="email" placeholder="Your Email *" name="email" value="{{ old('email') }}" class="@error('email') is-invalid @enderror" required>
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="form-row">
                                    <div class="col-md-6">
                                        <input type="tel" placeholder="Phone Number" name="phone" value="{{ old('phone') }}" class="@error('phone') is-invalid @enderror">
                                        @error('phone')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <select name="subject" class="nice-select @error('subject') is-invalid @enderror" required>
                                            <option value="">Select Subject *</option>
                                            <option value="General Inquiry" {{ old('subject') == 'General Inquiry' ? 'selected' : '' }}>General Inquiry</option>
                                            <option value="Show Booking" {{ old('subject') == 'Show Booking' ? 'selected' : '' }}>Show Booking</option>
                                            <option value="Technical Support" {{ old('subject') == 'Technical Support' ? 'selected' : '' }}>Technical Support</option>
                                            <option value="Partnership" {{ old('subject') == 'Partnership' ? 'selected' : '' }}>Partnership Opportunities</option>
                                            <option value="Media Inquiry" {{ old('subject') == 'Media Inquiry' ? 'selected' : '' }}>Media Inquiry</option>
                                            <option value="Event Planning" {{ old('subject') == 'Event Planning' ? 'selected' : '' }}>Event Planning</option>
                                            <option value="Feedback" {{ old('subject') == 'Feedback' ? 'selected' : '' }}>Feedback</option>
                                            <option value="Other" {{ old('subject') == 'Other' ? 'selected' : '' }}>Other</option>
                                        </select>
                                        @error('subject')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <textarea placeholder="Your Message *" name="message" class="@error('message') is-invalid @enderror" required>{{ old('message') }}</textarea>
                                @error('message')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group mb-0 text-center">
                                <button type="submit" class="custom-button">
                                    <i class="flaticon-envelope"></i> Send Message
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


@endsection
