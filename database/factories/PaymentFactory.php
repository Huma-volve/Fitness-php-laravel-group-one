<?php

namespace Database\Factories;

use App\Models\Booking;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class PaymentFactory extends Factory
{
    public function definition(): array
    {
        $booking = Booking::factory()->confirmed()->create();
        $price   = $booking->trainerPackage->price;

        return [
            'booking_id'     => $booking->id,
            'amount'         => $price,
            'payment_method' => fake()->randomElement(['paypal', 'stripe']),
            'payment_status' => 'paid',
            'transaction_id' => Str::upper(fake()->bothify('TXN-########-????')),
        ];
    }

    public function failed(): static
    {
        return $this->state(['payment_status' => 'failed']);
    }
}
