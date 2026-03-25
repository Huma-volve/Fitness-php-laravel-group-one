<?php

use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Web\SearchAdminController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('master');
});
Route::get('payment/success' , [BookingController::class, 'success']);

Route::get('/search' , [SearchAdminController::class, 'index'])->name('search');
Route::get('/search/search_text' , [SearchAdminController::class, 'searchText']);
Route::get('/search/search_text/user' , [SearchAdminController::class, 'searchText']);
