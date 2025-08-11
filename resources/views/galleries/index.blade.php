@extends('layouts.master')

@section('content')

<!-- ==========Banner-Section========== -->
<section class="banner-section" style="padding-top:150px; padding-bottom:0px;">
    <div class="banner-bg bg_img bg-fixed" data-background="{{ asset('assets/images/banner/banner01.jpg') }}"></div>
    <div class="container">
        <div class="banner-content">
            <h1 class="title cd-headline clip" style="font-size:52px;">
                <span class="d-block" style="width:100%;">Photo Gallery</span>
            </h1>
        </div>
    </div>
</section>

<!-- ==========Photo Galleries Section========== -->
<section class="event-section padding-top padding-bottom bg-four">
    <div class="container">
        {{-- <div class="section-header-2">
            <div class="left">
                <h2 class="title heading-color">Photo Galleries</h2>
                <p class="heading-color" style="font-size:12px;">Explore our event memories through the years</p>
            </div>
        </div> --}}

        <!-- Photo Galleries Accordion -->
        <div class="faq-wrapper">
            @php
                // Group shows by year from start_date
                $showsByYear = $shows->groupBy(function($show) {
                    return $show->start_date ? $show->start_date->format('Y') : 'Unknown';
                })->sortKeysDesc();
            @endphp

            @forelse($showsByYear as $year => $yearShows)
                <div class="faq-item photo-gallery-year" data-year="{{ $year }}">
                    <div class="faq-title" style="cursor: pointer;">
                        <h4 class="title">{{ $year }} Events</h4>
                        <div class="right-icon">
                            <span></span>
                        </div>
                    </div>
                    <div class="faq-content photo-year-content">
                        <div class="row">
                            @foreach($yearShows as $show)
                                @if($show->photos && $show->photos->where('is_active', true)->count() > 0)
                                    <div class="col-lg-12 mb-4">
                                        <div class="show-galleries-section">
                                            <h5 class="show-title mb-3" style="color: #31d7a9; border-bottom: 1px solid #11326f; padding-bottom: 10px;">
                                                {{ $show->title }}
                                                <small style="color: #9aace5; font-size: 14px; margin-left: 10px;">
                                                    {{ $show->start_date ? $show->start_date->format('M d, Y') : 'Date TBA' }}
                                                </small>
                                            </h5>

                                            <!-- Photo Galleries for this show -->
                                            <div class="photo-galleries-grid">
                                                @foreach($show->photos->where('is_active', true)->sortBy('display_order') as $gallery)
                                                    <div class="gallery-item-card" data-show-id="{{ $show->id }}" data-gallery-id="{{ $gallery->id }}">
                                                        <div class="gallery-card">
                                                            <div class="gallery-thumb">
                                                                @if($gallery->image)
                                                                    <img src="{{ asset('storage/' . $gallery->image) }}"
                                                                         alt="{{ $gallery->title }}"
                                                                         style="width: 100%; height: 200px; object-fit: cover; border-radius: 10px;">
                                                                @else
                                                                    <div class="no-image-placeholder" style="height: 200px; background: #f8f9fa; display: flex; align-items: center; justify-content: center; color: #6c757d; border-radius: 10px;">
                                                                        <div style="text-align: center;">
                                                                            <i class="fas fa-images" style="font-size: 36px; margin-bottom: 10px; opacity: 0.5;"></i>
                                                                            <p>Gallery Cover</p>
                                                                        </div>
                                                                    </div>
                                                                @endif
                                                                <div class="gallery-overlay">
                                                                    <div class="gallery-content">
                                                                        <h6 style="color: white; margin-bottom: 10px;">{{ $gallery->title }}</h6>
                                                                        <p style="color: #ddd; font-size: 12px; margin-bottom: 15px;">
                                                                            {{ $gallery->photos->where('is_active', true)->count() }} Photos
                                                                        </p>
                                                                        <button class="btn btn-sm custom-button view-gallery-btn"
                                                                                data-gallery-id="{{ $gallery->id }}"
                                                                                data-gallery-title="{{ $gallery->title }}">
                                                                            <i class="fas fa-eye"></i> View Gallery
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>
            @empty
                <div class="faq-item">
                    <div class="faq-content" style="display: block;">
                        <div style="text-align: center; padding: 60px 0; color: #6c757d;">
                            <i class="fas fa-images" style="font-size: 72px; margin-bottom: 20px; opacity: 0.3;"></i>
                            <h4>No Photo Galleries Available</h4>
                            <p>Check back soon for event photo galleries!</p>
                        </div>
                    </div>
                </div>
            @endforelse
        </div>
    </div>
