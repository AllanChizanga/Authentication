<?php

namespace App\DTOs;

use Illuminate\Http\Request;

/**
 * DTO for OTP verification for both registration and login
 */
class VerifyOtpDTO
{
    public function __construct(
        public string $phone_number,
        public string $otp_code,
        public string $session_token,
        public string $purpose, // 'registration' or 'login'
    ) {}

    public static function from_request(Request $request): self
    {
        return new self(
            phone_number: $request->input('phone_number'),
            otp_code: $request->input('otp_code'),
            session_token: $request->input('session_token'),
            purpose: $request->input('purpose'),
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