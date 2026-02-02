<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class SettingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // allow admin to update settings
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            // Site Info
            'site_name'        => 'nullable|string|max:255',
            'site_tagline'     => 'nullable|string|max:255',

            // Contact
            'email'            => 'nullable|email|max:255',
            'phone'            => 'nullable|string|max:20',
            'address'          => 'nullable|string',

            // Images
            'site_logo'        => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'site_favicon'     => 'nullable|image|mimes:jpg,jpeg,png,ico,webp|max:2048',

            // SEO
            'meta_title'       => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords'    => 'nullable|string|max:500',

            // Social Links
            'facebook'         => 'nullable|url|max:255',
            'instagram'        => 'nullable|url|max:255',
            'twitter'          => 'nullable|url|max:255',
            'linkedin'         => 'nullable|url|max:255',
            'youtube'          => 'nullable|url|max:255',

            // System
            'maintenance_mode' => 'nullable|boolean',
        ];
    }
}
