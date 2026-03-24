<?php

namespace App\Http\Requests\Api;

use App\Models\TrainerPackage;
use App\Services\TrainerAvailabilityService;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;

class StoreBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'trainer_package_id' => ['required', 'integer', 'exists:trainer_packages,id'],
            'session_start'      => ['required', 'date', 'after:now'],
        ];
    }

    /**
     * After base validation passes, check trainer availability.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            if ($validator->errors()->any()) {
                return; // Don't bother if basic rules already failed
            }

            $trainerPackage = TrainerPackage::find($this->trainer_package_id);

            if (! $trainerPackage || ! $trainerPackage->is_active) {
                $validator->errors()->add('trainer_package_id', 'This package is not available.');
                return;
            }

            $sessionStart = Carbon::parse($this->session_start);
            $sessionEnd   = $sessionStart->copy()->addHour();

            $result = app(TrainerAvailabilityService::class)->check(
                $trainerPackage->trainer,
                $sessionStart,
                $sessionEnd
            );

            if (! $result['available']) {
                $validator->errors()->add('session_start', $result['reason']);
            }
        });
    }

    public function messages(): array
    {
        return [
            'trainer_package_id.exists' => 'The selected package does not exist.',
            'session_start.after'       => 'The session must be scheduled in the future.',
        ];
    }
}
