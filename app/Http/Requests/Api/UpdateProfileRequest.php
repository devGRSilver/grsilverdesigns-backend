<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => [
                'sometimes',
                'required',
                'string',
                'max:100',
                'regex:/^[a-zA-Z\s]+$/',
            ],
            'timezone' => [
                'sometimes',
                'required',
                'string',
                'timezone:all',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Name is required.',
            'name.max' => 'Name cannot exceed 100 characters.',
            'name.regex' => 'Name can only contain letters and spaces.',
            'timezone.required' => 'Timezone is required.',
            'timezone.timezone' => 'Invalid timezone.',
        ];
    }
}
