<?php

namespace App\Services;

use App\Models\Trainer;
use App\Models\TrainerAvailabilities;
use App\Models\TrainerAvailabilityException;
use App\Models\TraineeSession;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class TrainerAvailabilityService
{
    /**
     * Resolve the effective working window for a trainer on a specific date.
     *
     * Priority:
     *  1. Exception (day off or special hours) overrides the recurring schedule.
     *  2. Recurring weekly schedule is used if no exception exists.
     *
     * Returns null if the trainer is not available at all on this date.
     *
     * @return array{start: Carbon, end: Carbon}|null
     */
    public function resolveWindow(Trainer $trainer, Carbon $date): ?array
    {
        $dateStr = $date->toDateString();

        // ── 1. Check for a one-off exception on this exact date ──────────────────
        $exception = TrainerAvailabilityException::where('trainer_id', $trainer->id)
            ->whereDate('date', $dateStr)
            ->first();

        if ($exception) {
            // Explicitly marked as unavailable (day off)
            if (! $exception->is_available) {
                return null;
            }

            // Special hours override
            return [
                'start' => Carbon::parse($dateStr . ' ' . $exception->start_time),
                'end'   => Carbon::parse($dateStr . ' ' . $exception->end_time),
            ];
        }

        // ── 2. Fall back to recurring weekly schedule ─────────────────────────────
        $dayOfWeek = strtolower($date->format('l')); // e.g. "monday"

        $schedule = TrainerAvailabilities::where('trainer_id', $trainer->id)
            ->where('day_of_week', $dayOfWeek)
            ->where('is_active', true)
            ->first();

        if (! $schedule) {
            return null; // Trainer does not work on this day of the week
        }

        return [
            'start' => Carbon::parse($dateStr . ' ' . $schedule->start_time),
            'end'   => Carbon::parse($dateStr . ' ' . $schedule->end_time),
        ];
    }

    /**
     * Check if a trainer is available for a specific datetime slot.
     *
     * Steps:
     *  1. Resolve the effective working window for that date.
     *  2. Verify the requested slot fits inside the working window.
     *  3. Verify no existing confirmed session overlaps.
     *
     * @param  int|null $excludeSessionId  Ignore this session (used on reschedule)
     * @return array{available: bool, reason: string|null}
     */
    public function check(
        Trainer $trainer,
        Carbon  $sessionStart,
        Carbon  $sessionEnd,
        ?int    $excludeSessionId = null
    ): array {
        // ── Step 1: Resolve working window ────────────────────────────────────────
        $window = $this->resolveWindow($trainer, $sessionStart->copy()->startOfDay());

        if (! $window) {
            $dayName = $sessionStart->format('l');
            return [
                'available' => false,
                'reason'    => "The trainer is not available on {$dayName}s.",
            ];
        }

        // ── Step 2: Slot must fall within the working window ──────────────────────
        if ($sessionStart->lt($window['start']) || $sessionEnd->gt($window['end'])) {
            return [
                'available' => false,
                'reason'    => 'The selected time is outside the trainer\'s working hours '
                    . '(' . $window['start']->format('H:i') . ' – ' . $window['end']->format('H:i') . ').',
            ];
        }

        // ── Step 3: No overlapping booked sessions ────────────────────────────────
        $conflict = TraineeSession::where('trainer_id', $trainer->id)
            ->where('session_status', 'scheduled')
            ->when($excludeSessionId, fn ($q) => $q->where('id', '!=', $excludeSessionId))
            ->where('session_start', '<', $sessionEnd)
            ->where('session_end',   '>',  $sessionStart)
            ->exists();

        if ($conflict) {
            return [
                'available' => false,
                'reason'    => 'The trainer already has a booking during this time slot.',
            ];
        }

        return ['available' => true, 'reason' => null];
    }

    /**
     * Get all open time slots for a trainer on a given date.
     * Splits the working window into equal chunks, excluding already-booked slots.
     *
     * @return Collection<array{start: string, end: string}>
     */
    public function availableSlots(
        Trainer $trainer,
        string  $date,
        int     $sessionDurationMinutes = 60
    ): Collection {
        $window = $this->resolveWindow($trainer, Carbon::parse($date));

        if (! $window) {
            return collect();
        }

        // Fetch booked sessions on this date for the trainer
        $booked = TraineeSession::where('trainer_id', $trainer->id)
            ->where('session_status', 'scheduled')
            ->whereDate('session_start', $date)
            ->get(['session_start', 'session_end']);

        $slots  = collect();
        $cursor = $window['start']->copy();

        while ($cursor->copy()->addMinutes($sessionDurationMinutes)->lte($window['end'])) {
            $slotStart = $cursor->copy();
            $slotEnd   = $cursor->copy()->addMinutes($sessionDurationMinutes);

            $isBooked = $booked->contains(function ($session) use ($slotStart, $slotEnd) {
                $start = Carbon::parse($session->session_start);
                $end   = Carbon::parse($session->session_end);
                return $start->lt($slotEnd) && $end->gt($slotStart);
            });

            if (! $isBooked) {
                $slots->push([
                    'start' => $slotStart->format('H:i'),
                    'end'   => $slotEnd->format('H:i'),
                ]);
            }

            $cursor->addMinutes($sessionDurationMinutes);
        }

        return $slots;
    }

    /**
     * Get a trainer's full weekly schedule (recurring + upcoming exceptions).
     *
     * @return array{schedule: Collection, exceptions: Collection}
     */
    public function weeklyOverview(Trainer $trainer): array
    {
        $schedule = TrainerAvailabilities::where('trainer_id', $trainer->id)
            ->where('is_active', true)
            ->orderByRaw("FIELD(day_of_week, 'monday','tuesday','wednesday','thursday','friday','saturday','sunday')")
            ->get(['day_of_week', 'start_time', 'end_time']);

        $exceptions = TrainerAvailabilityException::where('trainer_id', $trainer->id)
            ->where('date', '>=', now()->toDateString())
            ->orderBy('date')
            ->get(['date', 'is_available', 'start_time', 'end_time', 'reason']);

        return compact('schedule', 'exceptions');
    }
}
