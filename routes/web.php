<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use App\Http\Controllers\PageController;
use App\Http\Controllers\ServicesController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\BaseController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\Admin\ShowController;
use App\Http\Controllers\Admin\VenueController;
use App\Http\Controllers\Admin\PhotoGalleryController;
use App\Http\Controllers\Admin\VideoGalleryController;
use App\Http\Controllers\Admin\PhotosinGalleryController;
use App\Http\Controllers\Admin\VideosinGalleryController;
use App\Http\Controllers\Admin\CustomerController;




// Public Routes
Route::get('/seatselection', [PageController::class, 'selection'])->name('seatselection');
Route::get('/', [PageController::class, 'index'])->name('index');
Route::get('/aboutus', [PageController::class, 'aboutus'])->name('aboutus');
Route::get('/contact', [PageController::class, 'contact'])->name('contact');
Route::get('/gallery', [PageController::class, 'gallery'])->name('gallery');
Route::get('/activeevents', [PageController::class, 'activeevents'])->name('activeevents');
Route::get('/posters', [PageController::class, 'posters'])->name('posters');
Route::get('/upcomingposters', [PageController::class, 'upcomingposters'])->name('upcomingposters');
Route::get('/service', [PageController::class, 'service'])->name('service');
Route::get('/team', [PageController::class, 'team'])->name('team');
Route::get('/testimonials', [PageController::class, 'testimonials'])->name('testimonials');
Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');
Route::get('/privacy-policy', [PageController::class, 'privacyPolicy'])
    ->name('privacy.policy');

// Client-side Gallery Routes (Public)
Route::get('/photo-galleries', [PhotoGalleryController::class, 'clientIndex'])->name('photo-galleries');
Route::get('/video-galleries', [VideoGalleryController::class, 'clientIndex'])->name('video-galleries');

// Public Show Details
Route::get('/shows/{slug}', [PageController::class, 'showDetails'])->name('show.details');

Route::get('/shows/{slug}/booking', [PageController::class, 'showBooking'])->name('show.booking');

// Base Controller's routes (User Authentication)
Route::get('user/login', [BaseController::class, 'loginCheck'])->name('user_login');
Route::post('user/login', [BaseController::class, 'loginCheck'])->name('logincheck');
Route::post('user/register', [BaseController::class, 'user_store'])->name('user_store');
Route::get('user/logout', [BaseController::class, 'logout'])->name('user_logout');

// Admin Authentication Routes
Route::get('/admin/login', [AdminController::class, 'login'])->name('admin.login');
Route::post('/admin/login', [AdminController::class, 'makeLogin'])->name('admin.makeLogin');
Route::get('/login', function () {
    return redirect()->route('admin.login');
})->name('login');

