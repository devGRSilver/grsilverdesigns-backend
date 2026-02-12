<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| OTP Routes - Public
|--------------------------------------------------------------------------
*/

Route::prefix('otp')->group(function () {
    Route::post('', [AuthController::class, 'sendOtp'])
        ->middleware('throttle:3,1');

    // Verify OTP - 5 attempts per minute
    Route::post('verify', [AuthController::class, 'verifyOtp'])
        ->middleware('throttle:5,1');

    // Resend OTP - 2 attempts per minute
    Route::post('resend', [AuthController::class, 'resendOtp'])
        ->middleware('throttle:2,1');
});

/*
|--------------------------------------------------------------------------
| Protected Auth Routes - Sanctum
|--------------------------------------------------------------------------
*/
Route::prefix('auth')->middleware('auth:sanctum')->group(function () {

    // Logout - 60 attempts per minute
    Route::post('logout', [AuthController::class, 'logout'])
        ->middleware('throttle:60,1');

    // Get user profile
    Route::get('profile', [AuthController::class, 'profile'])
        ->middleware('throttle:60,1');

    // Update user profile
    Route::patch('profile', [AuthController::class, 'updateProfile'])
        ->middleware('throttle:60,1');

    // Optional: Change password route
    Route::post('change-password', [AuthController::class, 'changePassword'])
        ->middleware('throttle:10,1');
});

/*
|--------------------------------------------------------------------------
| Product Routes
|--------------------------------------------------------------------------
*/
Route::prefix('products')->group(function () {

    Route::get('/', [ProductController::class, 'index'])
        ->middleware('throttle:60,1');

    Route::get('{slug}', [ProductController::class, 'show'])
        ->middleware('throttle:60,1');
});
