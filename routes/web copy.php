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



// Route::view('/welcome', 'welcome');
// Route::view('/seat-selection', 'seat-selection');
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

// Client-side Gallery Routes (Public)
Route::get('/photo-galleries', [PhotoGalleryController::class, 'clientIndex'])->name('photo-galleries');
Route::get('/video-galleries', [VideoGalleryController::class, 'clientIndex'])->name('video-galleries');

// Base Controller's routes
Route::get('user/login', [BaseController::class, 'loginCheck'])->name('user_login');
Route::post('user/login', [BaseController::class, 'loginCheck'])->name('logincheck');
Route::post('user/register', [BaseController::Class, 'user_store'])->name('user_store');
Route::get('user/logout', [BaseController::Class, 'logout'])->name('user_logout');

// AdminController's routes
Route::get('/admin/login', [AdminController::Class, 'login'])->name('admin.login');

Route::post('/admin/login', [AdminController::Class, 'makeLogin'])->name('admin.makeLogin');
Route::get('/login', function () {
    return redirect()->route('admin.login');
})->name('login');

Route::group(['middleware' => 'auth'], function () {

    // ProductController routes
    // Route::get('/orders', [OrderController::class, 'index'])->name('order.list');
    // Route::get('/directorders', [OrderController::class, 'directorders'])->name('directorder.list');

    Route::get('/admin/dashboard', [AdminController::Class, 'dashboard'])->name('admin.dashboard');
    // Route::get('/admin/dashboard', [BaseController::Class, 'logincheck'])->name('admin.dashboard');

    Route::get('/admin/logout', [AdminController::Class, 'logout'])->name('admin.logout');

    // CategoryController route
    Route::get('/showcategories', [CategoryController::Class, 'index'])->name('showcategory.list');

    Route::get('/showcategory/add', [CategoryController::Class, 'create'])->name('showcategory.create');

    Route::post('/showcategory/add', [CategoryController::Class, 'store'])->name('showcategory.store');

    Route::get('/showcategories/edit/{id}', [CategoryController::Class, 'edit'])->name('showcategory.edit');
    Route::put('/showcategories/update/{id}', [CategoryController::Class, 'update'])->name('showcategory.update');
    Route::post('/showcategory/delete/{id}', [CategoryController::Class, 'destroy'])->name('showcategory.delete');

    // ShowController routes
    Route::get('/shows', [ShowController::Class, 'index'])->name('show.index');

    Route::get('/show/add', [ShowController::Class, 'create'])->name('show.create');
    Route::post('/show/add', [ShowController::Class, 'store'])->name('show.store');

    Route::get('/show/edit/{id}', [ShowController::Class, 'edit'])->name('show.edit');
    Route::put('/show/update/{id}', [ShowController::Class, 'update'])->name('show.update');
    Route::post('/show/delete/{id}', [ShowController::Class, 'destroy'])->name('show.delete');
    // Show resource routes
    Route::get('/admin/show/{id}', [ShowController::class, 'show'])->name('shows.show');

    // VenueController routes
    Route::get('/venues', [VenueController::class, 'index'])->name('venues.index');

    Route::get('/venue/add', [VenueController::Class, 'create'])->name('venue.create');
    Route::post('/venue/add', [VenueController::Class, 'store'])->name('venue.store');

    Route::get('/venue/edit/{id}', [VenueController::Class, 'edit'])->name('venue.edit');
    Route::put('/venue/update/{id}', [VenueController::Class, 'update'])->name('venue.update');
    Route::post('/venue/delete/{id}', [VenueController::Class, 'destroy'])->name('venue.delete');


    // Route::get('/admin/users', [UserController::class, 'index'])->name('admin.users');
    // Route::post('/admin/delete', [UserController::class, 'delete'])->name('user.delete');

// PhotoGalleryController routes
Route::get('/photogallery/list', [PhotoGalleryController::class, 'index'])->name('photogallery.list');
    Route::get('/photogallery/create', [PhotoGalleryController::class, 'create'])->name('photogallery.create');
    Route::post('/photogallery/create', [PhotoGalleryController::class, 'store'])->name('photogallery.store');
    Route::get('/photogallery/edit/{id}', [PhotoGalleryController::class, 'edit'])->name('photogallery.edit');
    Route::post('/photogallery/edit/{id}', [PhotoGalleryController::class, 'update'])->name('photogallery.update');
    Route::get('/photogallery/{id}', [PhotoGalleryController::class, 'show'])->name('photogallery.show');
    Route::post('/photogallery/delete/{id}', [PhotoGalleryController::Class, 'destroy'])->name('photogallery.delete');
    // Route to update a specific discount
    // Route::put('/photogallery/edit/{id}', [PhotoGalleryController::class, 'update'])->name('photogallery.update');

    Route::get('/photosingallery/list', [PhotosinGalleryController::class, 'index'])->name('photosingallery.list');
    Route::get('/photosingallery/create', [PhotosinGalleryController::class, 'create'])->name('photosingallery.create');
    Route::post('/photosingallery/create', [PhotosinGalleryController::class, 'store'])->name('photosingallery.store');
    Route::get('/photosingallery/edit/{id}', [PhotosinGalleryController::class, 'edit'])->name('photosingallery.edit');
    Route::post('/photosingallery/edit/{id}', [PhotosinGalleryController::class, 'update'])->name('photosingallery.update');
    Route::get('/photosingallery/{id}', [PhotosinGalleryController::class, 'show'])->name('photosingallery.show');
    Route::post('/photosingallery/delete/{id}', [PhotosinGalleryController::class, 'destroy'])->name('photosingallery.delete');

    Route::get('/videosingallery/list', [VideosinGalleryController::class, 'index'])->name('videosingallery.list');
    Route::get('/videosingallery/create', [VideosinGalleryController::class, 'create'])->name('videosingallery.create');
    Route::post('/videosingallery/create', [VideosinGalleryController::class, 'store'])->name('videosingallery.store');
    Route::get('/videosingallery/edit/{id}', [VideosinGalleryController::class, 'edit'])->name('videosingallery.edit');
    Route::post('/videosingallery/edit/{id}', [VideosinGalleryController::class, 'update'])->name('videosingallery.update');
    Route::get('/videosingallery/{id}', [VideosinGalleryController::class, 'show'])->name('videosingallery.show');
    Route::post('/videosingallery/delete/{id}', [VideosinGalleryController::class, 'destroy'])->name('videosingallery.delete');

    // VideoGalleryController routes
    Route::get('/videogallery/list', [VideoGalleryController::class, 'index'])->name('videogallery.list');
    Route::get('/videogallery/create', [VideoGalleryController::class, 'create'])->name('videogallery.create');
    Route::post('/videogallery/create', [VideoGalleryController::class, 'store'])->name('videogallery.store');
    Route::get('/videogallery/edit/{id}', [VideoGalleryController::class, 'edit'])->name('videogallery.edit');
    Route::put('/videogallery/edit/{id}', [VideoGalleryController::class, 'update'])->name('videogallery.update');
    Route::get('/videogallery/{id}', [VideoGalleryController::class, 'show'])->name('videogallery.show');
    Route::post('/videogallery/delete/{id}', [VideoGalleryController::Class, 'destroy'])->name('videogallery.delete');
    // Route to update a specific video gallery
    // Route::put('/videogallery/edit/{id}', [VideoGalleryController::class, 'update'])->name('videogallery.update');



    // Route::get('/product/delete/{id}', [ProductController::class, 'productDelete'])->name('product.delete');
    // Route::post('/product/delete/{id}', [ProductController::class, 'productDelete'])->name('product.delete');

    // Route::get('/product/details/{id}', [ProductController::class, 'extraDetails'])->name('product.extraDetails');
    // Route::post('/product/details/{id}', [ProductController::class, 'extraDetailsStore'])->name('product.extraDetailsStore');

    // Route::get('/discounts', [DiscountController::class, 'index'])->name('discount.list');

    // Route to show the form for creating a new discount
    // Route::get('/discount/create', [DiscountController::class, 'create'])->name('discount.create');

    // Route to store a newly created discount
    // Route::post('/discount/create', [DiscountController::class, 'store'])->name('discount.store');

    // Route to show the form for editing a specific discount
    // Route::get('/discount/edit/{id}', [DiscountController::class, 'edit'])->name('discount.edit');

    // Route to update a specific discount
    // Route::put('/discount/edit/{id}', [DiscountController::class, 'update'])->name('discount.update');

    // Route to delete a specific discount
    // Route::get('/discount/delete/{id}', [DiscountController::class, 'discountDelete'])->name('discount.delete');
    // Route::post('/discount/delete/{id}', [DiscountController::class, 'discountDelete'])->name('discount.delete');


});

