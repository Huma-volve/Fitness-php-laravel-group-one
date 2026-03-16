<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            SpecializationSeeder::class,
            PackageSeeder::class,      // packages must exist before trainers
            TrainerSeeder::class,      // creates trainer_packages internally
            BookingSeeder::class,
        ]);
    }
}
