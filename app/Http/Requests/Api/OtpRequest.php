<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class OtpRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'phonecode' => [
                'required',
                'string',
                'regex:/^[1-9]\d{0,3}$/',
            ],

            'type' => [
                'required',
                'in:login,forget_password',
            ],

            'phone' => [
                'required',
                'string',
                'regex:/^[0-9]{6,15}$/',
            ],
        ];

        if ($this->type === 'forget_password') {
            $rules['phone'][] = Rule::exists('users', 'phone');
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'phonecode.required' => 'Phone code is required.',
            'phonecode.regex' => 'Invalid phone code format. Example: 91',

            'type.required' => 'Type is required.',
            'type.in' => 'Type must be login or forget_password.',

            'phone.required' => 'Phone number is required.',
            'phone.regex' => 'Invalid phone number. It must contain 6–15 digits only.',
            'phone.exists' => 'Phone number does not exist in our records.',
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->filled('phone')) {
            $this->merge([
                'phone' => preg_replace('/\D/', '', trim($this->phone)),
            ]);
        }

        if ($this->filled('phonecode')) {
            $this->merge([
                'phonecode' => preg_replace('/\D/', '', trim($this->phonecode)),
            ]);
        }
    }
}
