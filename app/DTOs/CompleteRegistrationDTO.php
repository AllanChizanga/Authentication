<?php

namespace App\DTOs;

use Illuminate\Http\Request;

/**
 * DTO for completing registration after OTP verification
 */
class CompleteRegistrationDTO
{
    public function __construct(
        public string $fullname,
        public string $email,
        public string $password,
        public string $national_id,
        public string $phone,
        public string $country,
        public string $city,
        public string $profile_photo,
        public string $id_photo,
        public string $work_location,
        public string $home_location,
        public string $gender,
        public string $payment_preference,
        public bool $is_activated,
        public string $badge,
    ) {}

 /**
     * Create DTO from HTTP Request
     * This method handles the transformation from raw request data to structured DTO
     */
    public static function from_request(Request $request): self
    {
        return new self(
            fullname: $request->input('fullname'),
            email: $request->input('email'),
            password: $request->input('password'),
            national_id: $request->input('national_id', ''),
            phone: $request->input('phone', ''),
            country: $request->input('country', ''),
            city: $request->input('city', ''),
            profile_photo: $request->input('profile_photo', ''),
            id_photo: $request->input('id_photo', ''),
            work_location: $request->input('work_location', ''),
            home_location: $request->input('home_location', ''),
            gender: $request->input('gender', ''),
            payment_preference: $request->input('payment_preference', ''),
            is_activated: $request->boolean('is_activated', false),
            badge: $request->input('badge', ''),
        );
    }
     /**
     * Convert DTO to array for database operations
     */
    public function to_array(): array
    {
        return [
            'name' => $this->fullname,
            'email' => $this->email,
            'password' => $this->password, // Will be hashed in service
            'national_id' => $this->national_id,
            'phone' => $this->phone,
            'country' => $this->country,
            'city' => $this->city,
            'profile_photo' => $this->profile_photo,
            'id_photo' => $this->id_photo,
            'work_location' => $this->work_location,
            'home_location' => $this->home_location,
            'gender' => $this->gender,
            'payment_preference' => $this->payment_preference,
            'is_activated' => $this->is_activated,
            'badge' => $this->badge,
        ];
    }
}