// Admin Routes (Protected by Auth Middleware)
Route::group(['middleware' => 'auth'], function () {

    // Admin Dashboard
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/admin/logout', [AdminController::class, 'logout'])->name('admin.logout');

    // Show Category Management
    Route::get('/showcategories', [CategoryController::class, 'index'])->name('showcategory.list');
    Route::get('/showcategory/add', [CategoryController::class, 'create'])->name('showcategory.create');
    Route::post('/showcategory/add', [CategoryController::class, 'store'])->name('showcategory.store');
    Route::get('/showcategories/edit/{id}', [CategoryController::class, 'edit'])->name('showcategory.edit');
    Route::put('/showcategories/update/{id}', [CategoryController::class, 'update'])->name('showcategory.update');
    Route::post('/showcategory/delete/{id}', [CategoryController::class, 'destroy'])->name('showcategory.delete');

    // Show Management
    Route::get('/shows', [ShowController::class, 'index'])->name('show.index');
    Route::get('/show/add', [ShowController::class, 'create'])->name('show.create');
    Route::post('/show/add', [ShowController::class, 'store'])->name('show.store');
    Route::get('/show/edit/{id}', [ShowController::class, 'edit'])->name('show.edit');
    Route::put('/show/update/{id}', [ShowController::class, 'update'])->name('show.update');
    Route::post('/show/delete/{id}', [ShowController::class, 'destroy'])->name('show.delete');
    Route::get('/admin/show/{id}', [ShowController::class, 'show'])->name('shows.show');

    // Venue Management
    Route::get('/venues', [VenueController::class, 'index'])->name('venues.index');
    Route::get('/venue/add', [VenueController::class, 'create'])->name('venue.create');
    Route::post('/venue/add', [VenueController::class, 'store'])->name('venue.store');
    Route::get('/venue/edit/{id}', [VenueController::class, 'edit'])->name('venue.edit');
    Route::put('/venue/update/{id}', [VenueController::class, 'update'])->name('venue.update');
    Route::post('/venue/delete/{id}', [VenueController::class, 'destroy'])->name('venue.delete');

    // Photo Gallery Management
    Route::get('/photogallery/list', [PhotoGalleryController::class, 'index'])->name('photogallery.list');
    Route::get('/photogallery/create', [PhotoGalleryController::class, 'create'])->name('photogallery.create');
    Route::post('/photogallery/create', [PhotoGalleryController::class, 'store'])->name('photogallery.store');
    Route::get('/photogallery/edit/{id}', [PhotoGalleryController::class, 'edit'])->name('photogallery.edit');
    Route::post('/photogallery/edit/{id}', [PhotoGalleryController::class, 'update'])->name('photogallery.update');
    Route::get('/photogallery/{id}', [PhotoGalleryController::class, 'show'])->name('photogallery.show');
    Route::post('/photogallery/delete/{id}', [PhotoGalleryController::class, 'destroy'])->name('photogallery.delete');

    // Photos in Gallery Management
    Route::get('/photosingallery/list', [PhotosinGalleryController::class, 'index'])->name('photosingallery.list');
    Route::get('/photosingallery/create', [PhotosinGalleryController::class, 'create'])->name('photosingallery.create');
    Route::post('/photosingallery/create', [PhotosinGalleryController::class, 'store'])->name('photosingallery.store');
    Route::get('/photosingallery/edit/{id}', [PhotosinGalleryController::class, 'edit'])->name('photosingallery.edit');
    Route::post('/photosingallery/edit/{id}', [PhotosinGalleryController::class, 'update'])->name('photosingallery.update');
    Route::get('/photosingallery/{id}', [PhotosinGalleryController::class, 'show'])->name('photosingallery.show');
    Route::post('/photosingallery/delete/{id}', [PhotosinGalleryController::class, 'destroy'])->name('photosingallery.delete');

    // Videos in Gallery Management
    Route::get('/videosingallery/list', [VideosinGalleryController::class, 'index'])->name('videosingallery.list');
    Route::get('/videosingallery/create', [VideosinGalleryController::class, 'create'])->name('videosingallery.create');
    Route::post('/videosingallery/create', [VideosinGalleryController::class, 'store'])->name('videosingallery.store');
    Route::get('/videosingallery/edit/{id}', [VideosinGalleryController::class, 'edit'])->name('videosingallery.edit');
    Route::post('/videosingallery/edit/{id}', [VideosinGalleryController::class, 'update'])->name('videosingallery.update');
    Route::get('/videosingallery/{id}', [VideosinGalleryController::class, 'show'])->name('videosingallery.show');
    // Route::delete('/videosingallery/delete/{id}', [VideosinGalleryController::class, 'destroy'])->name('videosingallery.delete');
    // routes/web.php
    Route::delete('/videosingallery/delete/{id}', [VideosinGalleryController::class, 'destroy'])->name('videosingallery.delete');

    // Video Gallery Management
    Route::get('/videogallery/list', [VideoGalleryController::class, 'index'])->name('videogallery.list');
    Route::get('/videogallery/create', [VideoGalleryController::class, 'create'])->name('videogallery.create');
    Route::post('/videogallery/create', [VideoGalleryController::class, 'store'])->name('videogallery.store');
    Route::get('/videogallery/edit/{id}', [VideoGalleryController::class, 'edit'])->name('videogallery.edit');
    Route::put('/videogallery/edit/{id}', [VideoGalleryController::class, 'update'])->name('videogallery.update');
    Route::get('/videogallery/{id}', [VideoGalleryController::class, 'show'])->name('videogallery.show');
    Route::post('/videogallery/delete/{id}', [VideoGalleryController::class, 'destroy'])->name('videogallery.delete');

});

