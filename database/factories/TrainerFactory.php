<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TrainerFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id'          => User::factory()->trainer(),
            'bio'              => fake()->paragraphs(2, true),
            'experience_years' => fake()->numberBetween(1, 20),
            'location'         => fake()->city(),
            'rating'           => fake()->randomFloat(2, 1, 5),
            'total_reviews'    => fake()->numberBetween(0, 500),
        ];
    }
}
