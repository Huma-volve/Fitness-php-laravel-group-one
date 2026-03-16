<?php

namespace Database\Seeders;

use App\Models\FitnessProfile;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Fixed admin account
        User::factory()->admin()->create([
            'name'  => 'Admin',
            'email' => 'admin@app.com',
        ]);

        // Trainees with fitness profiles
        User::factory(30)->trainee()->create()->each(function (User $user) {
            FitnessProfile::factory()->create(['user_id' => $user->id]);
        });
    }
}
