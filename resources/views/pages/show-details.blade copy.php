@extends('layouts.master')

@section('content')

<!-- ==========Banner-Section========== -->
<section class="details-banner event-details-banner hero-area bg_img" data-background="{{ $show->featured_image ? asset('storage/' . $show->featured_image) : asset('assets/images/banner/banner03.jpg') }}">
    <div class="container">
        <div class="details-banner-wrapper">
            <div class="details-banner-content">
                <h1 class="title">{{ $show->title }}</h1>
                <div class="tags">
                    <a href="#">{{ $show->category ? $show->category->name : 'Event' }}</a>
                    @if($show->venue)
                        <a href="#">{{ $show->venue->name }}</a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>
<!-- ==========Banner-Section-End========== -->

<!-- ==========Event-Details========== -->
<section class="event-about padding-top padding-bottom">
    <div class="container">
        <div class="row">
            <div class="col-lg-8">
                <div class="event-about-content">
                    <div class="section-header-3 left-style">
                        <span class="cate">{{ $show->category ? $show->category->name : 'Event' }}</span>
                        <h2 class="title">About The Event</h2>
                        @if($show->description)
                            <p>{!! nl2br(e($show->description)) !!}</p>
                        @endif
                    </div>

                    <!-- Event Details -->
                    <div class="event-details-info">
                        <div class="details-info-item">
                            <h5 class="title">Event Schedule</h5>
                            <div class="info-box">
                                <div class="info-left">
                                    <h6>Start Date & Time</h6>
                                    <p>{{ $formattedStartDate }}<br>{{ $formattedStartTime }}</p>
                                </div>
                                <div class="info-right">
                                    <h6>End Date & Time</h6>
                                    <p>{{ $formattedEndDate }}<br>{{ $formattedEndTime }}</p>
                                </div>
                            </div>
                        </div>

                        @if($show->venue)
                        <div class="details-info-item">
                            <h5 class="title">Venue Information</h5>
                            <div class="info-box">
                                <h6>{{ $show->venue->name }}</h6>
                                @if($show->venue->address)
                                    <p>{{ $show->venue->address }}</p>
                                @endif
                                @if($show->venue->city || $show->venue->state || $show->venue->zip)
                                    <p>
                                        {{ $show->venue->city ? $show->venue->city . ', ' : '' }}
                                        {{ $show->venue->state ?? '' }}
                                        {{ $show->venue->zip ?? '' }}
                                    </p>
                                @endif
                            </div>
                        </div>
                        @endif

                        @if($show->price !== null)
                        <div class="details-info-item">
                            <h5 class="title">Ticket Information</h5>
                            <div class="info-box">
                                <h6>Price</h6>
                                <p>
                                    @if($show->price == 0)
                                        FREE EVENT
                                    @else
                                        ${{ number_format($show->price, 2) }}
                                    @endif
                                </p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="event-sidebar">
                    <!-- Booking Widget -->
                    <div class="widget widget-1">
                        <div class="widget-1-body">
                            <div class="widget-title">
                                <h4>Book Your Tickets</h4>
                            </div>
                            <div class="widget-content">
                                @if($show->redirect && $show->redirect_url)
                                    <a href="{{ $show->redirect_url }}" target="_blank" class="custom-button" style="width: 100%; text-align: center;">
                                        Book Now
                                    </a>
                                @elseif($isSoldOut)
                                    <button class="custom-button" style="width: 100%; background: #dc3545; cursor: not-allowed;" disabled>
                                        Sold Out
                                    </button>
                                @elseif($eventPassed)
                                    <button class="custom-button" style="width: 100%; background: #6c757d; cursor: not-allowed;" disabled>
                                        Event Passed
                                    </button>
                                @else
                                    <a href="{{ route('show.booking', $show->slug) }}" class="custom-button" style="width: 100%; text-align: center;">
                                        @if($show->price == 0 || $show->price === null)
                                            Register for Free
                                        @else
                                            Book Now - ${{ number_format($show->price, 2) }}
                                        @endif
                                    </a>
                                @endif

                                <div class="event-countdown" style="margin-top: 20px;">
                                    @if(!$eventPassed)
                                        <h6 style="text-align: center; margin-bottom: 15px;">Event Starts In:</h6>
                                        <div class="countdown" data-date="{{ $show->start_date->format('Y-m-d H:i:s') }}">
                                            <!-- Countdown will be inserted by JavaScript -->
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Share Widget -->
                    <div class="widget widget-1">
                        <div class="widget-1-body">
                            <div class="widget-title">
                                <h4>Share This Event</h4>
                            </div>
                            <div class="widget-content">
                                <ul class="social-icons" style="justify-content: center;">
                                    <li>
                                        <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->url()) }}" target="_blank">
                                            <i class="fab fa-facebook-f"></i>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="https://twitter.com/intent/tweet?url={{ urlencode(request()->url()) }}&text={{ urlencode($show->title) }}" target="_blank">
                                            <i class="fab fa-twitter"></i>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="https://www.linkedin.com/shareArticle?mini=true&url={{ urlencode(request()->url()) }}&title={{ urlencode($show->title) }}" target="_blank">
                                            <i class="fab fa-linkedin-in"></i>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="whatsapp://send?text={{ urlencode($show->title . ' - ' . request()->url()) }}" target="_blank">
                                            <i class="fab fa-whatsapp"></i>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- ==========Event-Details-End========== -->

