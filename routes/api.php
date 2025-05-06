<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RiderController;


Route::get('/status', function () {
    return response()->json(['status' => 'API is working']);
});

Route::post('/riders/register', [RiderController::class, 'register']);
Route::post('/riders/login', [RiderController::class, 'riderlogin']);
Route::post('/riders/update-location', [RiderController::class, 'updateLocation']);
Route::post('/riders/update-status', [RiderController::class, 'updateStatus']);