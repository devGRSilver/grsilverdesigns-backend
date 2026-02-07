<?php

namespace Database\Seeders\Admin;

use App\Models\Banner;
use Illuminate\Database\Seeder;

class BannerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Banner::truncate();

        /**
         * -------------------------
         * INSERT 5 BANNERS
         * -------------------------
         */
        for ($i = 1; $i <= 5; $i++) {
            Banner::create([
                'title'       => "Home Banner {$i}",
                'type'        => 'banner',
                'group_key'   => 'home-top',
                'image_url'   => 'http://127.0.0.1:8000/default_images/no_image.png',
                'link_url'    => '/shop',
                'description' => "This is description for Home Banner {$i}",
                'button_text' => 'Shop Now',
                'status'      => true,
            ]);
        }

        /**
         * -------------------------
         * INSERT 5 SLIDERS
         * -------------------------
         */
        for ($i = 1; $i <= 5; $i++) {
            Banner::create([
                'title'       => "Home Slider {$i}",
                'type'        => 'slider',
                'group_key'   => 'home-slider',
                'image_url'   => 'http://127.0.0.1:8000/default_images/no_image.png',
                'link_url'    => '/products',
                'description' => "This is description for Home Slider {$i}",
                'button_text' => 'Explore',
                'status'      => true,
            ]);
        }
    }
}
