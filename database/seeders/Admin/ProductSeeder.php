<?php

namespace Database\Seeders\Admin;

use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($i = 1; $i <= 10; $i++) {

            $product = Product::create([
                'category_id'        => 1,
                'product_type'       => 'with_variant',
                'name'               => 'Sample Product ' . $i,
                'slug'               => Str::slug('Sample Product ' . $i),
                'sku'                => 'PROD-' . $i,
                'main_image'         => 'http://127.0.0.1:8000/default_images/no_image.png',
                'secondary_image'    => 'http://127.0.0.1:8000/default_images/no_image.png',
                'short_description'  => 'Short description for product ' . $i,
                'description'        => 'Full description for product ' . $i,
                'marketing_label'    => 'Best Seller',
                'cost_price'         => 500,
                'mrp_price'          => 1000,
                'selling_price'      => 900,
                'tax_percentage'     => 18,
                'stock_status'       => 'in_stock',
                'is_featured'        => true,
                'status'             => true,
                'seo_title'          => 'SEO Title ' . $i,
                'seo_image'          => 'seo/image.jpg',
                'seo_description'    => 'SEO Description ' . $i,
                'seo_keywords'       => json_encode(['product, ecommerce']),
                'personalize'        => false,
                'variant_attributes' => json_encode(['size', 'color']),
                'min_price'          => 800,
                'max_price'          => 1200,
            ]);

            // Create variants for this product
            $variants = [
                [
                    'variant_name'   => 'Small',
                    'sku'            => 'PROD-' . $i . '-S',
                    'mrp_price'      => 1000,
                    'selling_price'  => 900,
                    'cost_price'     => 500,
                    'stock_quantity' => 50,
                    'is_default'     => true,
                ],
                [
                    'variant_name'   => 'Medium',
                    'sku'            => 'PROD-' . $i . '-M',
                    'mrp_price'      => 1100,
                    'selling_price'  => 1000,
                    'cost_price'     => 600,
                    'stock_quantity' => 30,
                    'is_default'     => false,
                ],
                [
                    'variant_name'   => 'Large',
                    'sku'            => 'PROD-' . $i . '-L',
                    'mrp_price'      => 1200,
                    'selling_price'  => 1100,
                    'cost_price'     => 700,
                    'stock_quantity' => 20,
                    'is_default'     => false,
                ],
            ];

            foreach ($variants as $variant) {
                ProductVariant::create([
                    'product_id'     => $product->id,
                    'variant_name'   => $variant['variant_name'],
                    'sku'            => $variant['sku'],
                    'mrp_price'      => $variant['mrp_price'],
                    'selling_price'  => $variant['selling_price'],
                    'cost_price'     => $variant['cost_price'],
                    'tax_percentage' => 18,
                    'stock_quantity' => $variant['stock_quantity'],
                    'is_default'     => $variant['is_default'],
                    'stock_status'   => 'in_stock',
                    'status'         => true,
                ]);
            }
        }
    }
}
