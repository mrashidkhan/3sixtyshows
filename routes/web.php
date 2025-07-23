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


    Route::get('/admin/users', [UserController::class, 'index'])->name('admin.users');
    Route::post('/admin/delete', [UserController::class, 'delete'])->name('user.delete');

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
    Route::get('/discount/create', [DiscountController::class, 'create'])->name('discount.create');

    // Route to store a newly created discount
    Route::post('/discount/create', [DiscountController::class, 'store'])->name('discount.store');

    // Route to show the form for editing a specific discount
    Route::get('/discount/edit/{id}', [DiscountController::class, 'edit'])->name('discount.edit');

    // Route to update a specific discount
    Route::put('/discount/edit/{id}', [DiscountController::class, 'update'])->name('discount.update');

    // Route to delete a specific discount


    Route::get('/discount/delete/{id}', [DiscountController::class, 'discountDelete'])->name('discount.delete');
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

// Admin Routes
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

// Frontend Routes
Route::middleware(['web'])->group(function () {
    Route::get('shows/{show}/seat-selection', [SeatSelectionController::class, 'showSeatMap'])->name('shows.seat-selection');
    Route::post('shows/temporary-reserve', [SeatSelectionController::class, 'temporaryReserve'])->name('shows.temporary-reserve');
    Route::post('shows/confirm-reservation', [SeatSelectionController::class, 'confirmReservation'])->name('shows.confirm-reservation');
});

// Add these routes to your routes/web.php file

// PhotosinGallery routes - nested under photo galleries
// Route::prefix('admin/gallery/{galleryId}/photos')->name('photos.gallery.')->middleware(['auth', 'admin'])->group(function () {
//     Route::get('/', [PhotosinGalleryController::class, 'index'])->name('index');
//     Route::get('/create', [PhotosinGalleryController::class, 'create'])->name('create');
//     Route::post('/', [PhotosinGalleryController::class, 'store'])->name('store');
//     Route::get('/{id}', [PhotosinGalleryController::class, 'show'])->name('show');
//     Route::get('/{id}/edit', [PhotosinGalleryController::class, 'edit'])->name('edit');
//     Route::put('/{id}', [PhotosinGalleryController::class, 'update'])->name('update');
//     Route::delete('/{id}', [PhotosinGalleryController::class, 'destroy'])->name('delete');

//     // Bulk upload routes
//     Route::get('/bulk-upload', [PhotosinGalleryController::class, 'bulkUpload'])->name('bulk-upload');
//     Route::post('/bulk-upload', [PhotosinGalleryController::class, 'processBulkUpload'])->name('process-bulk-upload');

//     // Order update route
//     Route::post('/update-order', [PhotosinGalleryController::class, 'updateOrder'])->name('update-order');
// });

