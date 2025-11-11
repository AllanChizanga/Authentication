<?php

namespace Database\Factories;

use App\Models\OtpVerification;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class OtpVerificationFactory extends Factory
{
    protected $model = OtpVerification::class;

    public function definition()
    {
        return [
            'phone_number' => '+1' . $this->faker->numerify('##########'),
            'otp_code' => $this->faker->numerify('######'),
            'session_token' => Str::uuid()->toString(),
            'expires_at' => now()->addMinutes(10),
            'verified_at' => null,
            'attempts' => 0,
            'purpose' => 'registration',
            'user_id' => null,
            'registration_data' => null,
        ];
    }

    public function verified()
    {
        return $this->state(function (array $attributes) {
            return [
                'verified_at' => now(),
            ];
        });
    }

    public function registration()
    {
        return $this->state(function (array $attributes) {
            return [
                'purpose' => 'registration',
            ];
        });
    }

    public function login()
    {
        return $this->state(function (array $attributes) {
            return [
                'purpose' => 'login',
            ];
        });
    }

    public function forUser(User $user)
    {
        return $this->state(function (array $attributes) use ($user) {
            return [
                'user_id' => $user->id,
            ];
        });
    }

    public function expired()
    {
        return $this->state(function (array $attributes) {
            return [
                'expires_at' => now()->subMinutes(5),
            ];
        });
    }
}