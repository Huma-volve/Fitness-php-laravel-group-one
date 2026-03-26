<?php

use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\trainerController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});
Route::get('/home', function () {
    return view('master'); 
})->name('home');
Route::get('payment/success' , [BookingController::class, 'success']);



///////////////////// get trainer ///////////
Route::get('get trainer',[trainerController::class,'index'])->name('gettrainer');
Route::get('showdetails/{id}',[trainerController::class,'showdetails'])->name('showdetails');
Route::get('/trainees/create', [trainerController::class, 'create'])->name('trainees.create');
Route::post('/trainees/store', [trainerController::class, 'store'])->name('trainees.store');
Route::delete('/trainees/deletetrainer/{id}', [trainerController::class, 'delete'])->name('deletetrainer');
Route::get('/trainers/edit/{id}', [trainerController::class, 'edit'])->name('trainers.edit');
Route::put('/trainers/update/{id}', [trainerController::class, 'update'])->name('trainers.update');
Route::get('login',[LoginController::class,'index'])->name('login');
Route::post('verifaylogin',[LoginController::class,'verifaylogin'])->name('verifaylogin');
