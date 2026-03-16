<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Payment;
use App\Models\Review;
use App\Models\Trainer;
use App\Models\TraineeSession;
use App\Models\TrainerPackage;
use App\Models\TrainerPayout;
use App\Models\User;
use Illuminate\Database\Seeder;

class BookingSeeder extends Seeder
{
    public function run(): void
    {
        $traineeIds = User::where('role', 'trainee')->pluck('id')->toArray();
        $trainers   = Trainer::with('trainerPackages')->get();

        foreach ($trainers as $trainer) {
            // Skip trainers with no packages configured
            if ($trainer->trainerPackages->isEmpty()) {
                continue;
            }

            $bookingCount = fake()->numberBetween(3, 6);

            for ($i = 0; $i < $bookingCount; $i++) {

                /** @var TrainerPackage $trainerPackage */
                $trainerPackage = $trainer->trainerPackages->random();

                $status        = fake()->randomElement(['pending', 'confirmed', 'canceled']);
                $paymentStatus = match ($status) {
                    'confirmed' => 'paid',
                    'canceled'  => fake()->randomElement(['pending', 'refunded']),
                    default     => 'pending',
                };

                $booking = Booking::create([
                    'user_id'               => fake()->randomElement($traineeIds),
                    'trainer_id'            => $trainer->id,
                    'trainer_package_id'    => $trainerPackage->id,
                    'status'                => $status,
                    'payment_status'        => $paymentStatus,
                    'cancellation_deadline' => now()->addDays(3),
                    'cancelled_at'          => $status === 'canceled' ? now() : null,
                    'cancel_reason'         => $status === 'canceled' ? fake()->sentence() : null,
                ]);

                // Payment for every booking
                Payment::create([
                    'booking_id'     => $booking->id,
                    'amount'         => $trainerPackage->price,
                    'payment_method' => fake()->randomElement(['paypal', 'stripe']),
                    'payment_status' => $paymentStatus,
                    'transaction_id' => strtoupper(fake()->bothify('TXN-########-????')),
                ]);

                // Sessions & payouts only for confirmed bookings
                if ($status === 'confirmed') {
                    $sessionCount = fake()->numberBetween(1, 3);

                    for ($s = 0; $s < $sessionCount; $s++) {
                        $start = fake()->dateTimeBetween('-2 weeks', '+4 weeks');
                        $end   = (clone $start)->modify('+' . fake()->numberBetween(45, 90) . ' minutes');

                        TraineeSession::create([
                            'booking_id'     => $booking->id,
                            'trainer_id'     => $trainer->id,
                            'client_id'      => $booking->user_id,
                            'session_start'  => $start,
                            'session_end'    => $end,
                            'session_status' => fake()->randomElement(['scheduled', 'completed', 'no_show']),
                            'notes'          => fake()->optional()->sentence(),
                        ]);
                    }

                    // Trainer payout (80% cut)
                    $trainerCut = round($trainerPackage->price * 0.80, 2);

                    TrainerPayout::create([
                        'trainer_id'     => $trainer->id,
                        'booking_id'     => $booking->id,
                        'trainer_amount' => $trainerCut,
                        'platform_fee'   => round($trainerPackage->price - $trainerCut, 2),
                        'payout_status'  => fake()->randomElement(['pending', 'paid']),
                        'payout_date'    => fake()->optional()->dateTimeBetween('now', '+30 days'),
                    ]);

                    // Review (60% chance)
                    if (fake()->boolean(60)) {
                        Review::create([
                            'trainer_id' => $trainer->id,
                            'user_id'    => $booking->user_id,
                            'rating'     => fake()->numberBetween(3, 5),
                            'comment'    => fake()->optional(0.8)->paragraph(),
                        ]);
                    }
                }
            }
        }
    }
}
