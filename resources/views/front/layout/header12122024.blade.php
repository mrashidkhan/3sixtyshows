<!DOCTYPE html>
<html lang="en">
<!-- Main Layout -->
@include('front.layout.header')
@yield('slider')
@yield('content')
@include('front.layout.footer')

<!-- Back to Top Button -->
<a href="#" class="btn btn-lg btn-primary btn-lg-square rounded-circle back-to-top" aria-label="Back to Top">
    <i class="bi bi-arrow-up"></i>
</a>

<!-- JavaScript Libraries -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('admin_themelib/wow/wow.min.js') }}"></script>
<script src="{{ asset('admin_themelib/easing/easing.min.js') }}"></script>
<script src="{{ asset('admin_themelib/waypoints/waypoints.min.js') }}"></script>
<script src="{{ asset('admin_themelib/owlcarousel/owl.carousel.min.js') }}"></script>
<script src="{{ asset('admin_theme/js/main.js') }}"></script>

@stack('footer-script')
</body>
</html>
