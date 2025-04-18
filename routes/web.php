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

    // VenueController routes
    Route::get('/venues', [VenueController::class, 'index'])->name('venues.index');

    Route::get('/venue/add', [VenueController::Class, 'create'])->name('venue.create');
    Route::post('/venue/add', [VenueController::Class, 'store'])->name('venue.store');

    Route::get('/venue/edit/{id}', [VenueController::Class, 'edit'])->name('venue.edit');
    Route::put('/venue/update/{id}', [VenueController::Class, 'update'])->name('venue.update');
    Route::post('/venue/delete/{id}', [VenueController::Class, 'destroy'])->name('venue.delete');


    Route::get('/admin/users', [UserController::class, 'index'])->name('admin.users');
    Route::post('/admin/delete', [UserController::class, 'delete'])->name('user.delete');

    // ProductController routes
    Route::get('/products', [ProductController::class, 'index'])->name('product.list');
    Route::get('/product/create', [ProductController::class, 'create'])->name('product.create');
    Route::post('/product/create', [ProductController::class, 'store'])->name('product.store');
    Route::get('/product/edit/{id}', [ProductController::class, 'edit'])->name('product.edit');
    Route::post('/product/edit/{id}', [ProductController::class, 'update'])->name('product.update');


    Route::get('/product/delete/{id}', [ProductController::class, 'productDelete'])->name('product.delete');
    // Route::post('/product/delete/{id}', [ProductController::class, 'productDelete'])->name('product.delete');

    Route::get('/product/details/{id}', [ProductController::class, 'extraDetails'])->name('product.extraDetails');
    Route::post('/product/details/{id}', [ProductController::class, 'extraDetailsStore'])->name('product.extraDetailsStore');

    Route::get('/discounts', [DiscountController::class, 'index'])->name('discount.list');

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
    Route::get('/customers', [App\Http\Controllers\Admin\CustomerController::class, 'index'])->name('customer.index');
    Route::get('/customers/create', [App\Http\Controllers\Admin\CustomerController::class, 'create'])->name('customer.create');
    Route::post('/customers', [App\Http\Controllers\Admin\CustomerController::class, 'store'])->name('customer.store');
    Route::get('/customers/{customer}', [App\Http\Controllers\Admin\CustomerController::class, 'show'])->name('customer.show');
    Route::get('/customers/{customer}/edit', [App\Http\Controllers\Admin\CustomerController::class, 'edit'])->name('customer.edit');
    Route::put('/customers/{customer}', [App\Http\Controllers\Admin\CustomerController::class, 'update'])->name('customer.update');
    Route::get('/customers/{customer}/delete', [App\Http\Controllers\Admin\CustomerController::class, 'delete'])->name('customer.delete');
    Route::delete('/customers/{customer}', [App\Http\Controllers\Admin\CustomerController::class, 'destroy'])->name('customer.destroy');
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
