<!-- ==========Event Posters Section========== -->
<section class="event-section padding-bottom" style="padding-top:60px;">
    <div class="container">
        <div class="row flex-wrap-reverse justify-content-center">
            <div class="col-lg-12 mb-50 mb-lg-0">
                <!-- Filter Tabs -->
                <div class="filter-tabs mb-4 text-center">
                    <ul class="nav nav-pills justify-content-center" id="posterTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active poster-filter-btn" id="all-tab" data-filter="all" type="button">
                                All Events
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link poster-filter-btn" id="upcoming-tab" data-filter="upcoming" type="button">
                                Upcoming
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link poster-filter-btn" id="past-tab" data-filter="past" type="button">
                                Past Events
                            </button>
                        </li>
                        @if($categories->count() > 0)
                            @foreach($categories as $category)
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link poster-filter-btn" id="category-{{ $category->id }}-tab" data-filter="category-{{ $category->id }}" type="button">
                                        {{ $category->name }}
                                    </button>
                                </li>
                            @endforeach
                        @endif
                    </ul>
                </div>

                <div class="filter-tab">
                    <div class="row mb-10 justify-content-center" id="postersGrid">
                        @forelse($shows->sortByDesc('start_date') as $show)
                            <div class="col-sm-6 col-lg-4 poster-item"
                                 data-category="{{ $show->category_id }}"
                                 data-status="{{ $show->status }}">
                                <div class="event-grid" style="height: 510px;">
                                    <div class="movie-thumb c-thumb" style="height: 357px;">
                                        @if($show->redirect && $show->redirect_url)
                                            <a href="{{ $show->redirect_url }}?frm=poster" target="_blank">
                                        @else
                                            <a href="{{ route('show.details', $show->slug) }}">
                                        @endif
                                            @if($show->featured_image)
                                                <img src="{{ asset('storage/' . $show->featured_image) }}"
                                                     height="420"
                                                     alt="{{ $show->title }}"
                                                     class="poster-image"
                                                     loading="lazy">
                                            @else
                                                <div class="no-poster-placeholder" style="height: 420px; background: #f8f9fa; display: flex; align-items: center; justify-content: center; color: #6c757d;">
                                                    <div style="text-align: center;">
                                                        <i class="fas fa-image" style="font-size: 48px; margin-bottom: 10px; opacity: 0.5;"></i>
                                                        <p>No Poster Available</p>
                                                    </div>
                                                </div>
                                            @endif
                                        </a>

                                        <div class="event-date">
                                            <h6 class="date-title">{{ $show->start_date->format('d') }}</h6>
                                            <span style="color:white;">{{ $show->start_date->format('M') }}</span>
                                            <span style="color:white;">{{ $show->start_date->format('Y') }}</span>
                                        </div>

                                        <!-- Overlay for hover effects -->
                                        <div class="poster-overlay">
                                            <div class="overlay-content">
                                                <div class="poster-actions">
                                                    @if($show->redirect && $show->redirect_url)
                                                        <a href="{{ $show->redirect_url }}?frm=poster" target="_blank" class="btn btn-primary btn-sm">
                                                            <i class="fas fa-external-link-alt"></i> View Event
                                                        </a>
                                                    @else
                                                        <a href="{{ route('show.details', $show->slug) }}" class="btn btn-primary btn-sm">
                                                            <i class="fas fa-eye"></i> View Details
                                                        </a>
                                                    @endif
                                                    @if($show->featured_image)
                                                        <button class="btn btn-secondary btn-sm download-poster ml-2"
                                                                data-image="{{ asset('storage/' . $show->featured_image) }}"
                                                                data-title="{{ $show->title }}">
                                                            <i class="fas fa-download"></i> Download
                                                        </button>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Status Badge -->
                                        <div class="status-badge">
                                            @if($show->status == 'upcoming')
                                                <span class="badge bg-success">Upcoming</span>
                                            @elseif($show->status == 'ongoing')
                                                <span class="badge bg-warning text-dark">Live</span>
                                            @elseif($show->status == 'past')
                                                <span class="badge bg-secondary">Past</span>
                                            @elseif($show->status == 'cancelled')
                                                <span class="badge bg-danger">Cancelled</span>
                                            @endif
                                        </div>

                                        <!-- Featured Badge -->
                                        @if($show->is_featured)
                                            <div class="featured-badge">
                                                <span class="badge bg-warning text-dark">
                                                    <i class="fas fa-star"></i> Featured
                                                </span>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="movie-content bg-one">
                                        <h5 class="title m-0">
                                            @if($show->redirect && $show->redirect_url)
                                                <a href="{{ $show->redirect_url }}?frm=poster" target="_blank">
                                            @else
                                                <a href="{{ route('show.details', $show->slug) }}">
                                            @endif
                                                {{ Str::limit($show->title, 20, '...') }}
                                            </a>
                                        </h5>
                                        <div class="movie-rating-percent">
                                            <span>{{ $show->venue ? Str::limit($show->venue->name, 20, '...') : 'Venue TBA' }}</span>
                                        </div>
                                        <div class="poster-category">
                                            <span class="category-tag">{{ $show->category->name ?? 'General' }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12 text-center">
                                <div class="no-posters-message" style="padding: 60px 20px;">
                                    <i class="fas fa-images fa-3x mb-3" style="opacity: 0.5;"></i>
                                    <h4>No Event Posters Available</h4>
                                    <p>Check back soon for our latest event posters!</p>
                                </div>
                            </div>
                        @endforelse
                    </div>

                    <!-- Load More Button -->
                    @if($shows->count() > 12)
                        <div class="text-center mt-4">
                            <button class="btn btn-primary btn-lg" id="loadMoreBtn">
                                <i class="fas fa-plus"></i> Load More Posters
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
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
/* Filter Tabs Styling */
.filter-tabs .nav-pills .nav-link {
    background: #001232;
    color: #fff;
    border: 1px solid #001232;
    margin: 0 5px 10px 5px;
    border-radius: 5px;
    padding: 10px 20px;
    transition: all 0.3s ease;
    font-weight: 500;
}

.filter-tabs .nav-pills .nav-link.active,
.filter-tabs .nav-pills .nav-link:hover {
    background: #31d7a9;
    border-color: #31d7a9;
    color: #001232;
    transform: translateY(-2px);
}

/* Poster Item Styling */
.poster-item {
    transition: all 0.3s ease;
}

.poster-item:hover .event-grid {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
}

/* Overlay Effects */
.movie-thumb {
    position: relative;
    overflow: hidden;
}

.poster-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 18, 50, 0.9);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.movie-thumb:hover .poster-overlay {
    opacity: 1;
}

