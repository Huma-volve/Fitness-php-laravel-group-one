<?php

namespace App\Http\Requests\Web;

use Illuminate\Foundation\Http\FormRequest;

class AdminBookingFilterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status'         => ['nullable', 'in:draft,pending,confirmed,canceled'],
            'payment_status' => ['nullable', 'in:pending,paid,failed,refunded'],
            'user_id'        => ['nullable', 'integer', 'exists:users,id'],
            'trainer_id'     => ['nullable', 'integer', 'exists:trainers,id'],
            'package_id'     => ['nullable', 'integer', 'exists:packages,id'],
            'date_from'      => ['nullable', 'date'],
            'date_to'        => ['nullable', 'date', 'after_or_equal:date_from'],
            'search'         => ['nullable', 'string', 'max:100'],
            'per_page'       => ['nullable', 'integer', 'min:5', 'max:100'],
            'sort_by'        => ['nullable', 'in:created_at,status,payment_status'],
            'sort_dir'       => ['nullable', 'in:asc,desc'],
        ];
    }
}
