<!-- Event Posters Section -->
<section class="event-posters-section padding-top padding-bottom" style="padding-top:60px;">
    <div class="banner-bg bg_img bg-fixed" data-background="{{ asset('assets/images/banner/banner01.jpg') }}"></div>
    <div class="container">
        <!-- Section Header -->
        {{-- <div class="section-header text-center mb-5 mt-5">
            <h2 class="title heading-color">Event Posters</h2>
            <p class="subtitle heading-color">Explore our collection of vibrant event posters</p>
            <div class="divider mx-auto"></div>
        </div> --}}

        <!-- Filter Tabs -->
        <div class="filter-tabs mb-4">
            <ul class="nav nav-pills justify-content-center" id="posterTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="all-tab" data-bs-toggle="pill" data-bs-target="#all" type="button" role="tab">
                        All Events
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="upcoming-tab" data-bs-toggle="pill" data-bs-target="#upcoming" type="button" role="tab">
                        Upcoming
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="past-tab" data-bs-toggle="pill" data-bs-target="#past" type="button" role="tab">
                        Past Events
                    </button>
                </li>
                @if(isset($categories) && $categories->count() > 0)
                    @foreach($categories as $category)
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="category-{{ $category->id }}-tab" data-bs-toggle="pill" data-bs-target="#category-{{ $category->id }}" type="button" role="tab">
                                {{ $category->name ?? 'Category' }}
                            </button>
                        </li>
                    @endforeach
                @endif
            </ul>
        </div>

        <!-- Posters Grid -->
        <div class="tab-content" id="posterTabContent">
            <!-- All Events Tab -->
            <div class="tab-pane fade show active" id="all" role="tabpanel">
                <div class="row" id="postersGrid">
                    @forelse($shows->sortByDesc('start_date') as $show)
                        <div class="col-lg-3 col-md-4 col-sm-6 col-6 mb-4 poster-item"
                             data-category="{{ $show->category_id ?? '' }}"
                             data-status="{{ $show->status ?? 'upcoming' }}"
                             data-aos="fade-up"
                             data-aos-delay="{{ $loop->index * 100 }}">
                            <div class="poster-card">
                                <div class="poster-image-wrapper">
                                    @if($show->featured_image && file_exists(storage_path('app/public/' . $show->featured_image)))
                                        <img src="{{ asset('storage/' . $show->featured_image) }}"
                                             alt="{{ $show->title ?? 'Event Poster' }}"
                                             class="poster-image"
                                             loading="lazy">
                                    @else
                                        <div class="no-poster-placeholder">
                                            <i class="fas fa-image"></i>
                                            <p>No Poster Available</p>
                                        </div>
                                    @endif

                                    <!-- Overlay -->
                                    <div class="poster-overlay">
                                        <div class="overlay-content">
                                            @php
                                                $showTitle = $show->title ?? 'Untitled Event';
                                                $displayTitle = mb_strlen($showTitle) > 30 ? mb_substr($showTitle, 0, 30) . '...' : $showTitle;
                                            @endphp
                                            <h5 class="poster-title">{{ $displayTitle }}</h5>

                                            @if($show->start_date)
                                                <p class="poster-date">
                                                    <i class="fas fa-calendar"></i>
                                                    {{ $show->start_date->format('M d, Y') }}
                                                </p>
                                            @endif

                                            <p class="poster-venue">
                                                <i class="fas fa-map-marker-alt"></i>
                                                @php
                                                    $venueName = $show->venue?->name ?? 'Venue TBA';
                                                    $displayVenue = mb_strlen($venueName) > 25 ? mb_substr($venueName, 0, 25) . '...' : $venueName;
                                                @endphp
                                                <span title="{{ $venueName }}">{{ $displayVenue }}</span>
                                            </p>

                                            <div class="poster-actions">
                                                @if($show->redirect && $show->redirect_url)
                                                    <a href="{{ $show->redirect_url }}?frm=poster" target="_blank" rel="noopener" class="btn btn-primary btn-sm">
                                                        <i class="fas fa-external-link-alt"></i> View Event
                                                    </a>
                                                @else
                                                    <a href="{{ route('show.details', $show->slug) }}" class="btn btn-primary btn-sm">
                                                        <i class="fas fa-eye"></i> View Details
                                                    </a>
                                                @endif

                                                @if($show->featured_image)
                                                    <button class="btn btn-secondary btn-sm download-poster"
                                                            data-image="{{ asset('storage/' . $show->featured_image) }}"
                                                            data-title="{{ $show->title ?? 'event_poster' }}">
                                                        <i class="fas fa-download"></i> Download
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Status Badge -->
                                    @if(isset($show->status))
                                        <div class="status-badge">
                                            @switch($show->status)
                                                @case('upcoming')
                                                    <span class="badge bg-success">Upcoming</span>
                                                    @break
                                                @case('ongoing')
                                                    <span class="badge bg-warning">Live</span>
                                                    @break
                                                @case('past')
                                                    <span class="badge bg-secondary">Past</span>
                                                    @break
                                                @case('cancelled')
                                                    <span class="badge bg-danger">Cancelled</span>
                                                    @break
                                                @default
                                                    <span class="badge bg-info">Event</span>
                                            @endswitch
                                        </div>
                                    @endif

                                    <!-- Featured Badge -->
                                    @if($show->is_featured ?? false)
                                        <div class="featured-badge">
                                            <span class="badge bg-warning">
                                                <i class="fas fa-star"></i> Featured
                                            </span>
                                        </div>
                                    @endif
                                </div>

                                <!-- Poster Info -->
                                <div class="poster-info">
                                    @if($show->start_date)
                                        <p class="poster-card-date">{{ $show->start_date->format('M d, Y') }}</p>
                                    @endif
                                    <p class="poster-card-category">
                                        <span class="category-tag">{{ $show->category?->name ?? 'General' }}</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12 text-center">
                            <div class="no-posters-message">
                                <i class="fas fa-images fa-3x mb-3"></i>
                                <h4>No Event Posters Available</h4>
                                <p>Check back soon for our latest event posters!</p>
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Load More Button -->
        @if(isset($shows) && $shows->count() > 12)
            <div class="text-center mt-4">
                <button class="btn btn-outline-primary btn-lg" id="loadMoreBtn">
                    <i class="fas fa-plus"></i> Load More Posters
                </button>
            </div>
        @endif
    </div>
