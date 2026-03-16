<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name'              => fake()->name(),
            'email'             => fake()->unique()->safeEmail(),
            'password'          => Hash::make('password'),
            'role'              => fake()->randomElement(['trainer', 'trainee']),
            'phone'             => fake()->phoneNumber(),
            'profile_image'     => fake()->imageUrl(200, 200, 'people'),
            'status'            => fake()->randomElement(['active', 'inactive']),
            'email_verified_at' => now(),
        ];
    }

    public function admin(): static
    {
        return $this->state(['role' => 'admin']);
    }

    public function trainer(): static
    {
        return $this->state(['role' => 'trainer']);
    }

    public function trainee(): static
    {
        return $this->state(['role' => 'trainee']);
    }

    public function unverified(): static
    {
        return $this->state(['email_verified_at' => null]);
    }
}
