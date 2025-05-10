<?php

use App\Http\Controllers\CustomerController;
use App\Http\Controllers\RestaurantController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RiderController;

Route::get('/status', function () {
    return response()->json(['status' => 'API is working']);
});

Route::post('/riders/register', [RiderController::class, 'register']);
Route::post('/riders/login', [RiderController::class, 'riderlogin']);
Route::post('/riders/update-location', [RiderController::class, 'updateLocation']);
Route::post('/riders/update-status', [RiderController::class, 'updateStatus']);
Route::get('/riders/get-status', [RiderController::class, 'getStatus']);
Route::get('/pending-delivery', [RiderController::class, 'getPendingOrders']);


//Customer
Route::post('/customers/register', [CustomerController::class, 'register']);

//Restaurant
Route::post('/restaurants/register', [RestaurantController::class, 'register']);
Route::post('/restaurants/login', [RestaurantController::class, 'login']);