</section>

<!-- Photo Gallery Modal -->
<div class="modal fade" id="photoGalleryModal" tabindex="-1" role="dialog" aria-labelledby="photoGalleryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content" style="background: #001232; border: none;">
            <div class="modal-header" style="border-bottom: 1px solid #11326f;">
                <h5 class="modal-title" id="photoGalleryModalLabel" style="color: #ffffff;">Gallery Photos</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: #ffffff; opacity: 0.8;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="padding: 30px;">
                <div id="galleryPhotosContainer">
                    <!-- Photos will be loaded here via AJAX -->
                    <div class="loading-spinner text-center" style="padding: 60px 0;">
                        <div class="spinner-border text-info" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                        <p style="color: #9aace5; margin-top: 15px;">Loading photos...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Gallery Card Styles */
.photo-galleries-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 20px;
    margin-top: 20px;
}

.gallery-item-card {
    position: relative;
    cursor: pointer;
    transition: transform 0.3s ease;
}

.gallery-item-card:hover {
    transform: translateY(-5px);
}

.gallery-card {
    background: #032055;
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
    transition: all 0.3s ease;
}

.gallery-card:hover {
    box-shadow: 0 10px 25px rgba(49, 215, 169, 0.2);
}

.gallery-thumb {
    position: relative;
    overflow: hidden;
}

.gallery-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(to bottom, rgba(0,0,0,0.3) 0%, rgba(0,0,0,0.8) 100%);
    display: flex;
    align-items: flex-end;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.gallery-item-card:hover .gallery-overlay {
    opacity: 1;
}

.gallery-content {
    padding: 20px;
    width: 100%;
}

.gallery-content h6 {
    font-weight: 600;
    margin-bottom: 5px;
}

/* Photo Grid Styles */
.photo-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 15px;
    margin-top: 20px;
}

.photo-item {
    position: relative;
    overflow: hidden;
    border-radius: 10px;
    cursor: pointer;
    transition: transform 0.3s ease;
    background: #032055;
}

.photo-item:hover {
    transform: scale(1.05);
}

.photo-item img {
    width: 100%;
    height: 200px;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.photo-item:hover img {
    transform: scale(1.1);
}

.photo-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0,0,0,0.6);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.photo-item:hover .photo-overlay {
    opacity: 1;
}

.photo-overlay i {
    color: #31d7a9;
    font-size: 24px;
}

