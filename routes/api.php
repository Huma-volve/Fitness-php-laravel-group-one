<?php

use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Auth\OtpController;
use App\Http\Controllers\Api\HomeController;
use App\Http\Controllers\Api\LandingController;
use App\Http\Controllers\Api\NewsletterController;
use App\Http\Controllers\Api\ReviewController;
use App\Http\Controllers\Api\TrainerController;
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


    Route::get('/trainers',          [HomeController::class, 'trainers']);
    Route::get('/trainers/{trainer}', [HomeController::class, 'showTrainer']);
});

Route::prefix('landing')->group(function () {
    Route::get('/stats',    [LandingController::class, 'stats']);
    Route::get('/trainers', [LandingController::class, 'trainers']);
    Route::get('/packages', [LandingController::class, 'packages']);
    Route::get('/reviews', [ReviewController::class, 'index']);
    Route::get('/trainers/{trainerId}/reviews', [ReviewController::class, 'trainerReviews']);
});

Route::middleware('auth:sanctum')->prefix('landing')->group(function () {
    Route::post('/newsletter', [NewsletterController::class, 'subscribe']);
    Route::post('/reviews', [ReviewController::class, 'store']);
});
Route::get('/auth/google/redirect', [AuthController::class, 'googleRedirect']);
Route::get('/auth/google/callback', [AuthController::class, 'googleCallback']);
