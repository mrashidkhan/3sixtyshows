@extends('layouts.master')

@section('content')

<!-- ==========Banner-Section========== -->
<section class="banner-section" style="padding-top:150px; padding-bottom:0px;">
    <div class="banner-bg bg_img bg-fixed" data-background="{{ asset('assets/images/banner/banner01.jpg') }}"></div>
    <div class="container">
        <div class="banner-content">
            <h1 class="title cd-headline clip" style="font-size:52px;">
                <span class="d-block" style="width:100%;">Video Gallery</span>
            </h1>
        </div>
    </div>
</section>

<section class="content">
    <div class="container" style="padding-right:8px; padding-left:8px; padding-top:20px; padding-bottom:130px;">
        <div class="row">
            @forelse($videos->where('is_active', true)->whereNotNull('youtubelink')->where('youtubelink', '!=', '') as $video)
                @php
                    $youtubeId = null;
                    $youtubeUrl = $video->youtubelink;

                    // Extract YouTube ID from various URL formats
                    $patterns = [
                        '/(?:youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/embed\/|youtube\.com\/v\/)([a-zA-Z0-9_-]{11})/',
                        '/^([a-zA-Z0-9_-]{11})$/' // Direct video ID
                    ];

                    foreach ($patterns as $pattern) {
                        if (preg_match($pattern, $youtubeUrl, $matches)) {
                            $youtubeId = $matches[1];
                            break;
                        }
                    }
                @endphp

                @if($youtubeId)
                <div class="col-md-4" style="float:left; padding-bottom:10px;">
                    <div class="box">
                        <div class="box-img" style="padding-bottom:10px; padding-top:10px;">
                            <iframe id="video-{{ $loop->iteration }}"
                                    width="350"
                                    height="325"
                                    src="https://www.youtube.com/embed/{{ $youtubeId }}"
                                    frameborder="0"
                                    allowfullscreen>
                            </iframe>
                        </div>
                    </div>
                </div>
                @endif
            @empty
                <div class="col-12">
                    <div class="no-videos-message">
                        <div class="text-center" style="padding: 80px 20px;">
                            <span id="loadingtext">Please Wait While Loading..</span>
                        </div>
                    </div>
                </div>
            @endforelse
        </div>
    </div>
</section>
<!--Gallery End-->

@endsection

@push('styles')
<style>
.content {
    background: #f8f9fa;
    min-height: 100vh;
}

.box {
    background: #fff;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
    margin-bottom: 20px;
}

.box:hover {
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    transform: translateY(-5px);
}

.box-img {
    position: relative;
    width: 100%;
    display: flex;
    justify-content: center;
    align-items: center;
}

iframe {
    border-radius: 8px;
    border: none;
    display: block;
}

.no-videos-message {
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    margin: 20px 0;
}

/* Responsive Design */
@media (max-width: 1200px) {
    iframe {
        width: 320px !important;
        height: 280px !important;
    }
}

@media (max-width: 992px) {
    iframe {
        width: 300px !important;
        height: 250px !important;
    }

    .col-md-4 {
        flex: 0 0 50%;
        max-width: 50%;
    }
}

@media (max-width: 768px) {
    .container {
        padding-right: 15px !important;
        padding-left: 15px !important;
    }

    .col-md-4 {
        flex: 0 0 100%;
        max-width: 100%;
        margin-bottom: 20px;
    }

    iframe {
        width: 100% !important;
        max-width: 350px !important;
        height: 200px !important;
    }
}

@media (max-width: 576px) {
    iframe {
        width: 100% !important;
        height: 180px !important;
    }

    .banner-content h1 {
        font-size: 36px !important;
    }
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {

    // Handle iframe loading and errors
    const iframes = document.querySelectorAll('iframe');
    iframes.forEach(iframe => {
        iframe.addEventListener('load', function() {
            console.log('Video loaded successfully');
        });

        iframe.addEventListener('error', function() {
            console.warn('Failed to load iframe:', this.src);
        });
    });
});
</script>
@endpush
