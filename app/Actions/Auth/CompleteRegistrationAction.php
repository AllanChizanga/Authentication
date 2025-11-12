<?php

namespace App\Actions\Auth;

use App\DTOs\CompleteRegistrationDTO;
use App\Services\OtpService;
use App\Services\AuthService;

/**
 * Action to complete registration after OTP verification
 */
class CompleteRegistrationAction
{
    public function __construct(
        private OtpService $otp_service,
        private AuthService $auth_service
    ) {}

    public function execute(CompleteRegistrationDTO $dto): array
    {
        
       
        // Verify session is valid for registration
        $otp_session = $this->otp_service->get_verified_registration_session($dto->session_token);
       

        if (!$otp_session) {
            throw new \Exception('Invalid or expired session. Please start registration again.');
        }

        // Create user with verified phone
        $user = $this->auth_service->register_user_with_phone(
            user_data: $dto->to_array(),
            phone_number: $otp_session->phone_number
        );
       
        // Create authentication response
        $authResponse = $this->auth_service->create_auth_response($user);

        // Clean up OTP session
        $otp_session->delete();

        return $authResponse;
    }
}