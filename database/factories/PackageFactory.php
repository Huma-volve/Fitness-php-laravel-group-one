<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class PackageFactory extends Factory
{
    public function definition(): array
    {
        $tiers = [
            ['title' => 'Single',  'sessions' => 1,   'duration_days' => 60],
            ['title' => 'Monthly', 'sessions' => 8,   'duration_days' => 60],
            ['title' => 'Premium', 'sessions' => 999, 'duration_days' => 60],
        ];

        $tier = fake()->randomElement($tiers);

        return [
            'title'             => $tier['title'],
            'description'       => fake()->paragraph(),
            'sessions'          => $tier['sessions'],
            'duration_days'     => $tier['duration_days'],
            'progress_tracking' => $tier['title'] !== 'Single',
            'nutrition_plan'    => $tier['title'] !== 'Single',
            'priority_booking'  => $tier['title'] === 'Premium',
            'full_access'       => $tier['title'] === 'Premium',
        ];
    }
}
