<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

/**
 * Authentication Routes
 * All routes are prefixed with /api/auth
 */
Route::prefix('auth')->group(function () {
    // Public routes (no authentication required)
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    
    // Protected routes (require Sanctum authentication)
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::post('/logout-all', [AuthController::class, 'logoutAllDevices']);
        Route::post('/refresh', [AuthController::class, 'refresh']);
        Route::get('/user', [AuthController::class, 'user']);
    });
});

//protected route for testing
Route::middleware('auth:sanctum')->get('/protected-route', function () {
    return response()->json([
        'message' => 'Access granted to protected route!',
        'user' => auth()->user()->only(['id', 'name', 'email'])
    ]);
});