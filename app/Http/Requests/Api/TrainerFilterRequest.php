<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class TrainerFilterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'location'          => ['nullable', 'string', 'max:100'],
            'specialization_id' => ['nullable', 'integer', 'exists:specializations,id'],
            'min_price'         => ['nullable', 'numeric', 'min:0'],
            'max_price'         => ['nullable', 'numeric', 'min:0'],
            'min_rating'        => ['nullable', 'numeric', 'min:0', 'max:5'],
            'sort_by'           => ['nullable', 'in:rating,price,experience_years'],
            'sort_dir'          => ['nullable', 'in:asc,desc'],
            'per_page'          => ['nullable', 'integer', 'min:1', 'max:50'],
        ];
    }
}