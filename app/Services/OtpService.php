<?php

namespace App\Services;

use App\Models\Otp;
use App\Models\User;
use Illuminate\Support\Str;
use App\Models\OtpVerification;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\ValidationException;

class OtpService
{
    private int $otp_length = 6;
    private int $otp_expiry_minutes = 10;
    private int $max_attempts = 3;
    private int $session_expiry_hours = 1;

    /**
     * Generate OTP for registration or login
     */
    public function generate_otp(string $phone_number, string $purpose = 'registration', ?User $user = null): array
    {
         
        
        $phone_number = $this->clean_phone_number($phone_number);
        
        // Validate purpose
        if (!in_array($purpose, ['registration', 'login'])) {
            throw new \InvalidArgumentException('Invalid OTP purpose');
        }
          
      
        // Check rate limiting
        $this->check_rate_limit($phone_number, $purpose);
          
        // For registration, check if phone already exists
    
        if ($purpose === 'registration' && User::where('phone',$phone_number)->exists()) {
            throw ValidationException::withMessages([
                'phone_number' => ['This phone number is already registered.'],
            ]);
        }

        // For login, check if user exists and get user ID
        if ($purpose === 'login') {
            $user = User::where('phone',$phone_number)->first();
            if (!$user) {
                throw ValidationException::withMessages([
                    'phone_number' => ['No account found with this phone number.'],
                ]);
            }
        }

        // Generate OTP code
        $otp_code = $this->generate_otp_code();
        
    
        // Create session token
        $session_token = Str::uuid()->toString();
       
        // Create OTP record
        $otp_data = [
            'phone_number' => $phone_number,
            'otp_code' => $otp_code,
            'session_token' => $session_token,
            'expires_at' => now()->addMinutes($this->otp_expiry_minutes),
            'purpose' => $purpose,
        ];
 
        if ($user) {
            $otp_data['user_id'] = $user->id;
        }

        
        $otp_verification = Otp::create($otp_data);
        
        // Send OTP via SMS
      //  $this->send_otp_sms($phone_number, $otp_code, $purpose);
        
        // Set rate limit
        $this->set_rate_limit($phone_number, $purpose);

        return [
            'session_token' => $session_token,
            'expires_in' => $this->otp_expiry_minutes * 60,
            'message' => 'OTP sent successfully',
            'purpose' => $purpose,
            'otp_code'=>$otp_code,
        ];
    }

    /**
     * Verify OTP for registration or login
     */
    public function verify_otp(string $phone_number, string $otp_code, string $session_token, string $purpose)
    {
       
        
        $phone_number = $this->clean_phone_number($phone_number);
        
        $otp_verification = Otp::where('session_token', $session_token)
            ->where('phone_number', $phone_number)
            ->first();
        
        if (!$otp_verification) {
            throw ValidationException::withMessages([
                'otp_code' => ['Invalid or expired OTP.'],
            ]);
        }
      
       
        // Increment attempts
        $otp_verification->increment_attempts();

       
        // Check if max attempts exceeded
        if ($otp_verification->attempts >= $this->max_attempts) {
            $otp_verification->delete();
            throw ValidationException::withMessages([
                'otp_code' => ['Too many attempts. Please request a new OTP.'],
            ]);
        }
        
        // Verify OTP code
        if ($otp_verification->otp_code !== $otp_code) {
            throw ValidationException::withMessages([
                'otp_code' => ['Invalid OTP code.'],
            ]);
        }

        // Mark as verified
        $otp_verification->mark_as_verified();

        return $otp_verification;
    }

    /**
     * Get verified session for registration completion
     */
    public function get_verified_registration_session(string $session_token): ?Otp
    {
        return Otp::where('session_token', $session_token)
            ->where('expires_at', '>', now()->subHours($this->session_expiry_hours))
            ->first();
    }

    /**
     * Get verified session for login completion
     */
    public function get_verified_login_session(string $session_token): ?Otp
    {
        return Otp::where('session_token', $session_token)
            ->with('user') // Eager load user
            ->first();
    }

    /**
     * Clean phone number format
     */
    private function clean_phone_number(string $phoneNumber): string
    {
        return preg_replace('/[^0-9+]/', '', $phoneNumber);
    }

    /**
     * Generate numeric OTP code
     */
    private function generate_otp_code(): string
    {
        $min = pow(10, $this->otp_length - 1);
        $max = pow(10, $this->otp_length) - 1;
        
        return (string) rand($min, $max);
    }

    /**
     * Check rate limiting for phone number and purpose
     */
    private function check_rate_limit(string $phone_number, string $purpose): void
    {
    
        $key = "otp_rate_limit:{$phone_number}:{$purpose}";
        $attempts = Cache::get($key, 0);

        if ($attempts >= 5) {
            throw ValidationException::withMessages([
                'phone_number' => ['Too many OTP requests. Please try again in 1 hour.'],
            ]);
        }
    }

    /**
     * Set rate limit for phone number and purpose
     */
    private function set_rate_limit(string $phone_number, string $purpose): void
    {
        $key = "otp_rate_limit:{$phone_number}:{$purpose}";
        Cache::put($key, Cache::get($key, 0) + 1, now()->addHour());
    }

    /**
     * Send OTP via SMS
     */
    private function send_otp_sms(string $phone_number, string $otp_code, string $purpose): void
    {
        $message = $purpose === 'registration' 
            ? "Your registration verification code is: {$otp_code}. Valid for 10 minutes."
            : "Your login verification code is: {$otp_code}. Valid for 10 minutes.";

        // Implement your SMS service here
        \Log::info("SMS to {$phone_number}: {$message}");
        
        // Example with Twilio:
        // Twilio::message($phoneNumber, $message);
    }

    /**
     * Clean up expired OTPs
     */
    public function cleanup_expired_otps(): void
    {
        Otp::where('expires_at', '<', now())->delete();
    }
}