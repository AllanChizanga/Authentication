<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class CheckAuthController extends Controller
{
    public function __construct(
        private AuthService $authService
    ) {}

    public function checkAuth(Request $request): JsonResponse
    {
        // Get token from Authorization header or request body
        $token = $request->bearerToken() ?? $request->input('token');
        
        Log::debug('Auth Check - Received token', [
            'token_prefix' => $token ? substr($token, 0, 10) . '...' : 'NULL',
            'source' => $request->bearerToken() ? 'header' : ($request->input('token') ? 'body' : 'none')
        ]);

        // If no token provided, return false
        if (!$token) {
            Log::debug('Auth Check - No token provided');
            return response()->json(false);
        }

        // Validate token using Sanctum
        $isAuthenticated = $this->authService->validateToken($token);
        
        Log::debug('Auth Check - Sanctum validation result', ['authenticated' => $isAuthenticated]);

        return response()->json($isAuthenticated);
    }

    /**
     * Debug endpoint to check token details
     */
    public function debugToken(Request $request): JsonResponse
    {
        $token = $request->bearerToken() ?? $request->input('token');
        
        if (!$token) {
            return response()->json(['error' => 'No token provided']);
        }

        $tokenDetails = $this->authService->getTokenDetails($token);

        if (!$tokenDetails) {
            return response()->json([
                'error' => 'Token not found in personal_access_tokens table',
                'token_prefix' => substr($token, 0, 10) . '...',
                'token_length' => strlen($token),
            ]);
        }

        return response()->json($tokenDetails);
    }

    /**
     * Check auth with user details
     */
    public function checkAuthWithDetails(Request $request): JsonResponse
    {
        $token = $request->bearerToken() ?? $request->input('token');

        if (!$token) {
            return response()->json([
                'authenticated' => false,
                'message' => 'No token provided'
            ]);
        }

        $isAuthenticated = $this->authService->validateToken($token);

        if ($isAuthenticated) {
            $user = $this->authService->getUserFromToken($token);
            
            return response()->json([
                'authenticated' => true,
                'user' => $user
            ]);
        }

        return response()->json([
            'authenticated' => false,
            'message' => 'Invalid or expired token'
        ]);
    }
}