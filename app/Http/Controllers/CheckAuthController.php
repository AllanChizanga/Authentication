<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;

class CheckAuthController extends Controller
{
    public function __construct(
        private AuthService $authService
    ) {}

    /**
     * Simple authentication check that returns only true/false
     */
    public function checkAuth(Request $request): JsonResponse
    {
        // Get token from Authorization header or request body
        $token = $request->bearerToken() ?? $request->input('token');

        // If no token provided, return false
        if (!$token) {
            return response()->json(false);
        }

        // Validate token and return boolean result
        $isAuthenticated = $this->authService->validateToken($token);

        return response()->json($isAuthenticated);
    }

}