// Customer Routes (Admin Only)
Route::group(['prefix' => 'admin', 'middleware' => ['auth']], function () {
    Route::get('/customers', [CustomerController::class, 'index'])->name('customer.index');
    Route::get('/customers/create', [CustomerController::class, 'create'])->name('customer.create');
    Route::post('/customers', [CustomerController::class, 'store'])->name('customer.store');
    Route::get('/customers/{customer}', [CustomerController::class, 'show'])->name('customer.show');
    Route::get('/customers/{customer}/edit', [CustomerController::class, 'edit'])->name('customer.edit');
    Route::put('/customers/{customer}', [CustomerController::class, 'update'])->name('customer.update');
    Route::get('/customers/{customer}/delete', [CustomerController::class, 'delete'])->name('customer.delete');
    Route::delete('/customers/{customer}', [CustomerController::class, 'destroy'])->name('customer.destroy');
});

// ==================== BOOKING SYSTEM ROUTES ====================
// Only include if you have these controllers available



use App\Http\Controllers\OptimizedBookingController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\SeatMapController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\Admin\AdminBookingController;
use App\Http\Controllers\Admin\TicketTypeController;

// Booking Routes (Authenticated Users) - OPTIMIZED VERSION WITH MONITORING
Route::middleware(['auth'])->group(function () {

    // Main Booking Flow - Using OptimizedBookingController
    Route::get('/shows/{show}/book', [OptimizedBookingController::class, 'selectSeats'])->name('booking.select-seats');
    Route::post('/shows/{show}/book/seats', [OptimizedBookingController::class, 'reserveSeats'])->name('booking.reserve-seats');
    Route::get('/shows/{show}/book/checkout', [OptimizedBookingController::class, 'checkout'])->name('booking.checkout');
    Route::post('/shows/{show}/book/confirm', [OptimizedBookingController::class, 'confirmBooking'])->name('booking.confirm');

    // AJAX Routes for Seat Management - OPTIMIZED
    Route::get('/api/shows/{show}/seats', [SeatMapController::class, 'getSeats'])->name('api.seats.get');
    Route::post('/api/shows/{show}/seats/reserve', [SeatMapController::class, 'reserveSeats'])->name('api.seats.reserve');
    Route::delete('/api/shows/{show}/seats/release', [SeatMapController::class, 'releaseSeats'])->name('api.seats.release');
    Route::get('/api/shows/{show}/availability', [OptimizedBookingController::class, 'getSeatsAvailability'])->name('api.seats.availability');
    Route::get('/api/shows/{show}/seat-updates', [OptimizedBookingController::class, 'getSeatStatusUpdates'])->name('api.seats.updates');

    // User Booking Management - OPTIMIZED
    Route::get('/my-bookings', [OptimizedBookingController::class, 'myBookings'])->name('bookings.my');
    Route::get('/bookings/{booking}', [OptimizedBookingController::class, 'show'])->name('bookings.show');
    Route::post('/bookings/{booking}/cancel', [OptimizedBookingController::class, 'cancel'])->name('bookings.cancel');

    // Ticket Management
    Route::get('/bookings/{booking}/tickets', [TicketController::class, 'download'])->name('tickets.download');
    Route::get('/bookings/{booking}/tickets/pdf', [TicketController::class, 'downloadPdf'])->name('tickets.pdf');
    Route::get('/tickets/{ticket}/qr', [TicketController::class, 'qrCode'])->name('tickets.qr');
    Route::get('/tickets/{ticket}/view', [TicketController::class, 'view'])->name('tickets.view');
});

