<?php

namespace Database\Seeders;

use App\Models\Package;
use App\Models\Specialization;
use App\Models\Trainer;
use App\Models\TrainerCertification;
use App\Models\TrainerPackage;
use App\Models\TrainerSpecialization;
use App\Models\User;
use Illuminate\Database\Seeder;

class TrainerSeeder extends Seeder
{
    private array $basePrices = [
        'Single'  => 29.99,
        'Monthly' => 99.99,
        'Premium' => 199.99,
    ];

    public function run(): void
    {
        $specializationIds = Specialization::pluck('id')->toArray();
        $packages          = Package::all()->keyBy('title');

        User::factory(10)->trainer()->create()->each(function (User $user) use ($specializationIds, $packages) {

            $trainer = Trainer::factory()->create(['user_id' => $user->id]);

            // ── Certifications ────────────────────────────────────────────────
            TrainerCertification::factory()
                ->count(fake()->numberBetween(1, 3))
                ->create(['trainer_id' => $trainer->id]);

            // ── Specializations ───────────────────────────────────────────────
            $picked = fake()->randomElements($specializationIds, fake()->numberBetween(2, 4));
            foreach ($picked as $specId) {
                TrainerSpecialization::firstOrCreate([
                    'trainer_id'        => $trainer->id,
                    'specialization_id' => $specId,
                ]);
            }

            // ── Packages with trainer-specific price ──────────────────────────
            foreach ($packages as $title => $package) {
                $base  = $this->basePrices[$title];
                $price = round($base * fake()->randomFloat(2, 0.80, 1.20), 2);

                TrainerPackage::create([
                    'trainer_id' => $trainer->id,
                    'package_id' => $package->id,
                    'price'      => $price,
                    'is_active'  => true,
                ]);
            }

            // NOTE: Availability is seeded separately in TrainerAvailabilitySeeder
        });
    }
}
