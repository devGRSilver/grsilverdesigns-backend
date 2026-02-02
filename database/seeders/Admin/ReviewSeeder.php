<?php

namespace Database\Seeders\Admin;

use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Seeder;

class ReviewSeeder extends Seeder
{
    public function run(): void
    {
        Review::truncate();

        $comments = [
            'Excellent product quality!',
            'Very satisfied with the service.',
            'Worth the price.',
            'Good experience overall.',
            'Fast delivery and nice packaging.',
            'Product matches the description.',
            'Highly recommended!',
            'Customer support was helpful.',
            'Will buy again.',
            'Amazing quality, loved it!',
        ];

        // Get 10 unique users starting from ID 4
        $users = User::where('id', '>=', 4)
            ->orderBy('id')
            ->take(10)
            ->get();

        foreach ($users as $index => $user) {
            Review::create([
                'user_id'    => $user->id,
                'rating'     => rand(3, 5),
                'comment'    => $comments[$index],
                'status'     => true,
                'ip_address' => '127.0.0.' . ($index + 1),
                'user_agent' => 'Seeder',
            ]);
        }
    }
}