.overlay-content {
    text-align: center;
    color: #fff;
    padding: 20px;
}

.poster-actions .btn {
    margin: 5px;
    border-radius: 20px;
    font-size: 0.9rem;
    padding: 8px 16px;
}

/* Status and Featured Badges */
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
    font-weight: 500;
}

/* Category Tag */
.poster-category {
    margin-top: 8px;
}

.category-tag {
    background: linear-gradient(45deg, #001232, #31d7a9);
    color: #fff;
    padding: 4px 12px;
    border-radius: 15px;
    font-size: 0.8rem;
    display: inline-block;
}

/* Load More Button */
#loadMoreBtn {
    background: #001232;
    border-color: #001232;
    border-radius: 25px;
    padding: 12px 30px;
    font-weight: 600;
    transition: all 0.3s ease;
}

#loadMoreBtn:hover {
    background: #31d7a9;
    border-color: #31d7a9;
    color: #001232;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
}

/* Modal Styling */
.modal-content {
    border-radius: 15px;
    border: none;
}

.modal-header {
    background: #001232;
    color: #fff;
    border-bottom: none;
    border-radius: 15px 15px 0 0;
}

.modal-footer {
    border-top: none;
    border-radius: 0 0 15px 15px;
}

#modalPosterImage {
    max-height: 70vh;
    border-radius: 10px;
}

/* Responsive Design */
@media (max-width: 768px) {
    .filter-tabs .nav-pills .nav-link {
        margin: 5px 2px;
        padding: 8px 15px;
        font-size: 0.9rem;
    }

    .overlay-content {
        padding: 15px;
    }

    .poster-actions .btn {
        font-size: 0.8rem;
        padding: 6px 12px;
        margin: 3px;
    }
}

/* Animation for filtered items */
.poster-item.filtered-out {
    display: none !important;
}

