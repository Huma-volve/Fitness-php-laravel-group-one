<?php

namespace Database\Seeders;

use App\Models\Package;
use Illuminate\Database\Seeder;

class PackageSeeder extends Seeder
{
    public function run(): void
    {
        $packages = [
            [
                'title'             => 'Single',
                'description'       => 'A single session with any available trainer. Great for trying out the platform.',
                'sessions'          => 1,
                'duration_days'     => 60,
                'progress_tracking' => false,
                'nutrition_plan'    => false,
                'priority_booking'  => false,
                'full_access'       => false,
            ],
            [
                'title'             => 'Monthly',
                'description'       => 'Eight sessions with a dedicated trainer, including nutrition plan and progress tracking.',
                'sessions'          => 15,
                'duration_days'     => 60,
                'progress_tracking' => true,
                'nutrition_plan'    => true,
                'priority_booking'  => false,
                'full_access'       => false,
            ],
            [
                'title'             => 'Premium',
                'description'       => 'Unlimited sessions with a dedicated trainer, full platform access, priority booking, and everything included.',
                'sessions'          => 50,
                'duration_days'     => 60,
                'progress_tracking' => true,
                'nutrition_plan'    => true,
                'priority_booking'  => true,
                'full_access'       => true,
            ],
        ];

        foreach ($packages as $package) {
            Package::firstOrCreate(['title' => $package['title']], $package);
        }
    }
}
