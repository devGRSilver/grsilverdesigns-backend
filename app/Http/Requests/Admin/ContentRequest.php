<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ContentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // Detect update case (encrypted ID)
        $id = $this->route('id') ? decrypt($this->route('id')) : null;

        return [
            'title' => [
                'required',
                'string',
                'max:255',
            ],

            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('contents', 'slug')->ignore($id),
            ],

            'type' => [
                'nullable',
                'string',
                'max:100',
            ],

            'description' => [
                'nullable',
                'string',
            ],

            'image' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:5120', // 5MB
            ],

            'meta_title' => [
                'nullable',
                'string',
                'max:255',
            ],

            'meta_keywords' => [
                'nullable',
                'array',
            ],

            'meta_keywords.*' => [
                'string',
                'max:255',
            ],

            'meta_description' => [
                'nullable',
                'string',
                'max:500',
            ],

            'status' => [
                'required',
                Rule::in([0, 1]),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Title is required.',
            'title.string'   => 'Title must be valid text.',
            'title.max'      => 'Title may not exceed 255 characters.',

            'slug.unique'    => 'This slug already exists.',

            'image.image' => 'Only image files are allowed.',
            'image.mimes' => 'Allowed formats: JPG, JPEG, PNG, WEBP.',
            'image.max'   => 'Image must be less than 5MB.',

            'meta_keywords.array' => 'Meta keywords must be an array.',
            'meta_keywords.*.string' => 'Each keyword must be text.',

            'meta_description.max' => 'Meta description must be under 500 characters.',

            'status.required' => 'Status field is required.',
            'status.in'       => 'Invalid status value.',
        ];
    }
}
