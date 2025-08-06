<!-- ==========Event-Section========== -->
<section class="event-section padding-bottom" style="padding-top:60px;">
    <div class="container">
        <div class="row flex-wrap-reverse justify-content-center">
            <div class="col-lg-9 mb-50 mb-lg-0">
                <div class="filter-tab">
                    <div class="row mb-10 justify-content-center">
                        @forelse($shows->where('start_date', '>', now())->sortBy('start_date') as $show)
                            <div class="col-sm-6 col-lg-4">
                                <div class="event-grid" style="height: 510px;">
                                    <div class="movie-thumb c-thumb" style="height: 357px;">
                                        @if($show->redirect && $show->redirect_url)
                                            <a target="_blank" href="{{ $show->redirect_url }}?frm=ae">
                                        @else
                                            <a href="{{ route('show.details', $show->slug) }}">
                                        @endif
                                            @if($show->featured_image)
                                                <img src="{{ asset('storage/' . $show->featured_image) }}" height="420" alt="{{ $show->title }}">
                                            @else
                                                <div class="no-image-placeholder" style="height: 420px; background: #f8f9fa; display: flex; align-items: center; justify-content: center; color: #6c757d;">
                                                    <div style="text-align: center;">
                                                        <i class="fas fa-image" style="font-size: 48px; margin-bottom: 10px; opacity: 0.5;"></i>
                                                        <p>No Image Available</p>
                                                    </div>
                                                </div>
                                            @endif
                                        </a>
                                        <div class="event-date">
                                            <h6 class="date-title">{{ $show->start_date->format('d') }}</h6>
                                            <span style="color:white;">{{ $show->start_date->format('M') }}</span>
                                            <span style="color:white;">{{ $show->start_date->format('Y') }}</span>
                                        </div>
                                    </div>
                                    <div class="movie-content bg-one">
                                        <h5 class="title m-0">
                                            @if($show->redirect && $show->redirect_url)
                                                <a target="_blank" href="{{ $show->redirect_url }}?frm=ae">
                                            @else
                                                <a href="{{ route('show.details', $show->slug) }}">
                                            @endif
                                                {{ Str::limit($show->title, 20, '...') }}
                                            </a>
                                        </h5>
                                        <div class="movie-rating-percent">
                                            <span>{{ $show->venue ? Str::limit($show->venue->name, 20, '...') : 'Venue TBA' }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12 text-center">
                                <div class="no-events-message" style="padding: 60px 20px;">
                                    <i class="fas fa-calendar-plus fa-3x mb-3" style="color: #ccc; opacity: 0.5;"></i>
                                    <h4>No Upcoming Events</h4>
                                    <p>Check back soon for our latest events!</p>
                                </div>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- ==========Event-Section========== -->
