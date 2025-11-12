<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\PersonalAccessToken;
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
        'email'         => strtolower($user_data['email']) ?? null,
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

  public function validateToken(string $token): bool
    {
        try {
            Log::debug('Sanctum Token Validation', ['token_prefix' => substr($token, 0, 10) . '...']);

            // Find the token in personal_access_tokens table
            $accessToken = PersonalAccessToken::findToken($token);
            
            if (!$accessToken) {
                Log::debug('Sanctum: Token not found in personal_access_tokens table');
                return false;
            }

            // Check if token is expired
            if ($accessToken->expires_at && $accessToken->expires_at->isPast()) {
                Log::debug('Sanctum: Token expired', ['expires_at' => $accessToken->expires_at]);
                $accessToken->delete(); // Clean up expired token
                return false;
            }

            // Get the user associated with the token
            $user = $accessToken->tokenable;
            
            if (!$user) {
                Log::debug('Sanctum: No user associated with token');
                return false;
            }

            // Check if user is active/activated
            if (isset($user->is_activated) && !$user->is_activated) {
                Log::debug('Sanctum: User is not activated', ['user_id' => $user->id]);
                return false;
            }

            if (isset($user->is_active) && !$user->is_active) {
                Log::debug('Sanctum: User is not active', ['user_id' => $user->id]);
                return false;
            }

            // Update last used at timestamp
            $accessToken->forceFill([
                'last_used_at' => now(),
            ])->save();

            Log::debug('Sanctum: Token validated successfully', [
                'user_id' => $user->id,
                'token_id' => $accessToken->id
            ]);

            return true;

        } catch (\Exception $e) {
            Log::error('Sanctum token validation failed: ' . $e->getMessage());
            return false;
        }
    }

    public function getUserFromToken(string $token): ?array
    {
        try {
            $accessToken = PersonalAccessToken::findToken($token);
            
            if (!$accessToken) {
                return null;
            }

            $user = $accessToken->tokenable;

            if (!$user) {
                return null;
            }

            return [
                'id' => $user->id,
                'name' => $user->fullname, // Using fullname from your fillable array
                'email' => $user->email,
                'phone' => $user->phone,
                'country' => $user->country,
                'city' => $user->city,
                'is_activated' => $user->is_activated ?? false,
                'badge' => $user->badge ?? 'red',
                'profile_photo' => $user->profile_photo,
                'token_id' => $accessToken->id,
                'token_name' => $accessToken->name,
                'last_used_at' => $accessToken->last_used_at,
                'expires_at' => $accessToken->expires_at,
            ];

        } catch (\Exception $e) {
            Log::error('Get user from Sanctum token failed: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get detailed token information for debugging
     */
    public function getTokenDetails(string $token): ?array
    {
        try {
            $accessToken = PersonalAccessToken::findToken($token);
            
            if (!$accessToken) {
                return null;
            }

            $user = $accessToken->tokenable;

            return [
                'token' => [
                    'id' => $accessToken->id,
                    'name' => $accessToken->name,
                    'abilities' => $accessToken->abilities,
                    'last_used_at' => $accessToken->last_used_at,
                    'expires_at' => $accessToken->expires_at,
                    'created_at' => $accessToken->created_at,
                ],
                'user' => $user ? [
                    'id' => $user->id,
                    'fullname' => $user->fullname,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'is_activated' => $user->is_activated,
                    'badge' => $user->badge,
                ] : null
            ];

        } catch (\Exception $e) {
            Log::error('Get token details failed: ' . $e->getMessage());
            return null;
        }
    }



     public function validateToken_test(string $token): bool
    {
        try {
            Log::debug('Sanctum Token Validation', ['token_prefix' => substr($token, 0, 10) . '...']);

            $accessToken = PersonalAccessToken::findToken($token);
            
            if (!$accessToken) {
                Log::debug('Sanctum: Token not found in personal_access_tokens table');
                return false;
            }

            if ($accessToken->expires_at && $accessToken->expires_at->isPast()) {
                Log::debug('Sanctum: Token expired', ['expires_at' => $accessToken->expires_at]);
                $accessToken->delete();
                return false;
            }

            $user = $accessToken->tokenable;
            
            if (!$user) {
                Log::debug('Sanctum: No user associated with token');
                return false;
            }

            // Update last used at timestamp
            $accessToken->forceFill([
                'last_used_at' => now(),
            ])->save();

            Log::debug('Sanctum: Token validated successfully', [
                'user_id' => $user->id,
                'token_id' => $accessToken->id
            ]);

            return true;

        } catch (\Exception $e) {
            Log::error('Sanctum token validation failed: ' . $e->getMessage());
            return false;
        }
    }

    public function getUserDetailsFromToken(string $token): ?array
    {
        try {
            $accessToken = PersonalAccessToken::findToken($token);
            
            if (!$accessToken) {
                return null;
            }

            $user = $accessToken->tokenable;

            if (!$user) {
                return null;
            }

            // Check if user is a driver (you might need to adjust this logic)
            $isDriver = $this->checkIfUserIsDriver($user);
            
            return [
                'is_driver' => $isDriver,
                'badge' => $user->badge ?? 'red',
                'is_active' => $user->is_activated ?? false,
                'user_details' => [
                    'id' => $user->id,
                    'name' => $user->fullname,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'country' => $user->country,
                    'city' => $user->city,
                    'profile_photo' => $user->profile_photo,
                ]
            ];

        } catch (\Exception $e) {
            Log::error('Get user details from token failed: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Check if user is a driver - adjust this based on your business logic
     */
    private function checkIfUserIsDriver(User $user): bool
    {
        // Method 1: Check if user has a driver profile (if you have a drivers table)
        // return \App\Models\Driver::where('user_id', $user->id)->exists();
        
        // Method 2: Check based on user role or type
        // return $user->user_type === 'driver';
        
        // Method 3: Check if user has driver-specific fields filled
        // return !empty($user->license_url) || !empty($user->vehicle_info);
        
        // Method 4: Check a specific driver flag
        // return $user->is_driver ?? false;

        // For now, return true if user has badge (adjust based on your logic)
        return !empty($user->badge);
    }

    /**
     * Get complete authentication status
     */
    public function getAuthenticationStatus(string $token): array
    {
        $isAuthenticated = $this->validateToken($token);
        
        if (!$isAuthenticated) {
            return [
                'authenticated' => false,
                'is_driver' => false,
                'badge' => 'red',
                'is_active' => false
            ];
        }

        $userDetails = $this->getUserDetailsFromToken($token);

        return [
            'authenticated' => true,
            'is_driver' => $userDetails['is_driver'] ?? false,
            'badge' => $userDetails['badge'] ?? 'red',
            'is_active' => $userDetails['is_active'] ?? false,
            'user' => $userDetails['user_details'] ?? null
        ];
    }

}