<?php

namespace Database\Factories;

use App\Models\Trainer;
use Illuminate\Database\Eloquent\Factories\Factory;

class TrainerCertificationFactory extends Factory
{
    public function definition(): array
    {
        $certifications = [
            'Certified Personal Trainer (CPT)',
            'Certified Strength and Conditioning Specialist (CSCS)',
            'Certified Nutrition Coach',
            'Functional Movement Screen (FMS)',
            'CrossFit Level 1 Trainer',
            'NASM Performance Enhancement Specialist',
            'ACE Health Coach',
            'Yoga Alliance RYT-200',
        ];

        $organizations = [
            'NASM', 'ACE', 'NSCA', 'ISSA', 'ACSM',
            'CrossFit LLC', 'Yoga Alliance', 'NCCPT',
        ];

        return [
            'trainer_id'       => Trainer::factory(),
            'certificate_name' => fake()->randomElement($certifications),
            'organization'     => fake()->randomElement($organizations),
            'year'             => fake()->numberBetween(2010, 2024),
            'path'             => 'certifications/' . fake()->uuid() . '.pdf',
        ];
    }
}
