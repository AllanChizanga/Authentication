<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CheckAuthController;

 Route::post('/register/initiate', [AuthController::class, 'initiateRegistration']);

Route::prefix('auth')->group(function () {
    // Registration flow
    Route::post('/register/initiate', [AuthController::class, 'initiateRegistration']);
    Route::post('/register/verify-otp', [AuthController::class, 'verifyRegistrationOtp']);
    Route::post('/register/complete', [AuthController::class, 'completeRegistration']);

    
    // Login flow
    Route::post('/login/initiate', [AuthController::class, 'initiateLogin']);
    Route::post('/login/verify-otp', [AuthController::class, 'verifyLoginOtp']);
    Route::post('/login/complete', [AuthController::class, 'completeLogin']);

    //Check Authentication
    Route::post('/verify', [CheckAuthController::class, 'checkAuth']);
    
    // Protected routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/user', [AuthController::class, 'user']);
    });
});