<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;

class CheckAuthController extends Controller
{

    protected $auth_service;
    public function __construct(AuthService $authService) {
        $this->auth_service = $authService;
    }

    /**
     * Simple authentication check that returns only true/false
     */
    public function checkAuth(Request $request): JsonResponse
    {
        // Get token from Authorization header or request body
        $token = $request->bearerToken() ?? $request->input('token');

        // If no token provided, return false
        if (!$token) {
            return response()->json("falseeee");
        }

        // Validate token and return boolean result
        $isAuthenticated = $this->auth_service->validateToken($token);

        return response()->json($isAuthenticated);
    }

}
