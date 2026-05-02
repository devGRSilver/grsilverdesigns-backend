<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoriesController;
use App\Http\Controllers\Api\ContentController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::prefix('otp')->group(function () {
    Route::post('send', [AuthController::class, 'sendOtp'])->middleware('throttle:5,1');
    Route::post('verify', [AuthController::class, 'verifyOtp'])->middleware('throttle:10,1');
    Route::post('resend', [AuthController::class, 'resendOtp'])->middleware('throttle:5,1');
});



/*
|--------------------------------------------------------------------------
| Protected Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function () {

    Route::post('logout', [AuthController::class, 'logout'])->middleware('throttle:3,1');

    Route::post('tokens/refresh', [AuthController::class, 'refreshToken'])
        ->middleware('throttle:10,1');

    Route::get('profile', [UserController::class, 'getUserDetails'])
        ->middleware('throttle:60,1');

    Route::patch('profile', [UserController::class, 'updateProfile'])
        ->middleware('throttle:20,1');


    Route::get('categories', [CategoriesController::class, 'categories']);



    Route::get('contents', [ContentController::class, 'getContent']);
});
