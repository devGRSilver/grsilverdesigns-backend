<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StaffStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get decrypted user ID for update, or null for create.
     */
    private function getDecryptedUserId(): ?int
    {
        $encryptedId = $this->route('id');

        if (!$encryptedId) {
            return null;
        }

        try {
            return decrypt($encryptedId);
        } catch (\Exception $e) {
            return null;
        }
    }

    public function rules(): array
    {
        $userId = $this->getDecryptedUserId();

        return [
            'name' => 'required|string|max:100',

            'email' => [
                'required',
                'email:rfc,dns',
                'max:100',
                Rule::unique('users', 'email')->ignore($userId),
            ],

            'phone' => [
                'required',
                'string',
                'max:20',
                Rule::unique('users', 'phone')->ignore($userId),
            ],

            'status' => 'nullable|boolean',
            'role' => 'required',
            // Password required on create, optional on update
            'password' => [
                $userId ? 'nullable' : 'required',
                'string',
                'min:6',
                'max:30',
                'confirmed',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Name is required.',
            'name.max' => 'Name cannot exceed 100 characters.',

            'email.required' => 'Email is required.',
            'email.email' => 'Email must be a valid email address.',
            'email.max' => 'Email cannot exceed 100 characters.',
            'email.unique' => 'This email is already in use.',

            'phone.required' => 'Phone number is required.',
            'phone.max' => 'Phone number cannot exceed 20 characters.',
            'phone.unique' => 'This phone number is already in use.',

            'password.required' => 'Password is required.',
            'password.confirmed' => 'Password confirmation does not match.',
            'password.regex' => 'Password must contain at least one uppercase letter, one lowercase letter, and one number.',

            'status.boolean' => 'Status must be true or false.',
        ];
    }
}
