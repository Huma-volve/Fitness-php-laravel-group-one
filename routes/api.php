<?php

use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Auth\OtpController;
use App\Http\Controllers\Api\HomeController;
use App\Http\Controllers\Api\LandingController;
use App\Http\Controllers\Api\NewsletterController;
use App\Http\Controllers\Api\ReviewController;
use App\Http\Controllers\ChatMessageController;
use App\Models\ChatMessage;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\SearchController;
use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\PackageController;
use App\Http\Controllers\Api\TrainerAvailabilityController;
use App\Http\Controllers\Api\TrainerSessionController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\ProfileController;


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// public routes
Route::get('packages',                        [PackageController::class, 'index']);
Route::get('packages/{package}',              [PackageController::class, 'show']);
Route::get('packages/{package}/trainers',     [PackageController::class, 'trainers']);

Route::get('trainers/{trainer}/availability', [TrainerAvailabilityController::class, 'slots']);
Route::get('trainers/{trainer}/schedule',     [TrainerAvailabilityController::class, 'schedule']);

Route::middleware('auth:sanctum')->group(function () {

    Route::middleware('role:trainee')->group(function () {

        Route::get('bookings',                        [BookingController::class, 'index']);
        Route::get('bookings/{booking}',              [BookingController::class, 'show']);

        Route::post('bookings/schedule',               [BookingController::class, 'schedule']);

        Route::post('bookings/{booking}/pay',          [BookingController::class, 'pay']);
        Route::post('bookings/{booking}/confirm',      [BookingController::class, 'confirm']);

        Route::put('bookings/{booking}/reschedule',   [BookingController::class, 'reschedule']);
        Route::delete('bookings/{booking}/cancel',     [BookingController::class, 'cancel']);
    });

    Route::middleware('role:trainer')->group(function () {
        Route::get('trainer/sessions',           [TrainerSessionController::class, 'index']);
        Route::get('trainer/sessions/{session}', [TrainerSessionController::class, 'show']);
        Route::get('trainer/bookings',           [TrainerSessionController::class, 'bookings']);
    });
});

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


    Route::get('/trainers',          [HomeController::class, 'index']);
    Route::get('/trainers/{trainer}', [HomeController::class, 'showTrainer']);
});

Route::prefix('landing')->group(function () {
    Route::get('/stats',    [LandingController::class, 'stats']);
    Route::get('/trainers', [LandingController::class, 'trainers']);
    Route::get('/packages', [LandingController::class, 'packages']);
    Route::post('/contact', [ContactController::class, 'store']);
});

Route::middleware('auth:sanctum')->prefix('landing')->group(function () {
    Route::post('/newsletter', [NewsletterController::class, 'subscribe']);
    Route::post('/reviews', [ReviewController::class, 'store']);
    Route::get('/reviews', [ReviewController::class, 'index']);
    Route::get('/trainers/reviews', [ReviewController::class, 'trainerReviews']);
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









//////////////////// chat ///////////////////////
Route::middleware('auth:sanctum')->group(function () {

    Route::post('/conversations', [ChatMessageController::class, 'startConversation']);
    Route::get('/conversations', [ChatMessageController::class, 'getConversations']);
    Route::get('/conversations/{id}/messages', [ChatMessageController::class, 'getMessages']);
    Route::post('/conversations/{id}/messages', [ChatMessageController::class, 'sendMessage']);
    Route::patch('/conversations/{id}/read', [ChatMessageController::class, 'markAsRead']);
});







Route::get('/search', [SearchController::class, 'search']);
Route::get('/search/searchFilter', [SearchController::class, 'searchFilter']);
