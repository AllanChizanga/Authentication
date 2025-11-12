<?php

namespace App\DTOs;

use Illuminate\Http\Request;

class CompleteRegistrationDTO
{
    public function __construct(
        public string $fullname,
        public string $national_id,
        public string $phone,
        public string $country,
        public string $city,
        public ?string $profile_photo = null,
        public ?string $id_photo = null,
        public ?string $work_location = null,
        public ?string $home_location = null,
        public ?string $gender = null,
        public string $payment_preference = 'cash',
        public bool $is_activated = false,
        public string $badge = 'red',
        public ?string $session_token = null,
    ) {}

    /**
     * Create DTO from HTTP Request
     */
    public static function from_request(Request $request): self
    {
        return new self(
            fullname: $request->input('fullname'),
            national_id: $request->input('national_id', ''),
            phone: $request->input('phone', ''),
            country: $request->input('country', ''),
            city: $request->input('city', ''),
            profile_photo: $request->input('profile_photo'),
            id_photo: $request->input('id_photo'),
            work_location: $request->input('work_location'),
            home_location: $request->input('home_location'),
            gender: $request->input('gender'),
            payment_preference: $request->input('payment_preference', 'cash'),
            is_activated: $request->boolean('is_activated', false),
            badge: $request->input('badge', 'red'),
            session_token:$request->input('session_token'),
        );
    }

    /**
     * Create DTO from array (NEW METHOD)
     */
    public static function from_array(array $data): self
    {
        return new self(
            fullname: $data['fullname'] ?? '',
            national_id: $data['national_id'] ?? '',
            phone: $data['phone'] ?? '',
            country: $data['country'] ?? '',
            city: $data['city'] ?? '',
            profile_photo: $data['profile_photo'] ?? null,
            id_photo: $data['id_photo'] ?? null,
            work_location: $data['work_location'] ?? null,
            home_location: $data['home_location'] ?? null,
            gender: $data['gender'] ?? null,
            payment_preference: $data['payment_preference'] ?? 'cash',
            is_activated: $data['is_activated'] ?? false,
            badge: $data['badge'] ?? 'red',
            session_token: $data['session_token'] ?? null,
        );
    }

    // ... rest of your methods
}