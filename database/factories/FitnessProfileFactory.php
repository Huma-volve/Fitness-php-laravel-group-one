<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class FitnessProfileFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id'                => User::factory()->trainee(),
            'gender'                 => fake()->randomElement(['male', 'female', 'other']),
            'age'                    => fake()->numberBetween(16, 65),
            'height_cm'              => fake()->numberBetween(150, 210),
            'weight_kg'              => fake()->randomFloat(2, 45, 150),
            'fitness_goal'           => fake()->randomElement(['weight_loss', 'muscle_gain', 'endurance', 'flexibility', 'general_fitness', 'strength']),
            'fitness_level'          => fake()->randomElement(['beginner', 'intermediate', 'advanced']),
            'workout_location'       => fake()->randomElement(['online', 'in_person_training', 'both']),
            'preferred_training_days'=> fake()->randomElement(['1-2', '3-4', '5+']),
        ];
    }
}
