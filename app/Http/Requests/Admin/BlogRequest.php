<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BlogRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // Detect update case (encrypted ID)
        $blogId = $this->route('id') ? decrypt($this->route('id')) : null;

        return [
            'title' => [
                'required',
                'string',
                'max:255',
                Rule::unique('blogs', 'title')->ignore($blogId),
            ],

            'short_description' => ['nullable', 'string', 'max:500'],

            'content' => ['required', 'string'],

            'featured_image' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:5120', // 5MB
            ],


            'status' => ['required', 'in:0,1'],

            // SEO fields
            'meta_title'       => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
            'meta_keywords'    => ['nullable', 'array'],
            'meta_keywords.*'  => ['string', 'max:50'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Blog title is required.',
            'title.string'   => 'Blog title must be valid text.',
            'title.unique'   => 'This blog title already exists.',
            'title.max'      => 'Blog title may not be greater than 255 characters.',

            'content.required' => 'Blog content is required.',

            'featured_image.image' => 'Only image files are allowed.',
            'featured_image.mimes' => 'Allowed formats: JPG, JPEG, PNG, WEBP.',
            'featured_image.max'   => 'Featured image must be less than 5MB.',

            'published_at.date' => 'Publish date must be a valid date.',

            'status.required' => 'Status field is required.',
            'status.in'       => 'Invalid status selected.',

            'meta_title.max' => 'Meta title may not exceed 255 characters.',
            'meta_description.max' => 'Meta description may not exceed 500 characters.',
            'meta_keywords.array' => 'Meta keywords must be an array.',
            'meta_keywords.*.max' => 'Each meta keyword may not exceed 50 characters.',
        ];
    }
}
