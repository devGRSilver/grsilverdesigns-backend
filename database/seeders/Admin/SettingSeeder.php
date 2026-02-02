<?php

namespace Database\Seeders\Admin;

use App\Models\Setting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Setting::updateOrCreate(
            ['id' => 1],   // single-row settings
            [
                // Site Info
                'site_name'        => 'My Admin Panel',
                'site_tagline'     => 'Best Ecommerce Platform',

                // Contact Info
                'email'            => 'admin@example.com',
                'phone'            => '+91 99999 88888',
                'address'          => 'New Delhi, India',

                // SEO
                'meta_title'       => 'My Ecommerce Website',
                'meta_description' => 'Buy best products online at best prices',
                'meta_keywords'    => 'ecommerce, online shopping, best deals',

                // Social Links
                'facebook'         => 'https://facebook.com/mywebsite',
                'instagram'        => 'https://instagram.com/mywebsite',
                'twitter'          => 'https://twitter.com/mywebsite',
                'linkedin'         => 'https://linkedin.com/company/mywebsite',
                'youtube'          => 'https://youtube.com/@mywebsite',

                // System
                'maintenance_mode' => false,
            ]
        );
    }
}
