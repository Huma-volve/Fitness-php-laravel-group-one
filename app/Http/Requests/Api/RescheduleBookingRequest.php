<?php

namespace App\Http\Requests\Api;

use App\Models\Booking;
use App\Services\TrainerAvailabilityService;
use Carbon\Carbon;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class RescheduleBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'session_start' => ['required', 'date', 'after:now'],
        ];
    }

    /**
     * After base validation passes, check trainer availability.
     * Excludes the current session from overlap detection.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            if ($validator->errors()->any()) {
                return;
            }

            /** @var Booking $booking */
            $booking = $this->route('booking');

            // Find the scheduled session being rescheduled
            $currentSession = $booking->sessions()
                ->where('session_status', 'scheduled')
                ->orderBy('session_start')
                ->first();

            if (! $currentSession) {
                $validator->errors()->add('session_start', 'No scheduled session found to reschedule.');
                return;
            }

            $sessionStart = Carbon::parse($this->session_start);
            $sessionEnd   = $sessionStart->copy()->addHour();

            $result = app(TrainerAvailabilityService::class)->check(
                $booking->trainer,
                $sessionStart,
                $sessionEnd,
                $currentSession->id  // Exclude current session from overlap check
            );

            if (! $result['available']) {
                $validator->errors()->add('session_start', $result['reason']);
            }
        });
    }

    public function messages(): array
    {
        return [
            'session_start.after' => 'The new session must be scheduled in the future.',
        ];
    }
}
