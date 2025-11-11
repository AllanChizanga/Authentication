<?php

namespace App\Actions\Auth;

use App\DTOs\InitiateAuthDTO;
use App\Services\OtpService;

/**
 * Action to initiate registration by sending OTP to phone
 */
class InitiateRegistrationAction
{
    public function __construct(
        private OtpService $otp_service
    ) {}

    public function execute(InitiateAuthDTO $dto): array
    {
        
        return $this->otp_service->generate_otp(
            phone_number: $dto->phone_number,
            purpose: 'registration'
        );
    }
}