<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class StoreCardInfoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'card_brand' => ['required', 'string', 'in:visa,mastercard,amex'],

            'card_last_four' => ['required', 'digits:4'],

            'card_exp_month' => ['required', 'integer', 'between:1,12'],

            'card_exp_year' => ['required', 'integer', 'min:' . date('Y')],

            'card_holder_name' => ['required', 'string', 'min:3', 'max:100', 'regex:/^[a-zA-Z\s]+$/'],
        ];
    }
}
