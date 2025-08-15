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
                                        @php
                                            // Determine the appropriate link based on ticket types and redirect settings
                                            $hasTicketTypes = $show->ticketTypes && $show->ticketTypes->count() > 0;

                                            if ($hasTicketTypes) {
                                                $eventLink = route('ga-booking.tickets', $show->slug);
                                                $linkTarget = '_self';
                                                $linkParams = '';
                                            } elseif ($show->redirect && $show->redirect_url) {
                                                $eventLink = $show->redirect_url;
                                                $linkTarget = '_blank';
                                                $linkParams = '?frm=ae';
                                            } else {
                                                $eventLink = route('show.details', $show->slug);
                                                $linkTarget = '_self';
                                                $linkParams = '';
                                            }
                                        @endphp

                                        <a href="{{ $eventLink . $linkParams }}" target="{{ $linkTarget }}" @if($linkTarget === '_blank') rel="noopener" @endif>
                                            @if ($show->featured_image && file_exists(storage_path('app/public/' . $show->featured_image)))
                                                <img src="{{ asset('storage/' . $show->featured_image) }}"
                                                     height="420"
                                                     alt="{{ $show->title ?? 'Event Image' }}"
                                                     loading="lazy"
                                                     onerror="this.parentElement.innerHTML='<div class=\'no-image-placeholder\' style=\'height: 420px; background: #f8f9fa; display: flex; align-items: center; justify-content: center; color: #6c757d;\'><div style=\'text-align: center;\'><i class=\'fas fa-image\' style=\'font-size: 48px; margin-bottom: 10px; opacity: 0.5;\'></i><p>No Image Available</p></div></div>'">
                                            @else
                                                <div class="no-image-placeholder"
                                                    style="height: 420px; background: #f8f9fa; display: flex; align-items: center; justify-content: center; color: #6c757d;">
                                                    <div style="text-align: center;">
                                                        <i class="fas fa-image"
                                                            style="font-size: 48px; margin-bottom: 10px; opacity: 0.5;"></i>
                                                        <p>No Image Available</p>
                                                    </div>
                                                </div>
                                            @endif
                                        </a>

                                        @if($show->start_date)
                                            <div class="event-date">
                                                <h6 class="date-title">{{ $show->start_date->format('d') }}</h6>
                                                <span style="color:white;">{{ $show->start_date->format('M') }}</span>
                                                <span style="color:white;">{{ $show->start_date->format('Y') }}</span>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="movie-content bg-one">
                                        <h5 class="title m-0">
                                            <a href="{{ $eventLink . $linkParams }}" target="{{ $linkTarget }}" @if($linkTarget === '_blank') rel="noopener" @endif>
                                                @php
                                                    $showTitle = $show->title ?? 'Untitled Show';
                                                    $displayTitle = mb_strlen($showTitle) > 35 ? mb_substr($showTitle, 0, 35) . '...' : $showTitle;
                                                @endphp
                                                {{ $displayTitle }}
                                            </a>
                                        </h5>

                                        <div class="movie-rating-percent">
                                            @if($show->venue?->name)
                                                @php
                                                    $venueName = $show->venue->name;
                                                    $displayVenue = mb_strlen($venueName) > 22 ? mb_substr($venueName, 0, 22) . '...' : $venueName;
                                                @endphp
                                                <span title="{{ $venueName }}">{{ $displayVenue }}</span>
                                            @else
                                                <span>Venue TBA</span>
                                            @endif
                                        </div>

                                        {{-- Optional: Add show timing --}}
                                        @if($show->start_date)
                                            <div class="show-time" style="font-size: 12px; color: #666; margin-top: 5px;">
                                                {{ $show->start_date->format('g:i A') }}
                                            </div>
                                        @endif

                                        {{-- Show appropriate action button based on ticket availability --}}
                                        <div class="show-actions" style="margin-top: 10px;">
                                            @if ($show->status === 'sold_out' || $show->sold_out)
                                                <span class="btn btn-secondary btn-sm" style="cursor: not-allowed;">
                                                    Sold Out
                                                </span>
                                            @elseif($show->start_date->isPast())
                                                <span class="btn btn-secondary btn-sm" style="cursor: not-allowed;">
                                                    Event Passed
                                                </span>
                                            @else
                                                @if ($hasTicketTypes)
                                                    <a href="{{ route('ga-booking.tickets', $show->slug) }}"
                                                       class="btn btn-primary btn-sm">
                                                        @if ($show->price == 0 || $show->price === null)
                                                            Select Tickets
                                                        @else
                                                            Book Now
                                                        @endif
                                                    </a>
                                                @elseif($show->redirect && $show->redirect_url)
                                                    <a target="_blank" href="{{ $show->redirect_url }}?frm=ae"
                                                       rel="noopener" class="btn btn-primary btn-sm">
                                                        Book Now
                                                    </a>
                                                @else
                                                    <a href="{{ route('show.details', $show->slug) }}"
                                                       class="btn btn-outline-primary btn-sm">
                                                        @if ($show->price == 0 || $show->price === null)
                                                            Free Event
                                                        @else
                                                            View Details
                                                        @endif
                                                    </a>
                                                @endif
                                            @endif
                                        </div>

                                        {{-- Optional: Add price if available --}}
                                        @if($show->price && $show->price > 0)
                                            <div class="show-price" style="font-size: 14px; font-weight: bold; color: #007bff; margin-top: 5px;">
                                                From ${{ number_format($show->price, 2) }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12 text-center">
                                <div class="no-events-message" style="padding: 60px 20px;">
                                    <i class="fas fa-calendar-plus fa-3x mb-3"
                                        style="color: #ccc; opacity: 0.5;"></i>
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