/* Accordion Customization */
.photo-gallery-year .faq-title {
    background: linear-gradient(135deg, #032055 0%, #0a1e5e 100%);
    border: 1px solid #11326f;
    border-radius: 10px;
    padding: 20px 30px;
    margin-bottom: 10px;
}

.photo-gallery-year.active .faq-title {
    background: linear-gradient(135deg, #31d7a9 0%, #26b894 100%);
    border-color: #31d7a9;
}

.photo-gallery-year.active .faq-title .title {
    color: #ffffff;
}

.photo-gallery-year .faq-content {
    background: transparent;
    padding: 20px 0;
    border: none;
}

/* Show title styling */
.show-title {
    font-size: 20px;
    font-weight: 700;
    text-transform: uppercase;
}

/* Responsive Design */
@media (max-width: 768px) {
    .photo-galleries-grid {
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 15px;
    }

    .photo-grid {
        grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
        gap: 10px;
    }

    .photo-item img {
        height: 150px;
    }
}

@media (max-width: 576px) {
    .photo-galleries-grid {
        grid-template-columns: 1fr;
    }

    .photo-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Accordion functionality
    const faqItems = document.querySelectorAll('.photo-gallery-year');

    faqItems.forEach(item => {
        const faqTitle = item.querySelector('.faq-title');
        const faqContent = item.querySelector('.faq-content');

        faqTitle.addEventListener('click', function() {
            // Close all other items
            faqItems.forEach(otherItem => {
                if (otherItem !== item) {
                    otherItem.classList.remove('active');
                    otherItem.querySelector('.faq-content').style.display = 'none';
                }
            });

            // Toggle current item
            item.classList.toggle('active');
            if (item.classList.contains('active')) {
                faqContent.style.display = 'block';
            } else {
                faqContent.style.display = 'none';
            }
        });
    });

    // Gallery view functionality
    const viewGalleryBtns = document.querySelectorAll('.view-gallery-btn');

    viewGalleryBtns.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();

            const galleryId = this.getAttribute('data-gallery-id');
            const galleryTitle = this.getAttribute('data-gallery-title');

            // Update modal title
            document.getElementById('photoGalleryModalLabel').textContent = galleryTitle;

            // Show modal
            $('#photoGalleryModal').modal('show');

            // Load photos via AJAX
            loadGalleryPhotos(galleryId);
        });
    });

    function loadGalleryPhotos(galleryId) {
        const container = document.getElementById('galleryPhotosContainer');

        // Show loading spinner
        container.innerHTML = `
            <div class="loading-spinner text-center" style="padding: 60px 0;">
                <div class="spinner-border text-info" role="status">
                    <span class="sr-only">Loading...</span>
                </div>
                <p style="color: #9aace5; margin-top: 15px;">Loading photos...</p>
            </div>
        `;

        // Make AJAX request
        fetch(`/gallery/${galleryId}/photos`)
            .then(response => response.json())
            .then(data => {
                if (data.success && data.photos.length > 0) {
                    let photosHtml = '<div class="photo-grid">';

                    data.photos.forEach(photo => {
                        const imageUrl = photo.image_url || '/images/placeholder.jpg';

                        photosHtml += `
                            <div class="photo-item" data-image="${imageUrl}">
                                <img src="${imageUrl}" alt="${photo.description || 'Gallery Photo'}" loading="lazy">
                                <div class="photo-overlay">
                                    <i class="fas fa-search-plus"></i>
                                </div>
                            </div>
                        `;
                    });

                    photosHtml += '</div>';
                    container.innerHTML = photosHtml;

                    // Add click handlers for photo items (for lightbox functionality)
                    const photoItems = container.querySelectorAll('.photo-item');
                    photoItems.forEach(item => {
                        item.addEventListener('click', function() {
                            const imageUrl = this.getAttribute('data-image');
                            openLightbox(imageUrl);
                        });
                    });

                } else {
                    container.innerHTML = `
                        <div class="text-center" style="padding: 60px 0; color: #6c757d;">
                            <i class="fas fa-images" style="font-size: 48px; margin-bottom: 15px; opacity: 0.5;"></i>
                            <h5>No Photos Available</h5>
                            <p>This gallery doesn't have any photos yet.</p>
                        </div>
                    `;
                }
            })
            .catch(error => {
                console.error('Error loading photos:', error);
                container.innerHTML = `
                    <div class="text-center" style="padding: 60px 0; color: #dc3545;">
                        <i class="fas fa-exclamation-triangle" style="font-size: 48px; margin-bottom: 15px;"></i>
                        <h5>Error Loading Photos</h5>
                        <p>Sorry, we couldn't load the photos. Please try again later.</p>
                    </div>
                `;
            });
    }

    function openLightbox(imageUrl) {
        // Simple lightbox implementation
        const lightboxHtml = `
            <div class="lightbox-overlay" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.9); z-index: 9999; display: flex; align-items: center; justify-content: center;">
                <div class="lightbox-content" style="position: relative; max-width: 90%; max-height: 90%;">
                    <img src="${imageUrl}" style="max-width: 100%; max-height: 100%; object-fit: contain;">
                    <button class="lightbox-close" style="position: absolute; top: -40px; right: 0; background: none; border: none; color: white; font-size: 30px; cursor: pointer;">&times;</button>
                </div>
            </div>
        `;

        document.body.insertAdjacentHTML('beforeend', lightboxHtml);

        const lightbox = document.querySelector('.lightbox-overlay');
        const closeBtn = lightbox.querySelector('.lightbox-close');

        closeBtn.addEventListener('click', () => lightbox.remove());
        lightbox.addEventListener('click', (e) => {
            if (e.target === lightbox) lightbox.remove();
        });
    }
});
</script>
@endsection
