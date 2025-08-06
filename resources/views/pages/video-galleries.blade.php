@extends('layouts.master')

@section('content')

<!-- ==========Banner-Section========== -->
<section class="banner-section" style="padding-top:150px; padding-bottom:0px;">
    <div class="banner-bg bg_img bg-fixed" data-background="{{ asset('assets/images/banner/banner01.jpg') }}"></div>
    <div class="container">
        <div class="banner-content">
            <h1 class="title cd-headline clip" style="font-size:52px;">
                <span class="d-block" style="width:100%;">Video Galleries</span>
            </h1>
            <p style="font-size:25px">Experience the Energy - Video Highlights from Our Shows</p>
        </div>
    </div>
</section>

<!-- ==========Video Galleries Section========== -->
<section class="video-gallery-section padding-top padding-bottom">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="section-header-3 text-center">
                    <span class="cate">Motion & Music</span>
                    <h2 class="title">Live Performance Highlights</h2>
                    <p>Immerse yourself in the electrifying atmosphere of our shows through our curated collection of performance videos, behind-the-scenes content, and exclusive artist interviews.</p>
                </div>
            </div>
        </div>


        <!-- Video Gallery Grid -->
        <div class="video-wrapper">
            <div class="row g-4" id="video-container">
                @forelse($videoGalleries as $video)
                    <div class="col-lg-6 col-md-6 video-item {{ $video->is_featured ? 'featured' : '' }}"
                         data-category="{{ $video->is_featured ? 'featured' : 'regular' }}">
                        <div class="video-card">
                            <div class="video-thumb">
                                @if($video->thumbnail)
                                    <img src="{{ asset('storage/' . $video->thumbnail) }}" alt="{{ $video->title }}"
                                         onerror="this.src='{{ asset('assets/images/video/placeholder.jpg') }}'">
                                @else
                                    <div class="video-placeholder">
                                        <i class="flaticon-play"></i>
                                    </div>
                                @endif

                                <div class="video-overlay">
                                    <div class="video-content">
                                        <h4 class="title">{{ $video->title }}</h4>
                                        @if($video->show)
                                            <span class="show-name">{{ $video->show->title }}</span>
                                        @endif
                                        <div class="video-actions">
                                            <button class="video-play-btn"
                                                    data-video-url="{{ $video->video_url }}"
                                                    data-video-title="{{ $video->title }}">
                                                <i class="flaticon-play"></i>
                                                <span>Play Video</span>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                @if($video->is_featured)
                                    <div class="featured-badge">
                                        <i class="flaticon-star"></i>
                                        <span>Featured</span>
                                    </div>
                                @endif

                                <div class="play-icon">
                                    <i class="flaticon-play"></i>
                                </div>
                            </div>

                            <div class="video-info">
                                <h5 class="title">{{ $video->title }}</h5>
                                @if($video->show)
                                    <div class="show-info">
                                        <i class="flaticon-calendar"></i>
                                        <span>{{ $video->show->title }}</span>
                                    </div>
                                @endif
                                @if($video->description)
                                    <p class="description">{{ Str::limit($video->description, 100) }}</p>
                                @endif
                                <div class="video-meta">
                                    <span class="video-type">{{ ucfirst($video->video_type ?? 'Performance') }}</span>
                                    <span class="video-date">{{ $video->created_at->format('M d, Y') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="no-videos">
                            <div class="no-videos-content">
                                <div class="no-videos-icon">
                                    <i class="flaticon-video"></i>
                                </div>
                                <h3 class="title">No Videos Available</h3>
                                <p>We're currently building our video collection. Check back soon for amazing performance footage and behind-the-scenes content!</p>
                                <a href="{{ route('activeevents') }}" class="custom-button">
                                    <i class="flaticon-right-arrow"></i> Browse Shows
                                </a>
                            </div>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Load More Button -->
        @if($videoGalleries->hasMorePages())
            <div class="load-more-area text-center">
                <button id="loadMoreBtn" class="custom-button load-more-btn" data-page="2">
                    <i class="flaticon-refresh"></i> Load More Videos
                </button>
            </div>
        @endif
    </div>
</section>

<!-- Video Modal -->
<div class="modal fade" id="videoModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="videoModalTitle">Video Player</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                <div class="video-player-container">
                    <div id="videoPlayerContent"></div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
/* Video Gallery Styles */
.video-filter-area {
    margin-bottom: 50px;
}

.filter-wrapper {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 15px;
    border: 1px solid #e9ecef;
}

.filter-buttons {
    display: flex;
    justify-content: center;
    flex-wrap: wrap;
    gap: 15px;
}

.filter-btn {
    padding: 12px 25px;
    background: #fff;
    color: #495057;
    border: 2px solid #e9ecef;
    border-radius: 25px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s ease;
}

.filter-btn:hover,
.filter-btn.active {
    background: #f5407e;
    color: white;
    border-color: #f5407e;
    transform: translateY(-2px);
}

.video-item {
    transition: all 0.3s ease;
}

.video-card {
    background: #fff;
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 5px 20px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
    position: relative;
}

.video-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 15px 40px rgba(0,0,0,0.2);
}

.video-thumb {
    position: relative;
    overflow: hidden;
    aspect-ratio: 16/9;
    cursor: pointer;
}

.video-thumb img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: all 0.3s ease;
}

