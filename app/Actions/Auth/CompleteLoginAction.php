<?php

namespace App\Actions\Auth;

use App\DTOs\CompleteLoginDTO;
use App\Services\OtpService;
use App\Services\AuthService;

/**
 * Action to complete login after OTP verification
 */
class CompleteLoginAction
{
    public function __construct(
        private OtpService $otp_service,
        private AuthService $auth_service
    ) {}

    public function execute(CompleteLoginDTO $dto): array
    {
         
        // Verify session is valid for login
        $otpSession = $this->otp_service->get_verified_login_session($dto->session_token);
        
        if (!$otpSession || !$otpSession->user) {
           return['error'=>'Invalid or expired session. Please start login again.'];
        }

        $user = $otpSession->user;
    
        // Mark phone as verified if not already
        // if (!$user->hasVerifiedPhone()) {
        //     $user->markPhoneAsVerified();
        // }
               
        // Create authentication response
        $authResponse = $this->auth_service->create_auth_response($user);
        
        // Clean up OTP session
        $otpSession->delete();

        return $authResponse;
    }
}