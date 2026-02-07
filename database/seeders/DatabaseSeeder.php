<?php

namespace Database\Seeders;

use Database\Seeders\Admin\AttributeAndValueSeeder;
use Database\Seeders\Admin\BannerSeeder;
use Database\Seeders\Admin\BlogSeeder;
use Database\Seeders\Admin\CategorySeeder;
use Database\Seeders\Admin\ContentSeeder;
use Database\Seeders\Admin\CouponSeeder;
use Database\Seeders\Admin\MetalSeeder;
use Database\Seeders\Admin\NewsletterSeeder;
use Database\Seeders\Admin\NotificationSeeder;
use Database\Seeders\Admin\OrderSeeder;
use Database\Seeders\Admin\ProductSeeder;
use Database\Seeders\Admin\PermissionSeeder;
use Database\Seeders\Admin\ReviewSeeder;
use Database\Seeders\Admin\SettingSeeder;
use Database\Seeders\Admin\UserSeeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            NewsLetterSeeder::class,
            ContentSeeder::class,
            MetalSeeder::class,
            CategorySeeder::class,
            AttributeAndValueSeeder::class,
            PermissionSeeder::class,
            UserSeeder::class,
            ProductSeeder::class,
            BlogSeeder::class,
            ReviewSeeder::class,
            SettingSeeder::class,
            OrderSeeder::class,
            NotificationSeeder::class,
            CouponSeeder::class,
            BannerSeeder::class,
        ]);
    }
}
