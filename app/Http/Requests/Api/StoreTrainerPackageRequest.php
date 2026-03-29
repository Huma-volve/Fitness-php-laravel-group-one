<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;

class StoreTrainerPackageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'package_id' => [
                'required',
                'integer',
                'exists:packages,id',
            ],
            'price' => [
                'required',
                'numeric',
                'min:1',
                'max:99999.99',
            ],
            'is_active' => [
                'nullable',
                'boolean',
            ],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            if ($validator->errors()->any()) {
                return;
            }

            $trainer = auth()->user()->trainerProfile;

            if (! $trainer) {
                $validator->errors()->add('trainer', 'Trainer profile not found.');
                return;
            }

            $exists = \App\Models\TrainerPackage::where('trainer_id', $trainer->id)
                ->where('package_id', $this->package_id)
                ->exists();

            if ($exists) {
                $validator->errors()->add(
                    'package_id',
                    'You have already added this package. Use update to change the price.'
                );
            }
        });
    }

    public function messages(): array
    {
        return [
            'package_id.exists' => 'The selected package does not exist.',
            'price.min'         => 'Price must be at least $1.',
        ];
    }
}
