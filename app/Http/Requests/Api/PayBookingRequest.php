<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class PayBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'payment_method'    => ['required', 'in:paypal'],
        ];
    }

    public function messages(): array
    {
        return [
            'payment_method.in' => 'Payment method must be paypal or stripe.',
        ];
    }
}