</section>

<!-- Lightbox Modal -->
<div class="modal fade" id="posterModal" tabindex="-1" aria-labelledby="posterModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="posterModalLabel">Event Poster</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <img src="" alt="" class="img-fluid" id="modalPosterImage">
                <div class="mt-3">
                    <h6 id="modalPosterTitle"></h6>
                    <p id="modalPosterDate"></p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="downloadModalPoster">
                    <i class="fas fa-download"></i> Download
                </button>
            </div>
        </div>
    </div>
</div>

<style>
/* Event Posters Section Styles */
.event-posters-section {
    background: #05103D;
    min-height: 100vh;
}

.section-header .title {
    font-size: 2.5rem;
    font-weight: 700;
    color: #fff;
    margin-bottom: 1rem;
}

.section-header .subtitle {
    font-size: 1.1rem;
    color: rgba(255, 255, 255, 0.8);
    margin-bottom: 2rem;
}

.divider {
    width: 80px;
    height: 4px;
    background: linear-gradient(45deg, #ff6b6b, #ffd93d);
    border-radius: 2px;
}

/* Filter Tabs */
.filter-tabs .nav-pills .nav-link {
    background: rgba(255, 255, 255, 0.1);
    color: #fff;
    border: 1px solid rgba(255, 255, 255, 0.2);
    margin: 0 5px;
    border-radius: 25px;
    padding: 10px 20px;
    transition: all 0.3s ease;
}

.filter-tabs .nav-pills .nav-link.active,
.filter-tabs .nav-pills .nav-link:hover {
    background: rgba(255, 255, 255, 0.2);
    border-color: rgba(255, 255, 255, 0.3);
    transform: translateY(-2px);
}

/* Poster Cards */
.poster-card {
    background: rgba(255, 255, 255, 0.95);
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
    transition: all 0.3s ease;
    height: 100%;
}

.poster-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
}

.poster-image-wrapper {
    position: relative;
    overflow: hidden;
    height: 350px;
}

.poster-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
    cursor: pointer;
}

