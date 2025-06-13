<?php

use App\Http\Controllers\Api\HotelBookingController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// API v1
Route::group(['prefix' => 'v1'], function () {
    Route::group(['prefix' => 'hotel'], function () {
        Route::post('booking', [HotelBookingController::class, 'bookingHotel'])->name('booking');
    })->name('hotel.');
});
