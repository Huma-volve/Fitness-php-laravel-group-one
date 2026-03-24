<?php

use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Web\Admin\AdminBookingController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('master');
});
Route::get('payment/success' , [BookingController::class, 'success']);

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('bookings/stats',     [AdminBookingController::class, 'stats'])->name('bookings.stats');
    Route::get('bookings',           [AdminBookingController::class, 'index'])->name('bookings.index');
    Route::get('bookings/{booking}', [AdminBookingController::class, 'show'])->name('bookings.show');
});
