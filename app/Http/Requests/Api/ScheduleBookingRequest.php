<?php

namespace App\Http\Requests\Api;

use App\Models\TrainerPackage;
use App\Services\TrainerAvailabilityService;
use Carbon\Carbon;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class ScheduleBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'trainer_package_id'      => ['required', 'integer', 'exists:trainer_packages,id'],

            'sessions'                => ['required', 'array', 'min:1'],
            'sessions.*'              => ['required', 'date', 'after:now', 'distinct'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            if ($validator->errors()->any()) {
                return;
            }

            $trainerPackage = TrainerPackage::with(['trainer', 'package'])->find($this->trainer_package_id);

            if (! $trainerPackage || ! $trainerPackage->is_active) {
                $validator->errors()->add('trainer_package_id', 'This package is not available.');
                return;
            }

            $packageSessions = $trainerPackage->package->sessions;
            $requestedCount  = count($this->sessions);

            if ($packageSessions !== 999 && $requestedCount > $packageSessions) {
                $validator->errors()->add(
                    'sessions',
                    "This package includes {$packageSessions} session(s). You submitted {$requestedCount}."
                );
                return;
            }

            $service = app(TrainerAvailabilityService::class);

            foreach ($this->sessions as $index => $sessionStart) {
                $start = Carbon::parse($sessionStart);
                $end   = $start->copy()->addHour();

                $result = $service->check($trainerPackage->trainer, $start, $end);

                if (! $result['available']) {
                    $validator->errors()->add(
                        "sessions.{$index}",
                        "Session " . ($index + 1) . ": {$result['reason']}"
                    );
                }
            }
        });
    }

    public function messages(): array
    {
        return [
            'sessions.required'   => 'Please select at least one session time.',
            'sessions.*.after'    => 'All sessions must be scheduled in the future.',
            'sessions.*.distinct' => 'Duplicate session times are not allowed.',
        ];
    }
}
