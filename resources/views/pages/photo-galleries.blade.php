@extends('layouts.master')

@section('content')

<!-- ==========Banner-Section========== -->
<section class="banner-section" style="padding-top:150px; padding-bottom:0px;">
    <div class="banner-bg bg_img bg-fixed" data-background="{{ asset('assets/images/banner/banner01.jpg') }}"></div>
    <div class="container">
        <div class="banner-content">
            <h1 class="title cd-headline clip" style="font-size:52px;">
                <span class="d-block" style="width:100%;">Photo Galleries</span>
            </h1>
            <p style="font-size:25px">Relive the Magic - Captured Moments from Our Shows</p>
        </div>
    </div>
</section>

<!-- ==========Photo Galleries Section========== -->
<section class="gallery-section padding-top padding-bottom">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="section-header-3 text-center">
                    <span class="cate">Visual Journey</span>
                    <h2 class="title">Behind the Scenes & Highlights</h2>
                    <p>Explore our collection of stunning photographs capturing the essence, energy, and unforgettable moments from 3Sixty Shows' spectacular events and performances.</p>
                </div>
            </div>
        </div>

        <!-- Photo Gallery Grid -->
        <div class="gallery-wrapper">
            <div class="row g-4" id="gallery-container">
                @forelse($photos as $photo)
                    <div class="col-lg-4 col-md-6 col-sm-6 gallery-item {{ optional($photo->photoGallery)->is_featured ? 'featured' : '' }}"
                         data-category="{{ optional($photo->photoGallery)->is_featured ? 'featured' : 'regular' }}">
                        <div class="gallery-card">
                            @if($photo->has_image)
                            <div class="gallery-thumb">
                                <img src="{{ $photo->image }}" alt="{{ optional($photo->photoGallery)->title ?? 'Photo' }}"
                                     onerror="this.parentElement.innerHTML='<div class=&quot;no-image-placeholder&quot;><i class=&quot;fas fa-image&quot;></i><span>Image not available</span></div>'">
                                {{-- <div class="gallery-overlay">
                                    <div class="gallery-content">
                                        <h4 class="title">{{ optional($photo->photoGallery)->title ?? 'Untitled' }}</h4>
                                        @if(optional($photo->photoGallery)->show)
                                            <span class="show-name">{{ $photo->photoGallery->show->title }}</span>
                                        @endif
                                        <div class="gallery-actions">
                                            <a href="{{ $photo->image }}"
                                               class="gallery-btn lightbox-btn"
                                               data-lightbox="gallery-{{ $photo->id }}"
                                               data-title="{{ optional($photo->photoGallery)->title ?? 'Photo' }}">
                                                <i class="flaticon-zoom"></i>
                                            </a>
                                            @if(optional($photo->photoGallery)->description)
                                                <button class="gallery-btn info-btn"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#photoModal{{ $photo->id }}">
                                                    <i class="flaticon-info"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                </div> --}}
                                {{-- @if(optional($photo->photoGallery)->is_featured)
                                    <div class="featured-badge">
                                        <i class="flaticon-star"></i>
                                        <span>Featured</span>
                                    </div>
                                @endif --}}
                            </div>
                            @else
                            <div class="gallery-thumb">
                                <div class="no-image-placeholder">
                                    <i class="fas fa-image"></i>
                                    <span>No Image Available</span>
                                </div>
                            </div>
                            @endif

                            {{-- <div class="gallery-info">
                                <h5 class="title">{{ optional($photo->photoGallery)->title ?? 'Untitled Photo' }}</h5>
                                @if(optional($photo->photoGallery)->show)
                                    <div class="show-info">
                                        <i class="flaticon-calendar"></i>
                                        <span>{{ $photo->photoGallery->show->title }}</span>
                                    </div>
                                @endif
                                @if(optional($photo->photoGallery)->description)
                                    <p class="description">{{ Str::limit($photo->photoGallery->description, 80) }}</p>
                                @endif
                                @if($photo->description)
                                    <p class="photo-description">{{ Str::limit($photo->description, 60) }}</p>
                                @endif
                                <div class="photo-date">
                                    <i class="flaticon-calendar"></i>
                                    <span>{{ $photo->created_at->format('M d, Y') }}</span>
                                </div>
                            </div> --}}
                        </div>
                    </div>

                    <!-- Photo Info Modal -->
                    @if(optional($photo->photoGallery)->description || $photo->description)
                        <div class="modal fade" id="photoModal{{ $photo->id }}" tabindex="-1">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">{{ optional($photo->photoGallery)->title ?? 'Photo Details' }}</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                @if($photo->has_image)
                                                    <img src="{{ $photo->image }}"
                                                         alt="{{ optional($photo->photoGallery)->title ?? 'Photo' }}"
                                                         class="img-fluid rounded"
                                                         onerror="this.parentElement.innerHTML='<div class=&quot;text-center p-4&quot;><i class=&quot;fas fa-image fa-3x text-muted&quot;></i><p class=&quot;mt-2 text-muted&quot;>Image not available</p></div>'">
                                                @else
                                                    <div class="text-center p-4">
                                                        <i class="fas fa-image fa-3x text-muted"></i>
                                                        <p class="mt-2 text-muted">No Image Available</p>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="col-md-6">
                                                @if(optional($photo->photoGallery)->description)
                                                    <h6>Gallery Description</h6>
                                                    <p>{{ $photo->photoGallery->description }}</p>
                                                @endif

                                                @if($photo->description)
                                                    <h6>Photo Description</h6>
                                                    <p>{{ $photo->description }}</p>
                                                @endif

                                                @if(optional($photo->photoGallery)->show)
                                                    <div class="show-details">
                                                        <h6>Event Details</h6>
                                                        <p><strong>Show:</strong> {{ $photo->photoGallery->show->title }}</p>
                                                        @if($photo->photoGallery->show->show_date)
                                                            <p><strong>Date:</strong> {{ \Carbon\Carbon::parse($photo->photoGallery->show->show_date)->format('M d, Y') }}</p>
                                                        @endif
                                                        @if($photo->photoGallery->show->venue)
                                                            <p><strong>Venue:</strong> {{ $photo->photoGallery->show->venue->name }}</p>
                                                        @endif
                                                    </div>
                                                @endif

                                                <div class="photo-details">
                                                    <h6>Photo Details</h6>
                                                    <p><strong>Uploaded:</strong> {{ $photo->created_at->format('M d, Y') }}</p>
                                                    @if($photo->display_order)
                                                        <p><strong>Display Order:</strong> {{ $photo->display_order }}</p>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                @empty
                    <div class="col-12">
                        <div class="no-gallery">
                            <div class="no-gallery-content">
                                <div class="no-gallery-icon">
                                    <i class="flaticon-image"></i>
                                </div>
                                <h3 class="title">No Photos Available</h3>
                                <p>We're currently building our photo collection. Check back soon for amazing visual content from our shows!</p>
                                <a href="{{ route('activeevents') }}" class="custom-button">
                                    <i class="flaticon-right-arrow"></i> Browse Shows
                                </a>
                            </div>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Pagination -->
        @if($photos->hasPages())
            <div class="pagination-area text-center">
                <div class="pagination-wrapper">
                    {{ $photos->links() }}
                </div>
            </div>
        @endif
    </div>
