<?php

use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Auth\OtpController;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\LandingController;
use App\Http\Controllers\Api\NewsletterController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\ReviewController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');




Route::post('/register', [AuthController::class, 'register']);
Route::post('/login',    [AuthController::class, 'login']);


Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout',         [AuthController::class, 'logout']);
    Route::delete('/delete-account', [AuthController::class, 'deleteAccount']);
    Route::get('/profile',         [AuthController::class, 'profile']);
    Route::post('/forgot-password', [OtpController::class, 'sendOtp']);
    Route::post('/verify-otp',      [OtpController::class, 'verifyOtp']);
    Route::post('/reset-password',  [OtpController::class, 'resetPassword']);
});

Route::prefix('landing')->group(function () {
    Route::get('/stats',    [LandingController::class, 'stats']);
    Route::get('/trainers', [LandingController::class, 'trainers']);
    Route::get('/packages', [LandingController::class, 'packages']);
    Route::get('/reviews', [ReviewController::class, 'index']);
    Route::get('/trainers/{trainerId}/reviews', [ReviewController::class, 'trainerReviews']);
    Route::post('/contact', [ContactController::class, 'store']);
});

Route::middleware('auth:sanctum')->prefix('landing')->group(function () {
    Route::post('/newsletter', [NewsletterController::class, 'subscribe']);
    Route::post('/reviews', [ReviewController::class, 'store']);

    Route::get('/profile', [ProfileController::class, 'profile']);
    Route::put('/profile', [ProfileController::class, 'update']);
    Route::post('/uploadImage', [ProfileController::class, 'uploadImage']);
    Route::delete('/removeImage', [ProfileController::class, 'removeImage']);
    Route::get('/sessions', [ProfileController::class, 'upcomingSessions']);
    Route::get('/packages', [ProfileController::class, 'currentPackages']);

    // Progress & Activity
    Route::get('/progress-activity', [ProfileController::class, 'progressActivity']);

    // Workout History
    Route::get('/workout-history', [ProfileController::class, 'workoutHistory']);

    // Payment Methods
    Route::get('/payment-methods', [ProfileController::class, 'paymentMethods']);

    // Add Payment Card
    Route::post('/payment-methods', [ProfileController::class, 'addPaymentMethod']);
});
Route::get('/auth/google/redirect', [AuthController::class, 'googleRedirect']);
Route::get('/auth/google/callback', [AuthController::class, 'googleCallback']);
