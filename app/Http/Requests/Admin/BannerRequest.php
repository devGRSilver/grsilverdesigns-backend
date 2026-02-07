<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BannerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $isCreate = $this->isMethod('post'); // POST = add, PUT/PATCH = edit

        return [
            /* ---------- BASIC INFO ---------- */
            'title' => [
                'required',
                'string',
                'max:255',
            ],

            'type' => [
                'required',
                'string',
                Rule::in(array_keys(config('banner.types', []))),
            ],

            'group_key' => [
                'required',
                'string',
                Rule::in(array_keys(config('banner.group_keys', []))),
            ],

            /* ---------- MEDIA ---------- */
            'image_url' => array_filter([
                $isCreate ? 'required' : 'nullable', // 🔥 magic line
                'image',
                'mimes:jpeg,png,jpg,gif,webp',
                'max:5120',
                'dimensions:min_width=600,min_height=300,max_width=2000,max_height=1000',
            ]),

            /* ---------- CONTENT ---------- */
            'description' => [
                'nullable',
                'string',
                'max:1000',
            ],

            /* ---------- CTA ---------- */
            'button_text' => [
                'nullable',
                'string',
                'max:50',
            ],

            'link_url' => [
                'nullable',
                'url',
                'max:500',
            ],

            /* ---------- STATUS ---------- */
            'status' => [
                'nullable',
                'boolean',
            ],
        ];
    }
}
