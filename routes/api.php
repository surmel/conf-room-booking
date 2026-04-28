<?php

declare(strict_types=1);

use App\Http\Controllers\BookingController;
use Illuminate\Support\Facades\Route;

Route::prefix('bookings')->middleware('auth:sanctum')->group(function (): void {
    Route::post('/',        [BookingController::class, 'store']);
    Route::get('/my',       [BookingController::class, 'byUser']);
    Route::get('/by-room',  [BookingController::class, 'byRoom']);
});
