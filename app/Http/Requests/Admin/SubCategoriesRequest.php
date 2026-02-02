<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SubCategoriesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // Detect update case
        $categoryId = $this->route('id') ? decrypt($this->route('id')) : null;
        return [
            'category_id'       => ['required', 'exists:categories,id'],
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('categories', 'name')->ignore($categoryId),
            ],

            'meta_title'       => ['nullable', 'string', 'max:255'],
            'meta_keywords'    => ['nullable', 'array'],
            'meta_keywords.*'  => ['string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
            'image'         => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'banner_image'  => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'status'        => ['required', 'in:0,1'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Category name is required.',
            'name.string'   => 'Category name must be valid text.',
            'name.unique'   => 'This category name already exists.',

            'category_id.exists' => 'Selected parent category is invalid.',


            'image.image' => 'Only image files are allowed.',
            'image.mimes' => 'Allowed formats: JPG, JPEG, PNG, WEBP.',
            'image.max'   => 'Image must be less than 5MB.',

            'banner_image.image' => 'Only image files are allowed.',
            'banner_image.mimes' => 'Allowed formats: JPG, JPEG, PNG, WEBP.',
            'banner_image.max'   => 'Banner image must be less than 5MB.',

            'status.required' => 'Status field is required.',
        ];
    }
}
