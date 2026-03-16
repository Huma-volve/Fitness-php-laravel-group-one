<?php

namespace Database\Seeders;

use App\Models\Package;
use App\Models\Specialization;
use App\Models\Trainer;
use App\Models\TrainerAvailabilities;
use App\Models\TrainerCertification;
use App\Models\TrainerPackage;
use App\Models\TrainerSpecialization;
use App\Models\User;
use Illuminate\Database\Seeder;

class TrainerSeeder extends Seeder
{
    /**
     * Base prices per package type that each trainer adjusts slightly.
     */
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

            // Create trainer profile
            $trainer = Trainer::factory()->create(['user_id' => $user->id]);

            // 1–3 certifications per trainer
            TrainerCertification::factory()
                ->count(fake()->numberBetween(1, 3))
                ->create(['trainer_id' => $trainer->id]);

            // 2–4 specializations per trainer (no duplicates)
            $picked = fake()->randomElements($specializationIds, fake()->numberBetween(2, 4));
            foreach ($picked as $specId) {
                TrainerSpecialization::firstOrCreate([
                    'trainer_id'        => $trainer->id,
                    'specialization_id' => $specId,
                ]);
            }

            // Each trainer sets their own price for every package
            foreach ($packages as $title => $package) {
                $base  = $this->basePrices[$title];
                // Trainer price varies ±20% around the base price
                $price = round($base * fake()->randomFloat(2, 0.80, 1.20), 2);

                TrainerPackage::create([
                    'trainer_id' => $trainer->id,
                    'package_id' => $package->id,
                    'price'      => $price,
                    'is_active'  => true,
                ]);
            }

            // 5–10 availability slots per trainer
            TrainerAvailabilities::factory()
                ->count(fake()->numberBetween(5, 10))
                ->create(['trainer_id' => $trainer->id]);
        });
    }
}
