<?php

use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\PackageController;
use App\Http\Controllers\Api\TrainerAvailabilityController;
use App\Http\Controllers\Api\TrainerSessionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// public routes
Route::get('packages',                        [PackageController::class, 'index']);
Route::get('packages/{package}',              [PackageController::class, 'show']);
Route::get('packages/{package}/trainers',     [PackageController::class, 'trainers']);

Route::get('trainers/{trainer}/availability', [TrainerAvailabilityController::class, 'slots']);
Route::get('trainers/{trainer}/schedule',     [TrainerAvailabilityController::class, 'schedule']);

    Route::get   ('bookings',                      [BookingController::class, 'index']);
    Route::post  ('bookings',                      [BookingController::class, 'store']);
    Route::get   ('bookings/{booking}',            [BookingController::class, 'show']);
    Route::put   ('bookings/{booking}/reschedule', [BookingController::class, 'reschedule']);
    Route::delete('bookings/{booking}/cancel',     [BookingController::class, 'cancel']);

//    Route::get('trainer/sessions',           [TrainerSessionController::class, 'index']);
//    Route::get('trainer/sessions/{session}', [TrainerSessionController::class, 'show']);
//    Route::get('trainer/bookings',           [TrainerSessionController::class, 'bookings']);

