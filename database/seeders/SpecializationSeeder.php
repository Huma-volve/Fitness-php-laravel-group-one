<?php

namespace Database\Seeders;

use App\Models\Specialization;
use Illuminate\Database\Seeder;

class SpecializationSeeder extends Seeder
{
    public function run(): void
    {
        $specializations = [
            'Weight Loss',
            'Muscle Gain',
            'Strength Training',
            'Cardio & Endurance',
            'Flexibility & Mobility',
            'HIIT Training',
            'CrossFit',
            'Yoga',
            'Pilates',
            'Nutrition Coaching',
            'Rehabilitation',
            'Sports Performance',
            'Senior Fitness',
            'Pre/Postnatal Fitness',
            'Bodybuilding',
        ];

        foreach ($specializations as $name) {
            Specialization::firstOrCreate(['name' => $name]);
        }
    }
}
