<?php

namespace App\Services;

use Illuminate\Http\JsonResponse;
use Laravel\Sanctum\PersonalAccessToken;

class UserService
{
    // User-related business logic can be implemented here

    public function get_user()
    {
        $user=Auth()->user();
        return $user;
        // Logic to retrieve user by ID
    }

    public function check_token($token): JsonResponse
    {
    $token = $token->bearerToken() ?? $token->input('token');
    
    if (!$token) {
        return response()->json(false);
    }

    try {
        $accessToken = PersonalAccessToken::findToken($token);
        
        if (!$accessToken) {
            return response()->json(false);
        }

        if ($accessToken->expires_at && $accessToken->expires_at->isPast()) {
            $accessToken->delete();
            return response()->json(false);
        }

        $user = $accessToken->tokenable_id;
        
        if (!$user) {
            return response()->json(false);
        }

        // Update last used
        $accessToken->forceFill([
            'last_used_at' => now(),
        ])->save();

        return response()->json([
            'authenticated' => true,
            'user' => [
                'id' => $user->id,
                'fullname' => $user->fullname,
                'is_activated' => $user->is_activated,
                'badge' => $user->badge
            ]
        ]);

    } catch (\Exception $e) {
        return response()->json(false);
    }
    }
    
}