// Payment Routes
Route::middleware(['auth'])->prefix('payment')->name('payment.')->group(function () {
    Route::post('/process', [PaymentController::class, 'process'])->name('process');
    Route::get('/success/{booking}', [PaymentController::class, 'success'])->name('success');
    Route::get('/cancel/{booking}', [PaymentController::class, 'cancel'])->name('cancel');
    Route::get('/failed/{booking}', [PaymentController::class, 'failed'])->name('failed');
});

// Payment Webhooks (no auth needed)
Route::post('/webhooks/payment/stripe', [PaymentController::class, 'stripeWebhook'])->name('webhooks.stripe');
Route::post('/webhooks/payment/paypal', [GeneralAdmissionController::class, 'paypalWebhook'])->name('webhooks.paypal');

// ==================== ADMIN BOOKING ROUTES ====================

// Add to your existing admin middleware group
Route::group(['middleware' => 'auth'], function () {



// Booking Management
    Route::get('/admin/bookings', [AdminBookingController::class, 'index'])->name('admin.bookings.index');
    Route::get('/admin/bookings/{booking}', [AdminBookingController::class, 'show'])->name('admin.bookings.show');
    Route::patch('/admin/bookings/{booking}/status', [AdminBookingController::class, 'updateStatus'])->name('admin.bookings.update-status');
    Route::post('/admin/bookings/{booking}/refund', [AdminBookingController::class, 'refund'])->name('admin.bookings.refund');
    Route::post('/admin/bookings/{booking}/resend-confirmation', [AdminBookingController::class, 'resendConfirmation'])->name('admin.bookings.resend-confirmation');

    // Booking Exports
    Route::get('/admin/bookings/export/csv', [AdminBookingController::class, 'exportCsv'])->name('admin.bookings.export.csv');
    Route::get('/admin/bookings/export/excel', [AdminBookingController::class, 'exportExcel'])->name('admin.bookings.export.excel');
    Route::get('/admin/shows/{show}/bookings/export', [AdminBookingController::class, 'exportShowBookings'])->name('admin.show-bookings.export');

    // Reports
    Route::get('/admin/reports/sales', [AdminBookingController::class, 'salesReport'])->name('admin.reports.sales');
    Route::get('/admin/reports/attendance', [AdminBookingController::class, 'attendanceReport'])->name('admin.reports.attendance');
    Route::get('/admin/reports/revenue', [AdminBookingController::class, 'revenueReport'])->name('admin.reports.revenue');

    // Ticket Scanning & Validation
    Route::get('/admin/scan', [AdminBookingController::class, 'scanTicket'])->name('admin.scan');
    Route::post('/admin/scan/validate', [AdminBookingController::class, 'validateTicket'])->name('admin.scan.validate');
    Route::get('/admin/shows/{show}/scan', [AdminBookingController::class, 'showScanPage'])->name('admin.show.scan');

    // Seat Map Management for Shows
    Route::get('/admin/shows/{show}/seat-map', [AdminBookingController::class, 'showSeatMap'])->name('admin.show.seat-map');
    Route::get('/admin/shows/{show}/reservations', [AdminBookingController::class, 'showReservations'])->name('admin.show.reservations');
    Route::post('/admin/reservations/{reservation}/release', [AdminBookingController::class, 'releaseReservation'])->name('admin.reservations.release');

    // Bulk Operations
    Route::post('/admin/bookings/bulk-cancel', [AdminBookingController::class, 'bulkCancel'])->name('admin.bookings.bulk-cancel');
    Route::post('/admin/bookings/bulk-confirm', [AdminBookingController::class, 'bulkConfirm'])->name('admin.bookings.bulk-confirm');
    Route::post('/admin/reservations/cleanup-expired', [AdminBookingController::class, 'cleanupExpiredReservations'])->name('admin.reservations.cleanup');
});