</section>

@endsection

@push('styles')
<style>
/* Gallery Styles */
.gallery-item {
    transition: all 0.3s ease;
}

.gallery-card {
    background: #fff;
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 5px 20px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
    position: relative;
}

.gallery-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 15px 40px rgba(0,0,0,0.2);
}

.gallery-thumb {
    position: relative;
    overflow: hidden;
    aspect-ratio: 4/3;
}

.gallery-thumb img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: all 0.3s ease;
}

.gallery-card:hover .gallery-thumb img {
    transform: scale(1.1);
}

.no-image-placeholder {
    width: 100%;
    height: 100%;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    background: #f8f9fa;
    color: #6c757d;
    font-size: 14px;
}

.no-image-placeholder i {
    font-size: 48px;
    margin-bottom: 10px;
    opacity: 0.5;
}

.gallery-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(245, 64, 126, 0.9);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: all 0.3s ease;
}

.gallery-card:hover .gallery-overlay {
    opacity: 1;
}

.gallery-content {
    text-align: center;
    color: white;
    padding: 20px;
}

.gallery-content .title {
    font-size: 20px;
    font-weight: 600;
    margin-bottom: 8px;
}

.gallery-content .show-name {
    font-size: 14px;
    opacity: 0.9;
    margin-bottom: 20px;
    display: block;
}

.gallery-actions {
    display: flex;
    justify-content: center;
    gap: 15px;
}

.gallery-btn {
    width: 45px;
    height: 45px;
    background: rgba(255,255,255,0.2);
    border: 2px solid rgba(255,255,255,0.3);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    text-decoration: none;
    transition: all 0.3s ease;
    cursor: pointer;
}

.gallery-btn:hover {
    background: white;
    color: #f5407e;
    transform: scale(1.1);
}

.featured-badge {
    position: absolute;
    top: 15px;
    right: 15px;
    background: #ffd700;
    color: #333;
    padding: 8px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 5px;
    z-index: 2;
}

.gallery-info {
    padding: 20px;
}

.gallery-info .title {
    font-size: 18px;
    font-weight: 600;
    color: #1e2328;
    margin-bottom: 10px;
}

