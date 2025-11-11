<?php

namespace App\DTOs;

use Illuminate\Http\Request;

/**
 * DTO for completing login after OTP verification
 */
class CompleteLoginDTO
{
    public function __construct(
        public string $session_token,
    ) {}

    public static function from_request(Request $request): self
    {
        return new self(
            session_token: $request->input('session_token'),
        );
    }
}