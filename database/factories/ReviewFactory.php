<?php

namespace Database\Factories;

use App\Models\Trainer;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReviewFactory extends Factory
{
    public function definition(): array
    {
        return [
            'trainer_id' => Trainer::factory(),
            'user_id'    => User::factory()->trainee(),
            'rating'     => fake()->numberBetween(1, 5),
            'comment'    => fake()->optional(0.8)->paragraph(),
        ];
    }
}
