<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class ResendOtpRequest extends FormRequest
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
                'string',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'token.required' => 'Token is required.',
            'token.size' => 'Invalid token format.',
        ];
    }
}
