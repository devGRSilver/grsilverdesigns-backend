<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class VerifyOtpRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'token' => [
                'required',
                'uuid', // since you're passing UUID
            ],

            'otp' => [
                'required',
                'digits:6',
            ],

            'timezone' => [
                'nullable',
                'string',
                'timezone', // validates real timezone like Europe/London
            ],

            'device_type' => [
                'nullable',
                'string',
                Rule::in([1, 2, 3, 4]),
            ],

            'device_token' => [
                'nullable',
                'string',
                'max:255',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'token.required' => 'Token is required.',
            'token.uuid' => 'Invalid token format.',

            'otp.required' => 'OTP is required.',
            'otp.digits' => 'OTP must be exactly 6 digits.',

            'timezone.timezone' => 'Invalid timezone provided.',

            'device_type.in' => 'Invalid device type.',
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->filled('device_type')) {
            $this->merge([
                'device_type' => strtolower($this->device_type),
            ]);
        }
    }
}
