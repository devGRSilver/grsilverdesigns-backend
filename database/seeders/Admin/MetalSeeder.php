<?php

namespace Database\Seeders\Admin;

use App\Models\Metal;
use Illuminate\Database\Seeder;

class MetalSeeder extends Seeder
{
    public function run(): void
    {
        $metals = [
            [
                'name' => 'GOLD',
                'price_per_gram' => 134,
                'currency' => 'USD',
            ],
            [
                'name' => 'SILVER',
                'price_per_gram' => 1.90,
                'currency' => 'USD',
            ],

            [
                'name' => 'OTHER',
                'price_per_gram' => 0,
                'currency' => 'USD',
            ],

        ];

        foreach ($metals as $metal) {
            Metal::updateOrCreate(
                ['name' => $metal['name']],
                [
                    'price_per_gram' => $metal['price_per_gram'],
                    'currency' => $metal['currency'],
                ]
            );
        }
    }
}
