<?php

namespace App\Http\Requests\PaymentManagement;

use Illuminate\Foundation\Http\FormRequest;

class PaymentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'number' => 'required|numeric|digits:16',
            'exp_month' => 'required|integer|between:1,12',
            'exp_year' => 'required|integer|digits:4|after_or_equal:' . date('Y'),
            'cvc' => 'required|numeric|digits:3',
        ];
    }
}
