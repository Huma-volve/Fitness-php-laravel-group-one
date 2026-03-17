<?php

namespace Database\Seeders;

use App\Models\Trainer;
use App\Models\TrainerAvailabilities;
use App\Models\TrainerAvailabilityException;
use Illuminate\Database\Seeder;

class TrainerAvilabilitiesSeeder extends Seeder
{
    private array $weekdays    = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'];
    private array $weekendDays = ['saturday', 'sunday'];

    public function run(): void
    {
        Trainer::all()->each(function (Trainer $trainer) {

            // ── Clear any stale rows left from old date-based seeding ─────────
            TrainerAvailabilities::where('trainer_id', $trainer->id)->delete();
            TrainerAvailabilityException::where('trainer_id', $trainer->id)->delete();

            // ── Recurring weekly schedule ─────────────────────────────────────
            // Every trainer works Mon–Fri; ~40% also cover one weekend day
            $workDays = $this->weekdays;
            if (fake()->boolean(40)) {
                $workDays[] = fake()->randomElement($this->weekendDays);
            }

            foreach ($workDays as $day) {
                $startHour = fake()->numberBetween(7, 10);
                $endHour   = $startHour + fake()->numberBetween(6, 10);

                TrainerAvailabilities::create([
                    'trainer_id'  => $trainer->id,
                    'day_of_week' => $day,
                    'start_time'  => sprintf('%02d:00:00', $startHour),
                    'end_time'    => sprintf('%02d:00:00', min($endHour, 21)),
                    'is_active'   => true,
                ]);
            }

            // ── Exceptions: 1–3 upcoming day-offs per trainer ─────────────────
            $offCount  = fake()->numberBetween(1, 3);
            $usedDates = [];

            for ($i = 0; $i < $offCount; $i++) {
                $date = fake()->dateTimeBetween('now', '+60 days')->format('Y-m-d');

                if (in_array($date, $usedDates)) {
                    continue;
                }

                $usedDates[] = $date;

                // 80% chance it is a full day off, 20% special shorter hours
                $isSpecialHours = fake()->boolean(20);

                TrainerAvailabilityException::create([
                    'trainer_id'   => $trainer->id,
                    'date'         => $date,
                    'is_available' => $isSpecialHours,
                    'start_time'   => $isSpecialHours ? '08:00:00' : null,
                    'end_time'     => $isSpecialHours ? '12:00:00' : null,
                    'reason'       => $isSpecialHours
                        ? 'Special short hours'
                        : fake()->randomElement(['Holiday', 'Personal leave', 'Travel', 'Sick day']),
                ]);
            }
        });
    }
}