.poster-item.filtered-in {
    display: block;
    animation: fadeInUp 0.6s ease forwards;
}

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

/* Hidden items for load more functionality */
.poster-item.hidden {
    display: none;
}

/* No posters message styling */
.no-posters-message {
    color: #666;
}

.no-posters-message i {
    color: #ccc;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Filter functionality
    const filterButtons = document.querySelectorAll('.poster-filter-btn');
    const posterItems = document.querySelectorAll('.poster-item');

    filterButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Remove active class from all buttons
            filterButtons.forEach(btn => btn.classList.remove('active'));
            // Add active class to clicked button
            this.classList.add('active');

            const filter = this.getAttribute('data-filter');

            if (filter === 'all') {
                showAllPosters();
            } else if (filter === 'upcoming') {
                filterByStatus('upcoming');
            } else if (filter === 'past') {
                filterByStatus('past');
            } else if (filter.includes('category-')) {
                const categoryId = filter.replace('category-', '');
                filterByCategory(categoryId);
            }
        });
    });

    function showAllPosters() {
        posterItems.forEach(item => {
            item.classList.remove('filtered-out');
            item.classList.add('filtered-in');
            item.style.display = 'block';
        });
    }

    function filterByStatus(status) {
        posterItems.forEach(item => {
            const itemStatus = item.getAttribute('data-status');
            if (itemStatus === status) {
                item.classList.remove('filtered-out');
                item.classList.add('filtered-in');
                item.style.display = 'block';
            } else {
                item.classList.add('filtered-out');
                item.classList.remove('filtered-in');
                item.style.display = 'none';
            }
        });
    }

    function filterByCategory(categoryId) {
        posterItems.forEach(item => {
            const itemCategory = item.getAttribute('data-category');
            if (itemCategory === categoryId) {
                item.classList.remove('filtered-out');
                item.classList.add('filtered-in');
                item.style.display = 'block';
            } else {
                item.classList.add('filtered-out');
                item.classList.remove('filtered-in');
                item.style.display = 'none';
            }
        });
    }

    // Poster image click to open modal
    document.querySelectorAll('.poster-image').forEach(img => {
        img.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();

            if (typeof bootstrap !== 'undefined') {
                const modal = new bootstrap.Modal(document.getElementById('posterModal'));
                const modalImage = document.getElementById('modalPosterImage');
                const modalTitle = document.getElementById('modalPosterTitle');
                const modalDate = document.getElementById('modalPosterDate');

                modalImage.src = this.src;
                modalImage.alt = this.alt;
                modalTitle.textContent = this.alt;

                // Get date from parent poster item
                const posterCard = this.closest('.poster-item');
                const eventGrid = posterCard.querySelector('.event-grid');
                const dateElement = eventGrid.querySelector('.event-date');
                if (dateElement) {
                    const day = dateElement.querySelector('.date-title').textContent;
                    const month = dateElement.querySelectorAll('span')[0].textContent;
                    const year = dateElement.querySelectorAll('span')[1].textContent;
                    modalDate.textContent = `${day} ${month} ${year}`;
                }

                modal.show();
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
            downloadImage(imageUrl, title);
        });
    });

    if (document.getElementById('downloadModalPoster')) {
        document.getElementById('downloadModalPoster').addEventListener('click', function() {
            const imageUrl = document.getElementById('modalPosterImage').src;
            const title = document.getElementById('modalPosterTitle').textContent;
            downloadImage(imageUrl, title);
        });
    }

    function downloadImage(url, filename) {
        const link = document.createElement('a');
        link.href = url;
        link.download = filename.replace(/[^a-z0-9]/gi, '_').toLowerCase() + '_poster.jpg';
        link.target = '_blank';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
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
                item.style.display = 'none';
            }
        });

        loadMoreBtn.addEventListener('click', function() {
            const hiddenItems = document.querySelectorAll('.poster-item.hidden');
            const itemsToLoad = Math.min(12, hiddenItems.length);

            for (let i = 0; i < itemsToLoad; i++) {
                hiddenItems[i].classList.remove('hidden');
                hiddenItems[i].style.display = 'block';
            }

            if (hiddenItems.length <= itemsToLoad) {
                loadMoreBtn.style.display = 'none';
            }
        });
    }
});
</script>
