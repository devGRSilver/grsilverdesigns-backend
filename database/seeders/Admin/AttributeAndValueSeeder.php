<?php

namespace Database\Seeders\Admin;

use Illuminate\Database\Seeder;
use App\Models\Attribute;
use App\Models\AttributeValue;

class AttributeAndValueSeeder extends Seeder
{
    public function run(): void
    {
        $attributes = [

            // ================= METAL =================
            [
                'name'   => 'Metal Type',
                'slug'   => 'metal-type',
                'type'   => 'select',
                'status' => true,
                'values' => [
                    ['name' => 'Gold'],
                    ['name' => 'Silver'],
                    ['name' => 'Platinum'],
                    ['name' => 'Rose Gold'],
                ],
            ],

            // ================= PURITY =================
            [
                'name'   => 'Metal Purity',
                'slug'   => 'metal-purity',
                'type'   => 'select',
                'status' => true,
                'values' => [
                    ['name' => '14K'],
                    ['name' => '18K'],
                    ['name' => '22K'],
                    ['name' => '24K'],
                ],
            ],

            // ================= METAL COLOR =================
            [
                'name'   => 'Metal Color',
                'slug'   => 'metal-color',
                'type'   => 'color',
                'status' => true,
                'values' => [
                    ['name' => 'Yellow Gold'],
                    ['name' => 'White Gold'],
                    ['name' => 'Rose Gold'],
                ],
            ],

            // ================= GEMSTONE =================
            [
                'name'   => 'Gemstone Type',
                'slug'   => 'gemstone-type',
                'type'   => 'select',
                'status' => true,
                'values' => [
                    ['name' => 'Diamond'],
                    ['name' => 'Ruby'],
                    ['name' => 'Emerald'],
                    ['name' => 'Sapphire'],
                ],
            ],

            // ================= DIAMOND CLARITY =================
            [
                'name'   => 'Diamond Clarity',
                'slug'   => 'diamond-clarity',
                'type'   => 'select',
                'status' => true,
                'values' => [
                    ['name' => 'FL'],
                    ['name' => 'VVS1'],
                    ['name' => 'VVS2'],
                    ['name' => 'VS1'],
                    ['name' => 'VS2'],
                    ['name' => 'SI1'],
                ],
            ],

            // ================= DIAMOND COLOR =================
            [
                'name'   => 'Diamond Color',
                'slug'   => 'diamond-color',
                'type'   => 'select',
                'status' => true,
                'values' => [
                    ['name' => 'D'],
                    ['name' => 'E'],
                    ['name' => 'F'],
                    ['name' => 'G'],
                    ['name' => 'H'],
                ],
            ],

            // ================= RING SIZE =================
            [
                'name'   => 'Ring Size',
                'slug'   => 'ring-size',
                'type'   => 'select',
                'status' => true,
                'values' => [
                    ['name' => 'US-6'],
                    ['name' => 'US-7'],
                    ['name' => 'US-8'],
                    ['name' => 'US-9'],
                    ['name' => 'US-10'],
                    ['name' => 'US-11'],
                    ['name' => 'US-12'],
                    ['name' => '16.6x13mm'],
                    ['name' => '6.6x13mm'],
                    ['name' => '5x8mm'],
                ],
            ],

            // ================= BAND WIDTH (MM) =================
            [
                'name'   => 'Band Width',
                'slug'   => 'band-width',
                'type'   => 'select',
                'status' => true,
                'values' => [
                    ['name' => '2 MM'],
                    ['name' => '3 MM'],
                    ['name' => '4 MM'],
                    ['name' => '5 MM'],
                    ['name' => '6 MM'],
                ],
            ],



            // ================= CERTIFICATION =================
            [
                'name'   => 'Certification',
                'slug'   => 'certification',
                'type'   => 'select',
                'status' => true,
                'values' => [
                    ['name' => 'BIS Hallmarked'],
                    ['name' => 'IGI'],
                    ['name' => 'GIA'],
                    ['name' => 'None'],
                ],
            ],

            // ================= OCCASION =================
            [
                'name'   => 'Occasion',
                'slug'   => 'occasion',
                'type'   => 'select',
                'status' => true,
                'values' => [
                    ['name' => 'Daily Wear'],
                    ['name' => 'Wedding'],
                    ['name' => 'Engagement'],
                    ['name' => 'Party'],
                    ['name' => 'Festive'],
                ],
            ],

            // ================= GENDER =================
            [
                'name'   => 'Gender',
                'slug'   => 'gender',
                'type'   => 'select',
                'status' => true,
                'values' => [
                    ['name' => 'Men'],
                    ['name' => 'Women'],
                    ['name' => 'Unisex'],
                ],
            ],
        ];

        foreach ($attributes as $item) {

            $attribute = Attribute::firstOrCreate(
                ['slug' => $item['slug']],
                [
                    'name'   => $item['name'],
                    'type'   => $item['type'],
                    'status' => $item['status'],
                ]
            );

            foreach ($item['values'] as $order => $value) {
                AttributeValue::firstOrCreate(
                    [
                        'attribute_id' => $attribute->id,
                        'value'        => $value['name'],
                    ],
                    [
                        'sort_order' => $order,
                    ]
                );
            }
        }
    }
}