// Maintenance Routes
Route::middleware(['auth', 'admin'])->prefix('admin/maintenance')->name('admin.maintenance.')->group(function () {
    Route::post('/cleanup-expired-reservations', [AdminBookingController::class, 'cleanupExpiredReservations'])->name('cleanup-reservations');
    Route::post('/cleanup-expired-bookings', [AdminBookingController::class, 'cleanupExpiredBookings'])->name('cleanup-bookings');
    Route::post('/update-show-statuses', [AdminBookingController::class, 'updateShowStatuses'])->name('update-show-statuses');
});

// routes/web.php
use App\Http\Controllers\BookingPageController;

// Use slug for booking
Route::get('/shows/{show:slug}/book', [BookingPageController::class, 'showSeatSelection'])
    ->name('booking.seat-selection');

// API routes with slug
// Route::prefix('api')->group(function () {
//     Route::get('/shows/{show:slug}/seating', [BookingController::class, 'getAvailableSeating']);
//     Route::post('/shows/{show:slug}/hold-tickets', [BookingController::class, 'holdTickets']);
// });


use App\Http\Controllers\GalleryController;

// Gallery routes
Route::get('/galleries', [GalleryController::class, 'index'])->name('galleries.index');
Route::get('/gallery/{galleryId}/photos', [GalleryController::class, 'getGalleryPhotos'])->name('gallery.photos');


// Add these booking routes (make sure they're not inside any other route groups)
// Route::prefix('booking')->group(function () {
//     Route::get('/{slug}/tickets', [BookingController::class, 'showTicketSelection'])->name('booking.tickets');
//     Route::post('/{slug}/select-tickets', [BookingController::class, 'selectTickets'])->name('booking.select-tickets');
// });

// Test route to verify show exists
Route::get('/test-show/{slug}', function($slug) {
    $show = App\Models\Show::where('slug', $slug)->with('ticketTypes')->first();

    if (!$show) {
        return "Show not found with slug: $slug";
    }

    return response()->json([
        'show_found' => true,
        'title' => $show->title,
        'slug' => $show->slug,
        'ticket_types' => $show->ticketTypes->map(function($type) {
            return [
                'id' => $type->id,
                'name' => $type->name,
                'price' => $type->price,
                'capacity' => $type->capacity,
                'is_active' => $type->is_active
            ];
        })
    ]);
});



use App\Http\Controllers\GeneralAdmissionController;

// General admission booking routes
Route::prefix('ga-booking')->group(function () {
    Route::get('/{slug}/tickets', [GeneralAdmissionController::class, 'showTicketSelection'])->name('ga-booking.tickets');
    Route::post('/{slug}/select-tickets', [GeneralAdmissionController::class, 'selectTickets'])->name('ga-booking.select-tickets');
});

// Test route
Route::get('/test-show/{slug}', function($slug) {
    $show = App\Models\Show::where('slug', $slug)->with('ticketTypes')->first();

    if (!$show) {
        return "Show not found with slug: $slug";
    }

    return response()->json([
        'show_found' => true,
        'title' => $show->title,
        'slug' => $show->slug,
        'ticket_types' => $show->ticketTypes->map(function($type) {
            return [
                'id' => $type->id,
                'name' => $type->name,
                'price' => $type->price,
                'capacity' => $type->capacity,
                'is_active' => $type->is_active
            ];
        })
    ]);
});


