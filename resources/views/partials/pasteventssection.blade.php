<!-- ==========Past Event Section========== -->
<section class="event-section padding-top padding-bottom bg-four">
    <div class="container">
        <div class="tab">
            <div class="section-header-2">
                <div class="left">
                    <h2 class="title heading-color">Past Events</h2>
                    <p class="heading-color" style="font-size:12px;">Memorable events you experienced</p>
                </div>
            </div>
            <div class="tab-area mb-30-none">
                <div class="tab-item active">
                    <div class="owl-carousel owl-theme owl-reponsive- breakpoint tab-slider">
                        @forelse($pastShows->sortByDesc('start_date') as $show)
                            <div class="item">
                                <div class="event-grid">
                                    <div class="movie-thumb c-thumb">
                                        @if($show->redirect && $show->redirect_url)
                                            <a href="{{ $show->redirect_url }}?frm=pe" target="_blank">
                                        @else
                                            <a href="{{ route('show.details', $show->slug) }}">
                                        @endif
                                            @if($show->featured_image)
                                                <img src="{{ asset('storage/' . $show->featured_image) }}"
                                                     height="420"
                                                     alt="{{ $show->title }}"
                                                     >
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
                                                <a href="{{ $show->redirect_url }}?frm=pe" target="_blank">
                                            @else
                                                <a href="{{ route('show.details', $show->slug) }}">
                                            @endif
                                                {{ strlen($show->title) > 20 ? substr($show->title, 0, 20).'...' : $show->title }}
                                            </a>
                                        </h5>
                                        <div class="movie-rating-percent">
                                            <span style="color:white;">
                                                {{-- {{ optional($show->venue)->name ? Str::limit($show->venue->name, 20, '...') : 'Venue TBA' }} --}}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="item">
                                <div class="event-grid">
                                    <div class="movie-thumb c-thumb" style="height: 420px; background: #f8f9fa; display: flex; align-items: center; justify-content: center;">
                                        <div style="text-align: center; color: #6c757d;">
                                            <i class="fas fa-calendar-times" style="font-size: 48px; margin-bottom: 15px; opacity: 0.5;"></i>
                                            <h4>No Past Events</h4>
                                            <p>Check back soon for our event history!</p>
                                        </div>
                                    </div>
                                    <div class="movie-content bg-one">
                                        <h5 class="title m-0">
                                            <span style="color: white;">No Events Available</span>
                                        </h5>
                                        <div class="movie-rating-percent">
                                            <span style="color:white;">Stay tuned for updates</span>
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
