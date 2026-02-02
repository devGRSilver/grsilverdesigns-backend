<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UserPasswordRequest extends FormRequest
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
            'password' => [
                'required',
                'string',
                'min:6',
                'max:30',
                'confirmed', // requires password_confirmation field
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/', // at least 1 uppercase, 1 lowercase, 1 number
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'password.required' => 'Password is required.',
            'password.min' => 'Password must be at least 8 characters.',
            'password.max' => 'Password cannot exceed 30 characters.',
            'password.confirmed' => 'Passwords do not match.',
            'password.regex' => 'Password must contain at least one uppercase letter, one lowercase letter, and one number.',
        ];
    }
}
