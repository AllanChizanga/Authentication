<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthService
{
    public function __construct(
        private TokenService $token_service,
        private OtpService $otp_service
    ) {}

    /**
     * Register user with phone verification
     */
    public function register_user_with_phone(array $user_data, string $phone_number): User
    {
        return User::create([
            'name' => $user_data['name'],
            'email' => $user_data['email'],
            'phone_number' => $phone_number,
            'password' => Hash::make($user_data['password']),   

            'phone_verified_at' => now(), // Mark phone as verified
        ]);
    }

    /**
     * Create standardized authentication response
     */
    public function create_auth_response(User $user): array
    {
        $token = $this->token_service->create_auth_token($user);

        return [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone_number' => $user->phone_number,
                'phone_verified_at' => $user->phone_verified_at,
                'email_verified_at' => $user->email_verified_at,
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

    /**
     * Validate phone number availability for registration
     */
    public function validate_phone_for_registration(string $phone_number): void
    {
        if (User::phoneNumberExists($phone_number)) {
            throw ValidationException::withMessages([
                'phone_number' => ['This phone number is already registered.'],
            ]);
        }
    }

    /**
     * Validate phone number exists for login
     */
    public function validate_phone_for_login(string $phone_number): User
    {
        $user = User::findByPhoneNumber($phone_number);
        
        if (!$user) {
            throw ValidationException::withMessages([
                'phone_number' => ['No account found with this phone number.'],
            ]);
        }

        return $user;
    }
}