Route::prefix('ga-booking')->group(function () {

    // PayPal return routes
    // Route::get('/{slug}/paypal-success', [GeneralAdmissionController::class, 'paypalSuccess'])
    //     ->name('ga-booking.paypal-success');
    // Route::get('/{slug}/paypal-cancel', [GeneralAdmissionController::class, 'paypalCancel'])
    //     ->name('ga-booking.paypal-cancel');
    // PayPal return routes - these MUST match what you send to PayPal
    Route::get('/{slug}/paypal/success', [GeneralAdmissionController::class, 'paypalSuccess'])
        ->name('ga-booking.paypal-success');

    Route::get('/{slug}/paypal/cancel', [GeneralAdmissionController::class, 'paypalCancel'])
        ->name('ga-booking.paypal-cancel');
    Route::get('/{slug}/tickets', [GeneralAdmissionController::class, 'showTicketSelection'])->name('ga-booking.tickets');
    Route::post('/{slug}/select-tickets', [GeneralAdmissionController::class, 'selectTickets'])->name('ga-booking.select-tickets');

    // Add these new routes:
    Route::get('/{slug}/customer-details', [GeneralAdmissionController::class, 'showCustomerDetails'])->name('ga-booking.customer-details');
    Route::post('/{slug}/customer-details', [GeneralAdmissionController::class, 'processCustomerDetails'])->name('ga-booking.process-customer-details');

        // ADD THESE MISSING ROUTES:
    Route::middleware('auth')->group(function() {
    // Route::get('/{slug}/payment', [GeneralAdmissionController::class, 'showPayment'])
    //     ->name('ga-booking.payment');
    // Route::post('/{slug}/payment', [GeneralAdmissionController::class, 'processPayment'])->name('ga-booking.process-payment');

    // SUCCESS/FAILURE ROUTES

    Route::get('/{slug}/booking-success/{bookingNumber}', [GeneralAdmissionController::class, 'bookingSuccess'])
        ->name('ga-booking.success');
    Route::get('/{slug}/booking-failed', [GeneralAdmissionController::class, 'bookingFailed'])
        ->name('ga-booking.failed');


     // NEW PAYMENT ROUTES
    Route::get('/{slug}/payment', [GeneralAdmissionController::class, 'showPayment'])
        ->name('ga-booking.payment');
    Route::post('/{slug}/payment', [GeneralAdmissionController::class, 'processPayment'])
        ->name('ga-booking.process-payment');

    // NEW SUCCESS/FAILURE ROUTES
    Route::get('/{slug}/booking-success/{bookingNumber}', [GeneralAdmissionController::class, 'bookingSuccess'])
        ->name('ga-booking.success');
    Route::get('/{slug}/booking-failed', [GeneralAdmissionController::class, 'bookingFailed'])
        ->name('ga-booking.failed');

    // PayPal return route
    Route::get('/{slug}/paypal-return', [GeneralAdmissionController::class, 'paypalReturn'])
        ->name('ga-booking.paypal-return');
     });
});

// Add these routes INSIDE your existing Route::group(['middleware' => 'auth'], function () { block in web.php
// Find your existing auth middleware group and add these routes:

Route::group(['middleware' => 'auth'], function () {

    // ==================== EXISTING ROUTES ====================
    // Your existing admin dashboard, show category, venue, etc. routes...

    // ==================== TICKET TYPES MANAGEMENT ====================

    // Main ticket types routes
    Route::get('/admin/ticket-types', [TicketTypeController::class, 'all'])
        ->name('admin.ticket-types.all');

    Route::get('/admin/ticket-types/create', [TicketTypeController::class, 'create'])
        ->name('admin.ticket-types.create');

    Route::post('/admin/ticket-types', [TicketTypeController::class, 'store'])
        ->name('admin.ticket-types.store');

    // AJAX route for show search
    Route::get('/admin/ticket-types/search-shows', [TicketTypeController::class, 'searchShows'])
        ->name('admin.ticket-types.search-shows');

    // Edit and update routes
    Route::get('/admin/ticket-types/{ticketType}/edit', [TicketTypeController::class, 'edit'])
        ->name('admin.ticket-types.edit');

    Route::put('/admin/ticket-types/{ticketType}', [TicketTypeController::class, 'update'])
        ->name('admin.ticket-types.update');

    Route::delete('/admin/ticket-types/{ticketType}', [TicketTypeController::class, 'destroy'])
        ->name('admin.ticket-types.delete');

    // Show-specific routes (for backward compatibility)
    Route::get('/admin/shows/{show}/ticket-types', [TicketTypeController::class, 'index'])
        ->name('admin.ticket-types.index');

    Route::get('/admin/shows/{show}/ticket-types/create', [TicketTypeController::class, 'createForShow'])
        ->name('admin.ticket-types.create-for-show');

    Route::post('/admin/shows/{show}/ticket-types', [TicketTypeController::class, 'storeForShow'])
        ->name('admin.ticket-types.store-for-show');

    // ==================== OTHER EXISTING ROUTES ====================
    // Your other existing routes...

});


