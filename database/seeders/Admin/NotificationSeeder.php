<?php

namespace Database\Seeders\Admin;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class NotificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $notifications = [];

        for ($i = 1; $i <= 10; $i++) {
            $notifications[] = [
                'user_id'    => 1,
                'title'      => "Notification {$i}",
                'message'    => "This is notification message {$i}.",
                'url'      =>  route('users.index'),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];
        }
        DB::table('notifications')->insert($notifications);
    }
}
