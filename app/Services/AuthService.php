<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthService
{

    protected FileService $file_service;
    protected OtpService $otp_service;
    protected TokenService $token_service;

    public function __construct(TokenService $token_service, OtpService $otp_service, FileService $file_service)
    {
        $this->token_service = $token_service;
        $this->otp_service = $otp_service;
        $this->file_service = $file_service;

    }
  

    /**
     * Register user with phone verification
     */
    public function register_user_with_phone(array $user_data, string $phone_number): User
    {
            // Prepare attributes for creation
    $attributes = [
        'fullname'      => trim($user_data['fullname']),
        'email'         => strtolower($user_data['email']),
        'phone'         => $phone_number,
        'city' => $user_data['city'],
        'country' => $user_data['country'],
        'profile_photo' => null, // default until we upload
        'id_photo' => null, // default until we upload
        'work_location' => $user_data['work_location'] ?? null,
        'home_location' => $user_data['home_location'] ?? null,
        'gender' => $user_data['gender'],
        'payment_preference' => $user_data['payment_preference'] ?? null,
        'is_activated' => $user_data['is_activated'] ?? false,
        'badge' => $user_data['badge'] ?? null,
    ];

    // Handle file upload (if provided)
   if (isset($user_data['profile_photo']) && $user_data['profile_photo'] instanceof \Illuminate\Http\UploadedFile) {
    $attributes['profile_photo'] = $this->file_service->upload_file($user_data['profile_photo']);
}
  if (isset($user_data['id_photo']) && $user_data['id_photo'] instanceof \Illuminate\Http\UploadedFile) {
    $attributes['id_photo'] = $this->file_service->upload_file($user_data['id_photo']);
}

        return User::create([
            'fullname' => $attributes['fullname'],
            'email' => $attributes['email'],
            'phone' => $attributes['phone'],
            'city' => $attributes['city'],
            'country' => $attributes['country'],
            'profile_photo' => $attributes['profile_photo'],
            'id_photo' => $attributes['id_photo'],
            'work_location' => $attributes['work_location'],
            'home_location' => $attributes['home_location'],
            'gender' => $attributes['gender'],
            'payment_preference' => $attributes['payment_preference'],  
            'is_activated' => $attributes['is_activated'],
            'badge' => $attributes['badge'],    
            'password' => isset($user_data['password']) ? Hash::make($user_data['password']) : null,
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