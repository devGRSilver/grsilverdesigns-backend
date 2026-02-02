<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class MetalStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
            ],
            'price_per_gram' => [
                'required',
                'numeric',
                'min:0',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Metal name is required.',
            'name.string' => 'Metal name must be a valid string.',
            'price_per_gram.required' => 'Price per gram is required.',
            'price_per_gram.numeric' => 'Price must be a valid number.',
            'price_per_gram.min' => 'Price cannot be negative.',

        ];
    }
}
