<!-- ==========Payment Banner========== -->
{{-- File: resources/views/partials/payment-banner.blade.php --}}

<section class="banner-section" style="padding-top:150px; padding-bottom:0px;">
    <div class="banner-bg bg_img bg-fixed" data-background="{{ asset('assets/images/banner/banner01.jpg') }}"></div>
    <div class="container">
        <div class="banner-content">
            <h1 class="title cd-headline clip" style="font-size:42px;">
                <span class="d-block" style="width:100%;">{{ $show->title }}</span>
            </h1>
            <p style="font-size:18px; margin-top: 20px;">
                <i class="fas fa-calendar"></i> {{ $show->start_date->format('l, F j, Y') }} &nbsp;&nbsp;
                <i class="fas fa-clock"></i> {{ $show->start_date->format('g:i A') }} &nbsp;&nbsp;
                <i class="fas fa-map-marker-alt"></i> {{ $show->venue->name }}
            </p>

            <!-- Timer -->
            <div class="mt-3">
                <span class="timer badge" id="countdown-timer" style="background: #dc3545; font-size: 16px; padding: 10px 20px; border-radius: 25px;">10:00</span>
                <p class="mt-2" style="font-size: 14px; color: #f39c12;">Complete your payment before time expires</p>
            </div>

            <p style="font-size:22px; color: #f39c12;">Secure Payment</p>
        </div>
    </div>
</section>
