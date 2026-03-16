<?php

namespace Database\Factories;

use App\Models\Booking;
use App\Models\Trainer;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TraineeSessionFactory extends Factory
{
    public function definition(): array
    {
        $start = fake()->dateTimeBetween('-1 month', '+1 month');
        $end   = (clone $start)->modify('+' . fake()->numberBetween(30, 90) . ' minutes');

        return [
            'booking_id'     => Booking::factory()->confirmed(),
            'trainer_id'     => Trainer::factory(),
            'client_id'      => User::factory()->trainee(),
            'session_start'  => $start,
            'session_end'    => $end,
            'session_status' => fake()->randomElement(['scheduled', 'completed', 'cancelled', 'no_show']),
            'notes'          => fake()->optional()->paragraph(),
        ];
    }

    public function completed(): static
    {
        return $this->state(['session_status' => 'completed']);
    }

    public function scheduled(): static
    {
        return $this->state(['session_status' => 'scheduled']);
    }
}
