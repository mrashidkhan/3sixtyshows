<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <meta name="keywords" content="Rangotsav Holi Festival Tickets, Dew Events Center, Holi Celebration Tickets" />
    <meta property="og:title" content="3Sixty Shows - The Premier Choice for Entertainment!" />
    <meta property="og:type" content="website" />
    <meta property="og:image" content="http://3sixtyshows.test/images/favicon.png" />
    <meta property="og:url" content="http://3sixtyshows.test" />
    <meta property="og:site_name" content="3sixtyshows" />
    <meta property="og:description" content="At 3Sixty Shows, we go beyond organizing events; we create unforgettable experiences. Embark on an extraordinary journey with us, where imagination has no limits and entertainment reaches new heights. ENGAGING CONCERTS, LASTING MEMORIES, EASY TICKETING, HASSLE-FREE ACCESS, STRATEGIC PARTNERSHIPS &amp; POWERFUL ADVERTISING." />
    <meta name="twitter:card" content="summary" />
    <meta name="twitter:site" content="3sixtyshows" />
    <meta name="twitter:title" content="3Sixty Shows - The Premier Choice for Entertainment!" />
    <meta name="twitter:description" content="At 3Sixty Shows, we go beyond organizing events; we create unforgettable experiences. Embark on an extraordinary journey with us, where imagination has no limits and entertainment reaches new heights. ENGAGING CONCERTS, LASTING MEMORIES, EASY TICKETING, HASSLE-FREE ACCESS, STRATEGIC PARTNERSHIPS &amp; POWERFUL ADVERTISING." />
    <meta name="twitter:image" content="http://3sixtyshows.test/images/favicon.png" />
    <meta name="twitter:url" content="http://3sixtyshows.test" />
    <meta name="description" content="At 3Sixty Shows, we go beyond organizing events; we create unforgettable experiences. Embark on an extraordinary journey with us, where imagination has no limits and entertainment reaches new heights. ENGAGING CONCERTS, LASTING MEMORIES, EASY TICKETING, HASSLE-FREE ACCESS, STRATEGIC PARTNERSHIPS &amp; POWERFUL ADVERTISING." />

    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/animate.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/flaticon.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/magnific-popup.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/odometer.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/owl.carousel.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/owl.theme.default.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/nice-select.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/jquery.animatedheadline.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/main.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/logo-slider.css') }}">

    <link rel="shortcut icon" href="{{ asset('assets/images/favicon.png') }}" type="image/x-icon">

    <title>3SixtyShows</title>

</head>

<body>
    <!-- ==========Preloader========== -->
    <div class="preloader">
        <div class="preloader-inner">
            <div class="preloader-icon">
                <span></span>
                <span></span>
            </div>
        </div>
    </div>
    <!-- ==========Preloader========== -->
    <!-- ==========Overlay========== -->
    <div class="overlay"></div>
    <a href="#0" class="scrollToTop">
        <i class="fas fa-angle-up"></i>
    </a>
    <!-- ==========Overlay========== -->

    <!-- ==========Header-Section========== -->
    <header class="header-section">
        <div class="container">
            <div class="header-wrapper">
                <div class="logo">
                    <a href="{{ route('index') }}">
                        <img src="{{ asset('assets/images/logo/favicon.png') }}" alt="logo">
                    </a>
                </div>
                <ul class="menu">
                    <li>
                        <a href="{{ route('index') }}" class="{{ Route::is('index') ? 'active' : '' }}">Home</a>
                    </li>
                    <li>
                        <a href="{{ route('activeevents') }}" class="{{ Route::is('activeevents') ? 'active' : '' }}">Events</a>
                    </li>
                    <li>
                        <a href="{{ route('posters') }}" class="{{ Route::is('posters') ? 'active' : '' }}">Posters</a>
                    </li>
                    <li class="{{ Route::is('galleries.index') || Route::is('video-galleries') ? 'active' : '' }}">
                        <a href="#0" class="{{ Route::is('galleries.index') || Route::is('video-galleries') ? 'active' : '' }}">Galleries</a>
                        <ul class="submenu">
                            <li>
                                <a href="{{ route('galleries.index') }}" class="{{ Route::is('galleries.index') ? 'active' : '' }}">Photo Galleries</a>
                            </li>
                            <li>
                                <a href="{{ route('video-galleries') }}" class="{{ Route::is('video-galleries') ? 'active' : '' }}">Video Galleries</a>
                            </li>
                        </ul>
                    </li>
                    <li>
                        <a href="{{ route('aboutus') }}" class="{{ Route::is('aboutus') ? 'active' : '' }}">About Us</a>
                    </li>
                    {{-- <li>
                        <a href="{{ route('contact') }}" class="{{ Route::is('contact') ? 'active' : '' }}">Contact Us</a>
                    </li> --}}
                    @auth
                        {{-- <li>
                            <a href="{{ route('bookings.my') }}" class="{{ Route::is('bookings.my') ? 'active' : '' }}">My Bookings</a>
                        </li> --}}
                    @endauth
                    <li class="header-button pr-0">
                        @auth
                            <a href="{{ route('user_logout') }}"
                               onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                               class="btn btn-link">Logout</a>
                            <form id="logout-form" action="{{ route('user_logout') }}" method="GET" style="display: none;">
                                @csrf
                            </form>
                        @else
                            <a href="{{ route('user_login') }}">Join Us</a>
                        @endauth
                    </li>
                </ul>
                <div class="header-bar d-lg-none">
					<span></span>
					<span></span>
					<span></span>
				</div>
            </div>
        </div>
    </header>
    <!-- ==========Header-Section========== -->
</body>
</html>
