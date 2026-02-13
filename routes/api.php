<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;


/*
    |--------------------------------------------------------------------------
    | Public Routes
    |--------------------------------------------------------------------------
    */

Route::prefix('otp')->group(function () {

    Route::post('send', [AuthController::class, 'send'])
        ->middleware('throttle:5,1');

    Route::post('verify', [AuthController::class, 'verify'])
        ->middleware('throttle:10,1');

    Route::post('resend', [AuthController::class, 'resend'])
        ->middleware()->middleware(['auth:sanctum']);
});




/*
    |--------------------------------------------------------------------------
    | Protected Routes (Requires Authentication)
    |--------------------------------------------------------------------------
    */

Route::middleware(['auth:sanctum'])->group(function () {

    Route::post('logout', [AuthController::class, 'logout'])
        ->middleware(['auth:sanctum', 'throttle:3,1']);


    Route::prefix('tokens')->group(function () {

        Route::post('refresh', [AuthController::class, 'refresh'])
            ->middleware('throttle:10,1');

        Route::delete('', [AuthController::class, 'revokeAll'])
            ->middleware('throttle:5,1');

        Route::delete('{tokenId}', [AuthController::class, 'revoke'])
            ->middleware('throttle:20,1');
    });




    // User Management
    Route::prefix('profile')->group(function () {

        // Profile
        Route::get('', [UserController::class, 'show'])
            ->middleware('throttle:60,1');

        Route::put('', [UserController::class, 'update'])
            ->middleware('throttle:20,1');

        Route::patch('', [UserController::class, 'partialUpdate'])
            ->middleware('throttle:20,1');
    });
});