// Customer Routes
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

// Admin Routes - Commented out due to missing controllers
/*
Route::prefix('admin')->middleware(['auth', 'admin'])->name('admin.')->group(function () {
    // Seat Categories
    Route::resource('seat-categories', SeatCategoryController::class);

    // Seats
    Route::get('seats/bulk-create', [SeatController::class, 'bulkCreate'])->name('seats.bulk-create');
    Route::post('seats/bulk-store', [SeatController::class, 'bulkStore'])->name('seats.bulk-store');
    Route::get('seats/map/{venue}', [SeatController::class, 'mapView'])->name('seats.map');
    Route::post('seats/update-positions', [SeatController::class, 'updatePositions'])->name('seats.update-positions');
    Route::resource('seats', SeatController::class);

    // Reservations
    Route::get('reservations/show/{show}/available-seats', [SeatReservationController::class, 'getAvailableSeats'])->name('reservations.available-seats');
    Route::resource('reservations', SeatReservationController::class);
});
*/

// Frontend Routes - Commented out due to missing controllers
/*
Route::middleware(['web'])->group(function () {
    Route::get('shows/{show}/seat-selection', [SeatSelectionController::class, 'showSeatMap'])->name('shows.seat-selection');
    Route::post('shows/temporary-reserve', [SeatSelectionController::class, 'temporaryReserve'])->name('shows.temporary-reserve');
    Route::post('shows/confirm-reservation', [SeatSelectionController::class, 'confirmReservation'])->name('shows.confirm-reservation');
});
*/

