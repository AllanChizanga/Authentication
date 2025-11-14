<?php

namespace App\DTOs\User;

use Spatie\LaravelData\Data;

class UserData extends Data
{
    public function __construct(
        public int $id,
        public string $fullname,
        public string $email,
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
        public string $is_activated,
        public string $badge,
        public string $password,
        public ?string $created_at,
        public ?string $updated_at,
    ) {}
    
    public static function fromModel($user): self
    {
        return new self(
            id: (int)$user->id,
            fullname: $user->fullname,
            email: $user->email,
            national_id: $user->national_id,
            phone: $user->phone,
            country: $user->country,
            city: $user->city,
            profile_photo: $user->profile_photo,
            id_photo: $user->id_photo,
          work_location: $user->work_location,
          home_location: $user->home_location,
          gender: $user->gender,
          payment_preference: $user->payment_preference,
          is_activated: $user->is_activated,
          badge: $user->badge,
          password: $user->password,
            created_at: $user->created_at?->toDateTime(),
            updated_at: $user->updated_at?->toDateTime(),
        );
    }
}