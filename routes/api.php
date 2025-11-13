<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

 Route::post('/login', function () {
    return response()->json(['message' => 'Login endpoint disabled'], 200);
})->name('login');


Route::prefix('auth')->group(function () {
    // Registration flow
    Route::post('/register/initiate', [AuthController::class, 'initiateRegistration']);
    Route::post('/register/verify-otp', [AuthController::class, 'verifyRegistrationOtp']);
    Route::post('/register/complete', [AuthController::class, 'completeRegistration']);

    // Login flow
    Route::post('/login/initiate', [AuthController::class, 'initiateLogin']);
    Route::post('/login/verify-otp', [AuthController::class, 'verifyLoginOtp']);
    Route::post('/login/complete', [AuthController::class, 'completeLogin']);
    });

    /**
     * Protected routes - require authentication for access to all users
     */
    Route::prefix('user')->middleware('auth:sanctum')->group(function () {

        Route::post('/logout', [AuthController::class, 'logout']);

        Route::get('/user', [AuthController::class, 'user']);

        Route::post('/verify-token', [AuthController::class, 'checkAuth']);
   });