.poster-card:hover .poster-image {
    transform: scale(1.05);
}

.no-poster-placeholder {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    height: 100%;
    background: #f8f9fa;
    color: #6c757d;
}

.no-poster-placeholder i {
    font-size: 48px;
    margin-bottom: 10px;
    opacity: 0.5;
}

/* Overlay */
.poster-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.8);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.poster-card:hover .poster-overlay {
    opacity: 1;
}

.overlay-content {
    text-align: center;
    color: #fff;
    padding: 20px;
}

.poster-title {
    font-size: 1.2rem;
    font-weight: 600;
    margin-bottom: 10px;
}

.poster-date,
.poster-venue {
    font-size: 0.9rem;
    margin-bottom: 8px;
    opacity: 0.9;
}

.poster-date i,
.poster-venue i {
    margin-right: 5px;
}

.poster-actions {
    margin-top: 15px;
}

.poster-actions .btn {
    margin: 0 5px;
    border-radius: 20px;
    font-size: 0.8rem;
    padding: 6px 12px;
}

/* Badges */
.status-badge {
    position: absolute;
    top: 10px;
    right: 10px;
    z-index: 2;
}

.featured-badge {
    position: absolute;
    top: 10px;
    left: 10px;
    z-index: 2;
}

.badge {
    padding: 5px 10px;
    border-radius: 15px;
    font-size: 0.75rem;
}

/* Poster Info */
.poster-info {
    padding: 20px;
    text-align: center;
}

.poster-card-title {
    font-size: 1rem;
    font-weight: 600;
    color: #333;
    margin-bottom: 8px;
}

.poster-card-date {
    font-size: 0.9rem;
    color: #666;
    margin-bottom: 10px;
}

.category-tag {
    background: linear-gradient(45deg, #001232, #764ba2);
    color: #fff;
    padding: 4px 12px;
    border-radius: 15px;
    font-size: 0.8rem;
}

/* Load More Button */
#loadMoreBtn {
    border-radius: 25px;
    padding: 12px 30px;
    font-weight: 600;
    transition: all 0.3s ease;
}

#loadMoreBtn:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
}

/* No Posters Message */
.no-posters-message {
    color: rgba(255, 255, 255, 0.8);
    padding: 60px 20px;
}

.no-posters-message i {
    opacity: 0.5;
}

/* Modal Styles */
.modal-content {
    border-radius: 15px;
}

#modalPosterImage {
    max-height: 70vh;
    border-radius: 10px;
}

/* Error handling for broken images */
.poster-image[src=""], .poster-image:not([src]) {
    display: none;
}

/* Responsive Design */
@media (max-width: 768px) {
    .section-header .title {
        font-size: 2rem;
    }

    .poster-image-wrapper {
        height: 250px;
    }

    .filter-tabs .nav-pills .nav-link {
        margin: 5px 2px;
        padding: 8px 15px;
        font-size: 0.9rem;
    }

    .overlay-content {
        padding: 15px;
    }

    .poster-title {
        font-size: 1rem;
    }

    .poster-actions .btn {
        font-size: 0.7rem;
        padding: 4px 8px;
        margin: 0 2px;
    }
}

/* Animation */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.poster-item {
    animation: fadeInUp 0.6s ease forwards;
}

/* Hidden items for load more functionality */
.poster-item.hidden {
    display: none !important;
}

