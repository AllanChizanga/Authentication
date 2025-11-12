<?php

namespace App\Actions\Auth;

use App\DTOs\VerifyOtpDTO;
use App\Services\OtpService;

/**
 * Action to verify OTP for registration
 */
class VerifyRegistrationOtpAction
{
    public function __construct(
        private OtpService $otp_service
    ) {}

    public function execute(VerifyOtpDTO $dto): array
    {
        
        
        $otpVerification = $this->otp_service->verify_otp(
            phone_number: $dto->phone_number,
            otp_code: $dto->otp_code,
            session_token: $dto->session_token,
            purpose: 'registration'
        );
      

        return [
            'session_token' => $otpVerification->session_token,
            'message' => 'OTP verified successfully. You can now complete registration.',
            'verified_at' => $otpVerification->verified_at,
        ];
    }
}