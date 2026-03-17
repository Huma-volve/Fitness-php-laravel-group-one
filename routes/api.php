<?php

use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Auth\OtpController;
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

Route::get('/auth/google/redirect', [AuthController::class, 'googleRedirect']);
Route::get('/auth/google/callback', [AuthController::class, 'googleCallback']);
