<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get decrypted user ID for update, or null for create.
     */
    private function getDecryptedUserId()
    {
        $encrypted = $this->route('id');

        if (!$encrypted) {
            return null;
        }

        try {
            return decrypt($encrypted);
        } catch (\Exception $e) {
            return null;
        }
    }

    public function rules(): array
    {
        $userId = $this->getDecryptedUserId();
        $isEdit = !is_null($userId);

        $rules = [
            'name' => 'required|string|max:100',

            'email' => [
                'required',
                'email:rfc,dns',
                'max:100',
                Rule::unique('users', 'email')
                    ->ignore($userId)
                    ->whereNull('deleted_at'),
            ],

            'phone' => [
                'required',
                'string',
                'max:20',
                Rule::unique('users', 'phone')
                    ->ignore($userId)
                    ->whereNull('deleted_at'),
            ],

            'status' => 'sometimes|boolean',
        ];

        // Password rules: required only for create, optional for edit
        if (!$isEdit) {
            // Create operation - password required
            $rules['password'] = [
                'required',
                'string',
                'min:8',
                'confirmed',
            ];
            $rules['password_confirmation'] = 'required|string|min:8';
        } else {
            // Edit operation - password optional
            $rules['password'] = [
                'nullable',
                'string',
                'min:8',
                'confirmed',
            ];
            $rules['password_confirmation'] = 'nullable|string|min:8';
        }

        return $rules;
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

            'status.boolean' => 'Status must be true or false.',

            'password.required' => 'Password is required.',
            'password.min' => 'Password must be at least 8 characters.',
            'password.confirmed' => 'Password confirmation does not match.',

            'password_confirmation.required' => 'Password confirmation is required.',
            'password_confirmation.min' => 'Password confirmation must be at least 8 characters.',
        ];
    }
}
