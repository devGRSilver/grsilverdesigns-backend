<?php

namespace Database\Seeders\Admin;

use App\Constants\Constant;
use Illuminate\Database\Seeder;
use App\Models\Newsletter;

class NewsLetterSeeder extends Seeder
{
    public function run(): void
    {
        Newsletter::insert([
            [
                'email' => 'john@example.com',
                'name' => 'John Doe',
                'status' => Constant::ACTIVE,
                'subscribed_at' => now(),
                'unsubscribed_at' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'email' => 'jane@example.com',
                'name' => 'Jane Smith',
                'status' => Constant::ACTIVE,
                'subscribed_at' => now(),
                'unsubscribed_at' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'email' => 'mark@example.com',
                'name' => 'Mark Lee',
                'status' => Constant::ACTIVE,
                'subscribed_at' => now()->subDays(10),
                'unsubscribed_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
