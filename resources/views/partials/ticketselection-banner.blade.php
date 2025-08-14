<!-- ==========Banner-Section========== -->
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
            <p style="font-size:22px; color: #f39c12;">Select Your Tickets Below</p>
        </div>
    </div>
</section>