.video-placeholder {
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 48px;
}

.video-card:hover .video-thumb img {
    transform: scale(1.1);
}

.video-overlay {
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

.video-card:hover .video-overlay {
    opacity: 1;
}

.video-content {
    text-align: center;
    color: white;
    padding: 20px;
}

.video-content .title {
    font-size: 20px;
    font-weight: 600;
    margin-bottom: 8px;
}

.video-content .show-name {
    font-size: 14px;
    opacity: 0.9;
    margin-bottom: 20px;
    display: block;
}

.video-play-btn {
    background: rgba(255,255,255,0.2);
    border: 2px solid rgba(255,255,255,0.3);
    border-radius: 50px;
    padding: 15px 25px;
    color: white;
    display: flex;
    align-items: center;
    gap: 10px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s ease;
}

.video-play-btn:hover {
    background: white;
    color: #f5407e;
    transform: scale(1.05);
}

.play-icon {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 60px;
    height: 60px;
    background: rgba(245, 64, 126, 0.9);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 24px;
    transition: all 0.3s ease;
    z-index: 1;
}

.video-card:hover .play-icon {
    opacity: 0;
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

.video-info {
    padding: 20px;
}

.video-info .title {
    font-size: 18px;
    font-weight: 600;
    color: #1e2328;
    margin-bottom: 10px;
}

.show-info {
    display: flex;
    align-items: center;
    color: #6c757d;
    font-size: 14px;
    margin-bottom: 10px;
}

.show-info i {
    margin-right: 8px;
    color: #f5407e;
}

.video-info .description {
    color: #6c757d;
    font-size: 14px;
    line-height: 1.6;
    margin-bottom: 15px;
}

.video-meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-top: 15px;
    border-top: 1px solid #e9ecef;
}

.video-type {
    background: #e3f2fd;
    color: #1976d2;
    padding: 4px 12px;
    border-radius: 15px;
    font-size: 12px;
    font-weight: 500;
}

.video-date {
    color: #6c757d;
    font-size: 12px;
}

.no-videos {
    text-align: center;
    padding: 80px 30px;
    background: #fff;
    border-radius: 15px;
    border: 1px solid #e9ecef;
}

.no-videos-icon {
    font-size: 64px;
    color: #e9ecef;
    margin-bottom: 20px;
}

.no-videos .title {
    font-size: 24px;
    font-weight: 600;
    color: #495057;
    margin-bottom: 15px;
}

.no-videos p {
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

.load-more-area {
    margin-top: 50px;
}

.load-more-btn {
    padding: 15px 35px;
    font-size: 16px;
}

/* Video Modal */
.modal-xl {
    max-width: 90%;
}

.video-player-container {
    position: relative;
    width: 100%;
    height: 0;
    padding-bottom: 56.25%; /* 16:9 aspect ratio */
    background: #000;
}

.video-player-container iframe,
.video-player-container video {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
}

/* Responsive */
@media (max-width: 768px) {
    .filter-buttons {
        justify-content: center;
    }

    .filter-btn {
        padding: 10px 20px;
        font-size: 14px;
    }

    .video-content .title {
        font-size: 16px;
    }

    .video-play-btn {
        padding: 12px 20px;
        font-size: 14px;
    }

    .play-icon {
        width: 50px;
        height: 50px;
        font-size: 20px;
    }

    .modal-xl {
        max-width: 95%;
        margin: 10px;
    }
}

@media (max-width: 576px) {
    .video-meta {
        flex-direction: column;
        gap: 10px;
        align-items: flex-start;
    }
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Video play functionality
    const videoPlayBtns = document.querySelectorAll('.video-play-btn');
    const videoThumbs = document.querySelectorAll('.video-thumb');
    const videoModal = new bootstrap.Modal(document.getElementById('videoModal'));

    // Handle play button clicks
    videoPlayBtns.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.stopPropagation();
            const videoUrl = this.getAttribute('data-video-url');
            const videoTitle = this.getAttribute('data-video-title');

            openVideoModal(videoUrl, videoTitle);
        });
    });

    // Handle thumbnail clicks
    videoThumbs.forEach(thumb => {
        thumb.addEventListener('click', function() {
            const playBtn = this.querySelector('.video-play-btn');
            if(playBtn) {
                const videoUrl = playBtn.getAttribute('data-video-url');
                const videoTitle = playBtn.getAttribute('data-video-title');

                openVideoModal(videoUrl, videoTitle);
            }
        });
    });

    function openVideoModal(videoUrl, videoTitle) {
        const modalTitle = document.getElementById('videoModalTitle');
        const playerContent = document.getElementById('videoPlayerContent');

        modalTitle.textContent = videoTitle;

        // Check if it's a YouTube URL
        if(videoUrl.includes('youtube.com') || videoUrl.includes('youtu.be')) {
            const videoId = extractYouTubeId(videoUrl);
            if(videoId) {
                playerContent.innerHTML = `
                    <iframe src="https://www.youtube.com/embed/${videoId}?autoplay=1&rel=0"
                            frameborder="0"
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                            allowfullscreen>
                    </iframe>
                `;
            }
        } else if(videoUrl.includes('vimeo.com')) {
            const videoId = extractVimeoId(videoUrl);
            if(videoId) {
                playerContent.innerHTML = `
                    <iframe src="https://player.vimeo.com/video/${videoId}?autoplay=1"
                            frameborder="0"
                            allow="autoplay; fullscreen; picture-in-picture"
                            allowfullscreen>
                    </iframe>
                `;
            }
        } else {
            // Direct video file
            playerContent.innerHTML = `
                <video controls autoplay>
                    <source src="${videoUrl}" type="video/mp4">
                    Your browser does not support the video tag.
                </video>
            `;
        }

        videoModal.show();
    }

    function extractYouTubeId(url) {
        const regExp = /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|&v=)([^#&?]*).*/;
        const match = url.match(regExp);
        return (match && match[2].length === 11) ? match[2] : null;
    }

    function extractVimeoId(url) {
        const regExp = /(?:vimeo)\.com.*(?:videos|video|channels|)\/([\d]+)/i;
        const match = url.match(regExp);
        return match ? match[1] : null;
    }

    // Clear video on modal close
    document.getElementById('videoModal').addEventListener('hidden.bs.modal', function() {
        document.getElementById('videoPlayerContent').innerHTML = '';
    });

    // Load more functionality
    const loadMoreBtn = document.getElementById('loadMoreBtn');
    if(loadMoreBtn) {
        loadMoreBtn.addEventListener('click', function() {
            const page = this.getAttribute('data-page');

            // Show loading state
            this.innerHTML = '<i class="flaticon-refresh"></i> Loading...';
            this.disabled = true;

            // Simulate loading (replace with actual AJAX call)
            setTimeout(() => {
                // Here you would typically make an AJAX call to load more videos
                // For now, we'll just hide the button
                this.style.display = 'none';
            }, 1500);
        });
    }
});
</script>
@endpush
