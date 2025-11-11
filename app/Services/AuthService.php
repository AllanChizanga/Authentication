<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

/**
 * Service handling authentication business logic
 * Separates auth logic from controller and actions
 */
class AuthService
{
    public function __construct(
        private TokenService $token_service
    ) {}

    /**
     * Register a new user with the provided data
     * Returns the created user model
     */
    public function register_user(array $user_data): User
    {
        return User::create([
            'name' => $user_data['name'],
            'email' => $user_data['email'],
            'password' => Hash::make($user_data['password']), // Hash password here
        ]);
    }

    /**
     * Authenticate user with credentials
     * Throws ValidationException if credentials are invalid
     */
    public function authenticate_user(array $credentials): User
    {
        $user = User::where('phone', $credentials['phone_number'])->first();

        if (!$user) {
            throw ValidationException::withMessages([
                'phone' => ['The provided credentials are incorrect.'],
            ]);
        }
        //check otp logic can be added here

        return $user;
    }

    /**
     * Create standardized authentication response
     * Returns user data with access token
     */
    public function create_auth_response(User $user): array
    {
        $token = $this->token_service->create_auth_token($user);

        return [
            'user' => [
                'id' => $user->id,
                'fullname' => $user->fullname,
                'email' => $user->email,
            ],
            'token' => $token->plainTextToken,
        ];
    }

    /**
     * Get user by ID
     */
    public function get_user_by_id(int $user_id): ?User
    {
        return User::find($user_id);
    }
}