use App\Http\Controllers\BookingController;
use App\Http\Controllers\OptimizedBookingController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\SeatMapController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\Admin\AdminBookingController;
use App\Http\Controllers\Admin\TicketTypeController;

// ==================== BOOKING SYSTEM ROUTES ====================

// Public Show Details (modify existing if needed)
Route::get('/shows/{slug}', [PageController::class, 'showDetails'])->name('show.details');




// Booking Routes (Authenticated Users) - OPTIMIZED VERSION WITH MONITORING
Route::middleware(['auth', 'booking.monitor'])->group(function () {

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

// Payment Routes
Route::middleware(['auth'])->prefix('payment')->name('payment.')->group(function () {
    Route::post('/process', [PaymentController::class, 'process'])->name('process');
    Route::get('/success/{booking}', [PaymentController::class, 'success'])->name('success');
    Route::get('/cancel/{booking}', [PaymentController::class, 'cancel'])->name('cancel');
    Route::get('/failed/{booking}', [PaymentController::class, 'failed'])->name('failed');
});

// Payment Webhooks (no auth needed)
Route::post('/webhooks/payment/stripe', [PaymentController::class, 'stripeWebhook'])->name('webhooks.stripe');
Route::post('/webhooks/payment/paypal', [PaymentController::class, 'paypalWebhook'])->name('webhooks.paypal');

// ==================== ADMIN BOOKING ROUTES ====================

// Add to your existing admin middleware group
Route::group(['middleware' => 'auth'], function () {

    // Ticket Types Management
    Route::get('/admin/shows/{show}/ticket-types', [TicketTypeController::class, 'index'])->name('admin.ticket-types.index');
    Route::get('/admin/shows/{show}/ticket-types/create', [TicketTypeController::class, 'create'])->name('admin.ticket-types.create');
    Route::post('/admin/shows/{show}/ticket-types', [TicketTypeController::class, 'store'])->name('admin.ticket-types.store');
    Route::get('/admin/ticket-types/{ticketType}/edit', [TicketTypeController::class, 'edit'])->name('admin.ticket-types.edit');
    Route::put('/admin/ticket-types/{ticketType}', [TicketTypeController::class, 'update'])->name('admin.ticket-types.update');
    Route::delete('/admin/ticket-types/{ticketType}', [TicketTypeController::class, 'destroy'])->name('admin.ticket-types.delete');

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

Route::middleware(['auth', 'admin'])->prefix('admin/maintenance')->name('admin.maintenance.')->group(function () {
    Route::post('/cleanup-expired-reservations', [AdminBookingController::class, 'cleanupExpiredReservations'])->name('cleanup-reservations');
    Route::post('/cleanup-expired-bookings', [AdminBookingController::class, 'cleanupExpiredBookings'])->name('cleanup-bookings');
    Route::post('/update-show-statuses', [AdminBookingController::class, 'updateShowStatuses'])->name('update-show-statuses');
});

// ==================== API ROUTES (Optional - for mobile/external) ====================
// Commented out due to missing API controllers
/*
Route::middleware(['auth:sanctum'])->prefix('api/v1')->name('api.')->group(function () {
    // Shows
    Route::get('/shows', [\App\Http\Controllers\Api\ShowController::class, 'index'])->name('');
    Route::get('/shows/{show}', [\App\Http\Controllers\Api\ShowController::class, 'show'])->name('shows.show');
    Route::get('/shows/{show}/availability', [\App\Http\Controllers\Api\ShowController::class, 'availability'])->name('shows.availability');

    // Bookings
    Route::get('/bookings', [\App\Http\Controllers\Api\BookingController::class, 'index'])->name('bookings.index');
    Route::post('/bookings', [\App\Http\Controllers\Api\BookingController::class, 'store'])->name('bookings.store');
    Route::get('/bookings/{booking}', [\App\Http\Controllers\Api\BookingController::class, 'show'])->name('bookings.show');
    Route::patch('/bookings/{booking}/cancel', [\App\Http\Controllers\Api\BookingController::class, 'cancel'])->name('bookings.cancel');

    // Tickets
    Route::get('/tickets/{ticket}', [\App\Http\Controllers\Api\TicketController::class, 'show'])->name('tickets.show');
    Route::post('/tickets/{ticket}/validate', [\App\Http\Controllers\Api\TicketController::class, 'validate'])->name('tickets.validate');
});
*/

// ==================== CRON/MAINTENANCE ROUTES ====================

// These should be protected by middleware or run via artisan commands

