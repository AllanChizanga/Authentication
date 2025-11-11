<?php

namespace App\DTOs;

use Illuminate\Http\Request;

/**
 * Data Transfer Object for user registration
 * This encapsulates the registration data and provides a clean way to pass it between layers
 */
class RegisterUserDTO
{
    public function __construct(
        public string $fullname,
        public string $email,
        public string $password,
         
        'national_id',
        'phone',
        'country',
        'city',
    
        'profile_photo',
        'id_photo',
        'work_location',
        'home_location',
        'gender',
        'payment_preference',
        'is_activated',
        'badge',
    ) {}

    /**
     * Create DTO from HTTP Request
     * This method handles the transformation from raw request data to structured DTO
     */
    public static function fromRequest(Request $request): self
    {
        return new self(
            name: $request->input('name'),
            email: $request->input('email'),
            password: $request->input('password'),
        );
    }

    /**
     * Convert DTO to array for database operations
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'email' => $this->email,
            'password' => $this->password, // Will be hashed in service
        ];
    }
}