<!-- ==========Related Events========== -->
@if($relatedShows->count() > 0)
<section class="event-section padding-top padding-bottom bg-four">
    <div class="container">
        <div class="section-header-3">
            <span class="cate">You May Also Like</span>
            <h2 class="title">Related Events</h2>
        </div>
        <div class="row">
            @foreach($relatedShows as $relatedShow)
                <div class="col-lg-3 col-md-6 col-sm-6">
                    <div class="event-grid">
                        <div class="movie-thumb c-thumb">
                            <a href="{{ route('show.details', $relatedShow->slug) }}">
                                @if($relatedShow->featured_image)
                                    <img src="{{ asset('storage/' . $relatedShow->featured_image) }}"
                                         height="320"
                                         alt="{{ $relatedShow->title }}">
                                @else
                                    <div class="no-image-placeholder" style="height: 320px;">
                                        <i class="fas fa-calendar-alt"></i>
                                        <p>{{ Str::limit($relatedShow->title, 15) }}</p>
                                    </div>
                                @endif
                            </a>
                            <div class="event-date">
                                <h6 class="date-title">{{ $relatedShow->start_date->format('d') }}</h6>
                                <span>{{ $relatedShow->start_date->format('M') }}</span>
                            </div>
                        </div>
                        <div class="movie-content bg-one">
                            <h5 class="title m-0">
                                <a href="{{ route('show.details', $relatedShow->slug) }}">
                                    {{ Str::limit($relatedShow->title, 20, '...') }}
                                </a>
                            </h5>
                            <div class="movie-rating-percent">
                                <span>{{ $relatedShow->venue ? Str::limit($relatedShow->venue->name, 20, '...') : 'Venue TBA' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif
<!-- ==========Related Events End========== -->

@endsection

@push('styles')
<style>
.event-details-info {
    margin-top: 40px;
}

.details-info-item {
    margin-bottom: 30px;
    padding: 25px;
    background: #032055;
    border-radius: 10px;
}

.details-info-item .title {
    font-size: 20px;
    margin-bottom: 20px;
    color: #31d7a9;
}

.info-box {
    display: flex;
    justify-content: space-between;
    flex-wrap: wrap;
}

.info-box h6 {
    font-size: 16px;
    margin-bottom: 10px;
    color: #ffffff;
}

.info-box p {
    margin: 0;
    color: #dbe2fb;
}

.info-left, .info-right {
    width: 48%;
}

.event-sidebar .widget {
    margin-bottom: 30px;
}

.widget-title h4 {
    font-size: 22px;
    margin-bottom: 20px;
    text-align: center;
}

.no-image-placeholder {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    flex-direction: column;
}

.no-image-placeholder i {
    font-size: 48px;
    margin-bottom: 10px;
    opacity: 0.8;
}

@media (max-width: 767px) {
    .info-left, .info-right {
        width: 100%;
        margin-bottom: 15px;
    }
}
</style>
@endpush

@push('scripts')
<script>
// Countdown functionality
document.addEventListener('DOMContentLoaded', function() {
    const countdownElement = document.querySelector('.countdown');
    if (countdownElement) {
        const eventDate = new Date(countdownElement.dataset.date).getTime();

        const countdown = setInterval(function() {
            const now = new Date().getTime();
            const distance = eventDate - now;

            if (distance < 0) {
                clearInterval(countdown);
                countdownElement.innerHTML = '<p style="text-align: center;">Event has started!</p>';
                return;
            }

            const days = Math.floor(distance / (1000 * 60 * 60 * 24));
            const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);

            countdownElement.innerHTML = `
                <ul style="display: flex; justify-content: space-around; text-align: center;">
                    <li>
                        <h2>${days}</h2>
                        <p>Days</p>
                    </li>
                    <li>
                        <h2>${hours}</h2>
                        <p>Hours</p>
                    </li>
                    <li>
                        <h2>${minutes}</h2>
                        <p>Minutes</p>
                    </li>
                    <li>
                        <h2>${seconds}</h2>
                        <p>Seconds</p>
                    </li>
                </ul>
            `;
        }, 1000);
    }
});
</script>
@endpush
