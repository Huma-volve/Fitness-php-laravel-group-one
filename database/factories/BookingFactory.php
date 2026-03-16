<?php

namespace Database\Factories;

use App\Models\Trainer;
use App\Models\TrainerPackage;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class BookingFactory extends Factory
{
    public function definition(): array
    {
        $status        = fake()->randomElement(['pending', 'confirmed', 'canceled']);
        $paymentStatus = match ($status) {
            'confirmed' => 'paid',
            'canceled'  => fake()->randomElement(['pending', 'refunded']),
            default     => 'pending',
        };

        // Reuse an existing trainer_package or create one
        $trainerPackage = TrainerPackage::factory()->create();

        return [
            'user_id'               => User::factory()->trainee(),
            'trainer_id'            => $trainerPackage->trainer_id,
            'trainer_package_id'    => $trainerPackage->id,
            'status'                => $status,
            'payment_status'        => $paymentStatus,
            'cancellation_deadline' => fake()->dateTimeBetween('+1 days', '+7 days'),
            'cancelled_at'          => $status === 'canceled' ? now() : null,
            'cancel_reason'         => $status === 'canceled' ? fake()->sentence() : null,
        ];
    }

    public function confirmed(): static
    {
        return $this->state([
            'status'         => 'confirmed',
            'payment_status' => 'paid',
            'cancelled_at'   => null,
            'cancel_reason'  => null,
        ]);
    }

    public function canceled(): static
    {
        return $this->state([
            'status'         => 'canceled',
            'payment_status' => 'refunded',
            'cancelled_at'   => now(),
            'cancel_reason'  => fake()->sentence(),
        ]);
    }
}