/* Fade in animation */
.fade-in {
    animation: fadeInUp 0.4s ease forwards;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize AOS (Animate On Scroll) if available
    if (typeof AOS !== 'undefined') {
        AOS.init({
            duration: 800,
            once: true
        });
    }

    // Filter functionality
    const filterButtons = document.querySelectorAll('[data-bs-toggle="pill"]');
    const posterItems = document.querySelectorAll('.poster-item');

    filterButtons.forEach(button => {
        button.addEventListener('click', function() {
            const target = this.getAttribute('data-bs-target');

            if (target === '#all') {
                showAllPosters();
            } else if (target === '#upcoming') {
                filterByStatus('upcoming');
            } else if (target === '#past') {
                filterByStatus('past');
            } else if (target && target.includes('category-')) {
                const categoryId = target.replace('#category-', '');
                filterByCategory(categoryId);
            }
        });
    });

    function showAllPosters() {
        posterItems.forEach(item => {
            item.style.display = 'block';
            item.classList.add('fade-in');
        });
    }

    function filterByStatus(status) {
        posterItems.forEach(item => {
            const itemStatus = item.getAttribute('data-status');
            if (itemStatus === status) {
                item.style.display = 'block';
                item.classList.add('fade-in');
            } else {
                item.style.display = 'none';
                item.classList.remove('fade-in');
            }
        });
    }

    function filterByCategory(categoryId) {
        posterItems.forEach(item => {
            const itemCategory = item.getAttribute('data-category');
            if (itemCategory === categoryId) {
                item.style.display = 'block';
                item.classList.add('fade-in');
            } else {
                item.style.display = 'none';
                item.classList.remove('fade-in');
            }
        });
    }

    // Poster click to open modal
    document.querySelectorAll('.poster-image').forEach(img => {
        img.addEventListener('click', function() {
            if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                const modal = new bootstrap.Modal(document.getElementById('posterModal'));
                const modalImage = document.getElementById('modalPosterImage');
                const modalTitle = document.getElementById('modalPosterTitle');
                const modalDate = document.getElementById('modalPosterDate');

                if (modalImage && modalTitle && modalDate) {
                    modalImage.src = this.src;
                    modalImage.alt = this.alt;
                    modalTitle.textContent = this.alt;

                    // Get date from parent poster item
                    const posterCard = this.closest('.poster-item');
                    const dateElement = posterCard ? posterCard.querySelector('.poster-card-date') : null;
                    modalDate.textContent = dateElement ? dateElement.textContent : '';

                    modal.show();
                }
            }
        });
    });

    // Download functionality
    document.querySelectorAll('.download-poster').forEach(button => {
        button.addEventListener('click', function(e) {
            e.stopPropagation();
            e.preventDefault();
            const imageUrl = this.getAttribute('data-image');
            const title = this.getAttribute('data-title');
            if (imageUrl && title) {
                downloadImage(imageUrl, title);
            }
        });
    });

    const downloadModalBtn = document.getElementById('downloadModalPoster');
    if (downloadModalBtn) {
        downloadModalBtn.addEventListener('click', function() {
            const modalImage = document.getElementById('modalPosterImage');
            const modalTitle = document.getElementById('modalPosterTitle');
            if (modalImage && modalTitle) {
                const imageUrl = modalImage.src;
                const title = modalTitle.textContent;
                downloadImage(imageUrl, title);
            }
        });
    }

    function downloadImage(url, filename) {
        try {
            const link = document.createElement('a');
            link.href = url;
            link.download = (filename || 'poster').replace(/[^a-z0-9]/gi, '_').toLowerCase() + '_poster.jpg';
            link.style.display = 'none';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        } catch (error) {
            console.error('Download failed:', error);
            // Fallback: open in new tab
            window.open(url, '_blank');
        }
    }

    // Load more functionality
    const loadMoreBtn = document.getElementById('loadMoreBtn');
    if (loadMoreBtn) {
        let itemsToShow = 12;
        const allItems = document.querySelectorAll('.poster-item');

        // Hide items beyond initial count
        allItems.forEach((item, index) => {
            if (index >= itemsToShow) {
                item.classList.add('hidden');
            }
        });

        loadMoreBtn.addEventListener('click', function() {
            const hiddenItems = document.querySelectorAll('.poster-item.hidden');
            const itemsToLoad = Math.min(12, hiddenItems.length);

            for (let i = 0; i < itemsToLoad; i++) {
                if (hiddenItems[i]) {
                    hiddenItems[i].classList.remove('hidden');
                    hiddenItems[i].style.display = 'block';
                    hiddenItems[i].classList.add('fade-in');
                }
            }

            if (hiddenItems.length <= itemsToLoad) {
                loadMoreBtn.style.display = 'none';
            }
        });
    }

    // Error handling for broken images
    document.querySelectorAll('.poster-image').forEach(img => {
        img.addEventListener('error', function() {
            const placeholder = this.parentElement.querySelector('.no-poster-placeholder');
            if (placeholder) {
                this.style.display = 'none';
                placeholder.style.display = 'flex';
            }
        });
    });
});
</script>