// Route::get('/test-paypal', function() {
//     try {
//         // Check environment variables directly
//         $envChecks = [
//             'PAYPAL_MODE' => env('PAYPAL_MODE'),
//             'PAYPAL_SANDBOX_CLIENT_ID' => env('PAYPAL_SANDBOX_CLIENT_ID') ? 'SET' : 'NOT SET',
//             'PAYPAL_SANDBOX_CLIENT_SECRET' => env('PAYPAL_SANDBOX_CLIENT_SECRET') ? 'SET' : 'NOT SET',
//         ];

//         // Check config values
//         $config = config('paypal');
//         $mode = $config['mode'];

//         $configChecks = [
//             'mode' => $mode,
//             'client_id' => $config[$mode]['client_id'] ?? 'NOT SET',
//             'client_secret' => $config[$mode]['client_secret'] ? 'SET' : 'NOT SET',
//             'api_url' => $config[$mode]['api_url'] ?? 'NOT SET',
//         ];

//         // Try to create PayPal service
//         $paypal = new \App\Services\PayPalService();

//         // Try to get access token
//         $token = $paypal->getAccessToken();

//         return response()->json([
//             'success' => true,
//             'env_checks' => $envChecks,
//             'config_checks' => $configChecks,
//             'token_received' => !empty($token),
//             'token_length' => strlen($token),
//         ]);

//     } catch (\Exception $e) {
//         return response()->json([
//             'success' => false,
//             'error' => $e->getMessage(),
//             'env_checks' => $envChecks ?? [],
//             'config_checks' => $configChecks ?? [],
//         ]);
//     }
// });


Route::get('/test-paypal', function() {
    try {
        // Check environment variables directly
        $envChecks = [
            'PAYPAL_MODE' => env('PAYPAL_MODE'),
            'PAYPAL_SANDBOX_CLIENT_ID' => env('PAYPAL_SANDBOX_CLIENT_ID') ? 'SET' : 'NOT SET',
            'PAYPAL_SANDBOX_CLIENT_SECRET' => env('PAYPAL_SANDBOX_CLIENT_SECRET') ? 'SET' : 'NOT SET',
        ];

        // Check config values
        $config = config('paypal');
        $mode = $config['mode'];

        $configChecks = [
            'mode' => $mode,
            'client_id' => $config[$mode]['client_id'] ?? 'NOT SET',
            'client_secret' => $config[$mode]['client_secret'] ? 'SET' : 'NOT SET',
            'api_url' => $config[$mode]['api_url'] ?? 'NOT SET',
        ];

        // Try to create PayPal service
        $paypal = new \App\Services\PayPalService();

        // Try to get access token
        $token = $paypal->getAccessToken();

        // Test creating an order
        $order = $paypal->createOrder(10.00, 'Test Ticket Purchase');

        return response()->json([
            'success' => true,
            'env_checks' => $envChecks,
            'config_checks' => $configChecks,
            'token_received' => !empty($token),
            'token_length' => strlen($token),
            'order_created' => $order['status'] === 'CREATED',
            'order_id' => $order['id'],
            'approval_url' => collect($order['links'])->firstWhere('rel', 'approve')['href'] ?? null,
            'order_details' => $order
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage(),
            'env_checks' => $envChecks ?? [],
            'config_checks' => $configChecks ?? [],
        ]);
    }
})->middleware('auth');

// Update your existing webhook route to use the new controller
Route::post('/webhooks/payment/paypal', [GeneralAdmissionController::class, 'paypalWebhook'])->name('webhooks.paypal');
