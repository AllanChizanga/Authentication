<?php

namespace App\DTOs;

use Illuminate\Http\Request;

/**
 * DTO for initiating both registration and login
 */
class InitiateAuthDTO
{
    public function __construct(
        public string $phone_number,
        public string $purpose, // 'registration' or 'login'
    ) {}

    public static function from_request(Request $request): self
    {
        return new self(
            phone_number: $request->input('phone_number'),
            purpose: $request->input('purpose'), // Must be provided
        );
    }

    public function is_registration(): bool
    {
        return $this->purpose === 'registration';
    }

    public function is_login(): bool
    {
        return $this->purpose === 'login';
    }
}