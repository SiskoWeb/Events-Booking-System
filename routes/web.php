<?php

use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\CategoriesController;
use App\Http\Controllers\Admin\UsersController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Organizer\EventController;
use App\Http\Controllers\Organizer\OrganizerDashboardController;
use App\Http\Controllers\Organizer\ReservationController;
use App\Http\Controllers\PDFController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\User\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});





//routes for admin
Route::middleware(['auth', 'verified', 'checkrole:admin'])->prefix('/admin')->group(function () {

    Route::resource('/', AdminDashboardController::class)->except(['show']);
    Route::resource('/categories', CategoriesController::class)->except(['show']);
    Route::resource('/users', UsersController::class)->except(['show']);
    Route::patch('/events/{event}', [EventController::class, 'accept'])->name('events.accept');
});

//routes for  oranied
Route::middleware(['auth', 'verified', 'checkrole:organizer'])->prefix('/organizer')->group(function () {

    Route::resource('/', OrganizerDashboardController::class)->except(['show']);

    Route::resource('/events', EventController::class)->except(['show']);
    Route::resource('/reservations', ReservationController::class)->except(['show']);
    Route::patch('/reservations/{reservation}', [ReservationController::class, 'confirmed'])->name('reservations.confirmed');
    Route::patch('/reservations/{reservation}', [ReservationController::class, 'canceled'])->name('reservations.canceled');
});



//routes for users
Route::middleware(['auth', 'verified', 'checkrole:user'])->prefix('/user')->group(function () {
    Route::get('/', [UserController::class, 'index'])->name('user.index');
    Route::get('/eventDetails', [ReservationController::class, 'store'])->name('user.booking');

    Route::post('/session/{event}', [ReservationController::class, 'session'])->name('user.session');
    Route::get('/checkout', function () {
        $reservationData = session('reservation_data');
        dd($reservationData);
        return 'n';
    })->name('checkout');



    //thi is when scan qrcode display details of tickets

    //thi is for generate pdf
    Route::get('ticket-pdf/{reservation}', [UserController::class, 'generatePdf'])->name('downloadTicket');
});


Route::get('ticketPreview/{reservation}', [UserController::class, 'ticketPreview'])->name('ticketPreview');


// Routes for guests
Route::prefix('/')->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('home.index');
    Route::get('/eventDetails/{event}', [HomeController::class, 'eventDetails'])->name('home.eventDetails');

    Route::middleware('guest')->group(function () {
        Route::get('registerUser', [HomeController::class, 'registerUser']);
    });
});




Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

//this route for not authrized users
Route::get('not_authorized', fn () => view('not_authorized'))->name('not_authorized');

Route::fallback(fn () => 'error  4040');

require __DIR__ . '/auth.php';
