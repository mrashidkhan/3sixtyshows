
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <!-- Meta, title, CSS, favicons, etc. -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="icon" href="{{asset('img/favicon.ico')}}" type="image/ico" />

    <title>pattokinaturals!</title>

    <!-- Bootstrap -->
    <link href="{{asset('admin_theme/vendors/bootstrap/dist/css/bootstrap.min.css')}}" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="{{asset('admin_theme/vendors/font-awesome/css/font-awesome.min.css')}}" rel="stylesheet">
    <!-- NProgress -->
    <link href="{{asset('admin_theme/vendors/nprogress/nprogress.css')}}" rel="stylesheet">
    <!-- iCheck -->
    <link href="{{asset('admin_theme/vendors/iCheck/skins/flat/green.css')}}" rel="stylesheet">

    <!-- bootstrap-progressbar -->
    <link href="{{asset('admin_theme/vendors/bootstrap-progressbar/css/bootstrap-progressbar-3.3.4.min.css')}}" rel="stylesheet">
    <!-- JQVMap -->
    <link href="{{asset('admin_theme/vendors/jqvmap/dist/jqvmap.min.css')}}" rel="stylesheet"/>
    <!-- bootstrap-daterangepicker -->
    <link href="{{asset('admin_theme/vendors/bootstrap-daterangepicker/daterangepicker.css')}}" rel="stylesheet">

    <!-- Custom Theme Style -->
    <link href="{{asset('admin_theme/build/css/custom.min.css')}}" rel="stylesheet">
  </head>
<body>
    <!-- Navbar Start -->
    <div class="container-fluid fixed-top px-0 wow fadeIn" data-wow-delay="0.1s">
        <!-- Top Bar Start -->
        <div class="top-bar row gx-0 align-items-center bg-success text-white d-none d-md-flex">
            <div class="col-md-6 px-5 text-start">
                <small><i class="fa fa-map-marker-alt me-2 text-warning"></i>Rosa Tibba Pattoki 55300, Kasur, Pakistan</small>
                <small class="ms-4"><i class="fa fa-envelope me-2"></i>pattokinaturals@gmail.com</small>
            </div>

            <div class="col-md-6 px-5 text-end">
                <span class="navbar-text fw-bold text-warning">Free shipping over Rs.5,000/-</span>
            </div>
        </div>
        <!-- Top Bar End -->


        <!-- Navbar Start -->
        <nav class="navbar navbar-expand-lg navbar-light bg-white py-2 px-lg-5">
            <a href="{{ route('home') }}" class="navbar-brand ms-4 ms-lg-0">
                <img src="{{asset('img/logo.png')}}" alt="Pattoki Naturals Logo" class="img-fluid me-3" style="width: auto; height: 50px;">
            </a>
            <button class="navbar-toggler me-4" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarCollapse">
                <div class="navbar-nav ms-auto p-2 p-lg-0">
                    <a href="{{ route('home') }}" class="nav-item nav-link text-success fw-bold">Home</a>
                    <a href="{{ route('shop') }}" class="nav-item nav-link text-success">Products</a>
                    <a href="{{ route('aboutus') }}" class="nav-item nav-link text-success">About Us</a>
                    <a href="{{ route('contactus') }}" class="nav-item nav-link text-success">Contact Us</a>
                    <a href="{{ route('cart') }}" class="nav-item nav-link text-success"><i class="bi bi-cart fs-6"></i>Cart</a>
                    @if (Auth::user())
                    <a href="{{ route('user_logout') }}" class="nav-item nav-link text-success"><i class="fas fa-sign-in-alt"></i> Log Out</a>
                    @else
                    <a href="{{ route('user_login') }}" class="nav-item nav-link text-success"><i class="fas fa-sign-in-alt"></i> Login</a>
                    @endif
                </div>
            </div>
        </nav>
        <!-- Navbar End -->
    </div>
    <!-- Navbar End -->



