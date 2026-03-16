<?php

namespace Database\Factories;

use App\Models\Package;
use App\Models\Trainer;
use Illuminate\Database\Eloquent\Factories\Factory;

class TrainerPackageFactory extends Factory
{
    public function definition(): array
    {
        return [
            'trainer_id' => Trainer::factory(),
            'package_id' => Package::factory(),
            'price'      => fake()->randomFloat(2, 20, 300),
            'is_active'  => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(['is_active' => false]);
    }
}
