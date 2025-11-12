<?php

namespace App\Services;

use App\Models\User;
use Laravel\Sanctum\NewAccessToken;

/**
 * Service dedicated to token management
 * Handles all token-related operations for Sanctum
 */
class TokenService
{
    /**
     * Create a new authentication token for user
     * Returns NewAccessToken which contains plainTextToken
     */
    public function create_auth_token(User $user): NewAccessToken
    {
        //dd($user->createToken('auth-token', ['*'], now()->addYear(5)));
        return $user->createToken('auth-token', ['*'], now()->addYear(5));
    }

    /**
     * Delete current access token (logout)
     */
    public function delete_current_token(User $user): void
    {
        $user->currentAccessToken()->delete();
    }

    /**
     * Delete all tokens for user (logout from all devices)
     */
    public function delete_all_tokens(User $user): void
    {
        $user->tokens()->delete();
    }

    /**
     * Check if token is valid and not expired
     */
    public function is_valid_token(User $user): bool
    {
        return $user->tokens()
            ->where('created_at', '>=', now()->subYear()) // Within expiry
            ->exists();
    }
}