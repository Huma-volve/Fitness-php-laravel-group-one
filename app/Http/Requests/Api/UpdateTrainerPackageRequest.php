<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTrainerPackageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'price' => [
                'required',
                'numeric',
                'min:1',
                'max:99999.99',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'price.required' => 'A price is required.',
            'price.min'      => 'Price must be at least $1.',
        ];
    }
}
