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

    /**
     * Simple authentication check returning only true/false
     */
    public function checkAuth(Request $request): JsonResponse
    {
        $token = $request->bearerToken() ?? $request->input('token');
        
        if (!$token) {
            return response()->json(false);
        }

        $isAuthenticated = $this->authService->validateToken($token);
        
        return response()->json($isAuthenticated);
    }

    /**
     * Detailed authentication check with driver status, badge, and active status
     */
    public function isDriver(Request $request): JsonResponse
    {
        $token = $request->bearerToken() ?? $request->input('token');

        if (!$token) {
            return response()->json([
              false
            ]);
        }

        $authStatus = $this->authService->getAuthenticationStatus($token);

        return response()->json($authStatus);
    }

       public function getBadge(Request $request): JsonResponse
    {
        $token = $request->bearerToken() ?? $request->input('token');

        if (!$token) {
            return response()->json([
                false
            ]);
        }

        $isAuthenticated = $this->authService->validateToken($token);

        if (!$isAuthenticated) {
            return response()->json([
                false
            ]);
        }

        $userDetails = $this->authService->getUserDetailsFromToken($token);

        return response()->json([
            'badge' => $userDetails['badge'] ?? 'red',
        ]);
    }

    /**
     * Check only isActivated information
     */
    public function isActivated(Request $request): JsonResponse
    {
        $token = $request->bearerToken() ?? $request->input('token');

        if (!$token) {
            return response()->json([
                false
            ]);
        }

        $isAuthenticated = $this->authService->validateToken($token);

        if (!$isAuthenticated) {
            return response()->json([
                false
            ]);
        }

        $userDetails = $this->authService->getUserDetailsFromToken($token);

        return response()->json([
            'is_active' => $userDetails['is_active'] ?? false
        ]);
    }

    /**
     * Debug endpoint to check all token details
     */
    public function debugToken(Request $request): JsonResponse
    {
        $token = $request->bearerToken() ?? $request->input('token');
        
        if (!$token) {
            return response()->json(['error' => 'No token provided']);
        }

        $authStatus = $this->authService->getAuthenticationStatus($token);
        $userDetails = $this->authService->getUserDetailsFromToken($token);

        return response()->json([
            'token_info' => [
                'token_prefix' => $token ? substr($token, 0, 10) . '...' : 'NULL',
                'token_length' => strlen($token),
            ],
            'authentication_status' => $authStatus,
            'user_details' => $userDetails
        ]);
    }
}