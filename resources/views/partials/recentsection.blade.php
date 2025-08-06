<!-- ==========Category-Section========== -->
<section class="event-section padding-top padding-bottom bg-four">
    <div class="container">
        <div class="tab">
            <div class="section-header-2">
                <div class="left">
                    <h2 class="title heading-color">Active Events</h2>
                    <p class="heading-color" style="font-size:12px;">Mark your calendar for these upcoming events in the next quarter.</p>
                </div>
                <ul class="tab-menu">
                    <li class="active">
                        now showing
                    </li>
                </ul>
            </div>
            <div class="tab-area mb-30-none">
                <div class="tab-item active">
                    <div class="owl-carousel owl-theme tab-slider">
                        @forelse($recentEvents as $event)
                            <div class="item">
                                <div class="event-grid">
                                    <div class="movie-thumb c-thumb">
                                        @if($event->redirect && $event->redirect_url)
                                            <a target="_blank" href="{{ $event->redirect_url }}">
                                        @else
                                            <a href="{{ route('show.details', $event->slug) }}">
                                        @endif
                                            @if($event->featured_image)
                                                <img src="{{ asset('storage/' . $event->featured_image) }}"
                                                     height="420"
                                                     alt="{{ $event->title }}"
                                                     >
                                            @else
                                                <div class="no-image-placeholder" style="height: 420px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center; color: white;">
                                                    <div style="text-align: center;">
                                                        <i class="fas fa-calendar-alt" style="font-size: 48px; margin-bottom: 10px; opacity: 0.8;"></i>
                                                        <p style="margin: 0; font-size: 14px; opacity: 0.9;">{{ Str::limit($event->title, 15) }}</p>
                                                    </div>
                                                </div>
                                            @endif
                                        </a>
                                        <div class="event-date">
                                            <h6 class="date-title">{{ $event->start_date->format('d') }}</h6>
                                            <span style="color:white;">{{ $event->start_date->format('M') }}</span>
                                            @if($event->start_date->year != now()->year)
                                                <span style="color:white; font-size: 12px;">{{ $event->start_date->format('Y') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="movie-content bg-one">
                                        <h5 class="title m-0" style="padding-bottom:0px; border:0px;">
                                            @if($event->redirect && $event->redirect_url)
                                                <a target="_blank" href="{{ $event->redirect_url }}">
                                            @else
                                                <a href="{{ route('show.details', $event->slug) }}">
                                            @endif
                                                {{ Str::limit($event->title, 20, '...') }}
                                            </a>
                                        </h5>
                                        <div class="movie-rating-percent title" style="justify-content:center">
                                            <span style="color:white;">
                                                {{ $event->venue ? Str::limit($event->venue->name, 20, '...') : 'Venue TBA' }}
                                            </span>
                                        </div>
                                        <div class="movie-rating-percent" style="justify-content:center">
                                            @if($event->redirect && $event->redirect_url)
                                                <a target="_blank" href="{{ $event->redirect_url }}" class="custom-button">book now</a>
                                            @elseif($event->status === 'sold_out' || $event->sold_out)
                                                <span class="custom-button" style="background: #dc3545; cursor: not-allowed;">sold out</span>
                                            @elseif($event->start_date->isPast())
                                                <span class="custom-button" style="background: #6c757d; cursor: not-allowed;">event passed</span>
                                            @else
                                                <a href="{{ route('show.details', $event->slug) }}" class="custom-button">
                                                    @if($event->price == 0 || $event->price === null)
                                                        free event
                                                    @else
                                                        book now
                                                    @endif
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="item">
                                <div class="event-grid">
                                    <div class="movie-thumb c-thumb" style="height: 420px; background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); display: flex; align-items: center; justify-content: center;">
                                        <div style="text-align: center; color: white;">
                                            <i class="fas fa-calendar-plus" style="font-size: 48px; margin-bottom: 15px; opacity: 0.8;"></i>
                                            <h4 style="margin: 0;">No Upcoming Events</h4>
                                            <p style="margin: 5px 0 0 0; opacity: 0.9;">Check back soon for new events!</p>
                                        </div>
                                    </div>
                                    <div class="movie-content bg-one">
                                        <h5 class="title m-0" style="padding-bottom:0px; border:0px;">
                                            <span style="color: white;">Stay Tuned</span>
                                        </h5>
                                        <div class="movie-rating-percent title" style="justify-content:center">
                                            <span style="color:white;">More events coming soon</span>
                                        </div>
                                        <div class="movie-rating-percent" style="justify-content:center">
                                            <a href="{{ route('activeevents') }}" class="custom-button">browse all events</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
