<?php

use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Web\SearchAdminController;
use App\Http\Controllers\Web\Admin\AdminBookingController;
use App\Http\Controllers\Web\Admin\BookingStateController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\trainerController;
use App\Http\Controllers\ReviewController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/home', function () {
    return view('master');
})->name('home');

Route::get('/admin/home',     [BookingStateController::class, 'stats'])->name('admin.index');

Route::get('payment/success', [BookingController::class, 'success']);

Route::get('/search', [SearchAdminController::class, 'index'])->name('search');
Route::get('/search/search_text', [SearchAdminController::class, 'searchText']);
Route::get('/search/search_text/userInfo/{id}', [SearchAdminController::class, 'searchInfo']);


///////////////////// get trainer ///////////
Route::get('get trainer', [trainerController::class, 'index'])->name('gettrainer');
Route::get('showdetails/{id}', [trainerController::class, 'showdetails'])->name('showdetails');
Route::get('/trainees/create', [trainerController::class, 'create'])->name('trainees.create');
Route::post('/trainees/store', [trainerController::class, 'store'])->name('trainees.store');
Route::delete('/trainees/deletetrainer/{id}', [trainerController::class, 'delete'])->name('deletetrainer');
Route::get('/trainers/edit/{id}', [trainerController::class, 'edit'])->name('trainers.edit');
Route::put('/trainers/update/{id}', [trainerController::class, 'update'])->name('trainers.update');
Route::get('login', [LoginController::class, 'index'])->name('login');
Route::post('verifaylogin', [LoginController::class, 'verifaylogin'])->name('verifaylogin');

Route::get('login', [LoginController::class, 'index'])->name('login');
Route::post('verifaylogin', [LoginController::class, 'verifaylogin'])->name('verifaylogin');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
Route::get('payment/success', [BookingController::class, 'success']);

Route::middleware('auth')->group(function () {
    Route::get('/reviews/all', [ReviewController::class, 'index'])->name('reviews.index');
    Route::get('/reviews', [ReviewController::class, 'trainerReviews'])->name('reviews.trainer');
    Route::post('/reviews/{review}/reply', [ReviewController::class, 'reply'])->name('reviews.reply');
});

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('bookings',           [AdminBookingController::class, 'index'])->name('bookings.index');
    Route::get('bookings/{booking}', [AdminBookingController::class, 'show'])->name('bookings.show');
});
