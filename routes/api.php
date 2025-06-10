<?php

use login;
use Illuminate\Http\Request;

// import controller ProductController
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProductController;

// route login
Route::post('login', [AuthController::class, 'login']);
    

// products routes
Route::apiResource('products', ProductController::class);


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

