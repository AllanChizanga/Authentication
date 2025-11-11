<?php

namespace App\DTOs;

use Illuminate\Http\Request;

/**
 * Data Transfer Object for user login
 * Encapsulates login credentials and validation
 */
class LoginUserDTO
{
    public function __construct(
        public string $phone_number,
        public string $otp,
    ) {}

    /**
     * Create DTO from HTTP Request
     */
    public static function fromRequest(Request $request): self
    {
        return new self(
            phone_number: $request->input('phone_number'),
            otp: $request->input('otp'),
        );
    }

    /**
     * Convert to array for authentication
     */
    public function toArray(): array
    {
        return [
            'phone_number' => $this->phone_number,
            'otp' => $this->otp,
        ];
    }
}