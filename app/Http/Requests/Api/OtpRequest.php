<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class OtpRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'phonecode' => [
                'required',
                'string',
                'regex:/^[1-9]\d{0,3}$/', // 1–4 digits, cannot start with 0
            ],
            'phone' => [
                'required',
                'string',
                'regex:/^[0-9]{6,15}$/', // 6–15 digits only
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'phonecode.required' => 'Phone code is required.',
            'phonecode.regex' => 'Invalid phone code format. Example: 91',
            'phone.required' => 'Phone number is required.',
            'phone.regex' => 'Invalid phone number. It must contain 6–15 digits only.',
        ];
    }

    protected function prepareForValidation(): void
    {
        // Normalize phone - remove all non-digit characters
        if ($this->filled('phone')) {
            $this->merge([
                'phone' => preg_replace('/\D/', '', trim($this->phone)),
            ]);
        }

        // Normalize phonecode - remove + and any non-digit characters
        if ($this->filled('phonecode')) {
            $phonecode = preg_replace('/\D/', '', trim($this->phonecode));

            $this->merge([
                'phonecode' => $phonecode,
            ]);
        }
    }
}
