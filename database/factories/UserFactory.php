<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected static ?string $password;

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'phone_number' => '+1' . fake()->numerify('##########'),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
        ];
    }

    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    public function phoneUnverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'phone_verified_at' => null,
        ]);
    }

    public function withPhoneNumber(string $phoneNumber): static
    {
        return $this->state(fn (array $attributes) => [
            'phone_number' => $phoneNumber,
        ]);
    }
}