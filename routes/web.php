<?php

use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\ReviewController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('master');
});

Route::get('payment/success', [BookingController::class, 'success']);

// Route::middleware('auth:sanctum')->prefix('landing')->group(function () {
Route::get('/reviews', [ReviewController::class, 'index'])->name('reviews.index');
Route::get('/reviews/{trainerId}', [ReviewController::class, 'trainerReviews'])->name('reviews.trainer');
Route::post('/reviews/{review}/reply', [ReviewController::class, 'reply'])->name('reviews.reply');
// });