.show-info, .photo-date {
    display: flex;
    align-items: center;
    color: #6c757d;
    font-size: 14px;
    margin-bottom: 10px;
}

.show-info i, .photo-date i {
    margin-right: 8px;
    color: #f5407e;
}

.gallery-info .description, .gallery-info .photo-description {
    color: #6c757d;
    font-size: 14px;
    line-height: 1.6;
    margin-bottom: 10px;
}

.gallery-info .photo-description {
    font-style: italic;
    color: #8a8a8a;
}

.no-gallery {
    text-align: center;
    padding: 80px 30px;
    background: #fff;
    border-radius: 15px;
    border: 1px solid #e9ecef;
}

.no-gallery-icon {
    font-size: 64px;
    color: #e9ecef;
    margin-bottom: 20px;
}

.no-gallery .title {
    font-size: 24px;
    font-weight: 600;
    color: #495057;
    margin-bottom: 15px;
}

.no-gallery p {
    color: #6c757d;
    margin-bottom: 30px;
}

.custom-button {
    display: inline-flex;
    align-items: center;
    padding: 12px 25px;
    background: #f5407e;
    color: white;
    text-decoration: none;
    border-radius: 25px;
    font-weight: 500;
    border: none;
    cursor: pointer;
    transition: all 0.3s ease;
}

.custom-button:hover {
    background: #e91e63;
    transform: translateY(-2px);
    color: white;
}

.custom-button i {
    margin-right: 8px;
}

/* Pagination Styles */
.pagination-area {
    margin-top: 50px;
}

.pagination-wrapper {
    display: flex;
    justify-content: center;
}

.pagination-wrapper .pagination {
    gap: 10px;
}

.pagination-wrapper .page-link {
    border: 2px solid #e9ecef;
    border-radius: 10px;
    padding: 10px 15px;
    color: #495057;
    text-decoration: none;
    transition: all 0.3s ease;
}

.pagination-wrapper .page-link:hover,
.pagination-wrapper .page-item.active .page-link {
    background: #f5407e;
    border-color: #f5407e;
    color: white;
}

/* Modal Styles */
.modal-content {
    border-radius: 15px;
    border: none;
}

.modal-header {
    background: #f8f9fa;
    border-bottom: 1px solid #e9ecef;
    border-radius: 15px 15px 0 0;
}

.show-details, .photo-details {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 10px;
    margin-top: 15px;
}

.show-details h6, .photo-details h6 {
    color: #f5407e;
    margin-bottom: 10px;
}

/* Responsive */
@media (max-width: 768px) {
    .gallery-content .title {
        font-size: 16px;
    }

    .gallery-actions {
        gap: 10px;
    }

    .gallery-btn {
        width: 40px;
        height: 40px;
    }
}

/* Lightbox enhancement */
.lightbox-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.9);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 9999;
}

.lightbox-content {
    max-width: 90%;
    max-height: 90%;
    position: relative;
}

.lightbox-image {
    width: 100%;
    height: auto;
    border-radius: 10px;
}

.lightbox-close {
    position: absolute;
    top: -40px;
    right: 0;
    color: white;
    font-size: 30px;
    cursor: pointer;
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(255,255,255,0.1);
    border-radius: 50%;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Simple lightbox
    const lightboxBtns = document.querySelectorAll('.lightbox-btn');

    lightboxBtns.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const imgSrc = this.getAttribute('href');
            const title = this.getAttribute('data-title');

            createLightbox(imgSrc, title);
        });
    });

    function createLightbox(imgSrc, title) {
        const lightbox = document.createElement('div');
        lightbox.className = 'lightbox-overlay';
        lightbox.innerHTML = `
            <div class="lightbox-content">
                <div class="lightbox-close">&times;</div>
                <img src="${imgSrc}" alt="${title}" class="lightbox-image">
                <div class="lightbox-title" style="color: white; text-align: center; margin-top: 15px; font-size: 18px;">${title}</div>
            </div>
        `;

        document.body.appendChild(lightbox);
        document.body.style.overflow = 'hidden';

        // Close lightbox
        lightbox.addEventListener('click', function(e) {
            if(e.target === lightbox || e.target.classList.contains('lightbox-close')) {
                document.body.removeChild(lightbox);
                document.body.style.overflow = 'auto';
            }
        });

        // Close with ESC key
        document.addEventListener('keydown', function(e) {
            if(e.key === 'Escape') {
                if(document.querySelector('.lightbox-overlay')) {
                    document.body.removeChild(document.querySelector('.lightbox-overlay'));
                    document.body.style.overflow = 'auto';
                }
            }
        });
    }
});
</script>
@endpush
