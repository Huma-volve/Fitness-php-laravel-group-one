<?php

namespace Database\Factories;

use App\Models\Trainer;
use Illuminate\Database\Eloquent\Factories\Factory;

class TrainerAvailabilitiesFactory extends Factory
{
    public function definition(): array
    {
        $startHour = fake()->numberBetween(6, 14);
        $endHour   = $startHour + fake()->numberBetween(4, 8);

        return [
            'trainer_id'  => Trainer::factory(),
            'day_of_week' => fake()->randomElement([
                'monday', 'tuesday', 'wednesday',
                'thursday', 'friday', 'saturday', 'sunday',
            ]),
            'start_time'  => sprintf('%02d:00:00', $startHour),
            'end_time'    => sprintf('%02d:00:00', min($endHour, 22)),
            'is_active'   => true,
        ];
    }
}
