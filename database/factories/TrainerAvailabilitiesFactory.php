<?php

namespace Database\Factories;

use App\Models\Trainer;
use Illuminate\Database\Eloquent\Factories\Factory;

class TrainerAvailabilitiesFactory extends Factory
{
    public function definition(): array
    {
        $startHour  = fake()->numberBetween(6, 18);
        $endHour    = $startHour + fake()->numberBetween(1, 4);

        return [
            'trainer_id' => Trainer::factory(),
            'date'       => fake()->dateTimeBetween('now', '+3 months')->format('Y-m-d H:i:s'),
            'start_time' => sprintf('%02d:00:00', $startHour),
            'end_time'   => sprintf('%02d:00:00', min($endHour, 22)),
        ];
    }
}
