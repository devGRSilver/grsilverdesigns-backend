<?php

namespace Database\Seeders\Admin;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run()
    {
        /* ================= MAIN CATEGORIES ================= */

        $shopJewellery = Category::create([
            'parent_id' => null,
            'name' => 'Shop Jewellery',
            'slug' => Str::slug('Shop Jewellery'),
            'meta_title' => 'Shop Jewellery',
            'meta_description' => 'All jewellery items',
            'meta_keywords' => json_encode(['jewellery', 'rings', 'necklaces']),
            'image' => 'http://127.0.0.1:8000/default_images/no_image.png',
            'banner_image' => 'http://127.0.0.1:8000/default_images/no_image.png',

            'is_primary' => 1,
            'status' => 1,
        ]);

        $shopByMetal = Category::create([
            'parent_id' => null,
            'name' => 'Shop by Metal',
            'slug' => Str::slug('Shop by Metal'),
            'meta_title' => 'Shop by Metal',
            'meta_description' => 'Jewellery by metal type',
            'meta_keywords' => json_encode(['gold', 'silver', 'platinum']),
            'image' => 'http://127.0.0.1:8000/default_images/no_image.png',
            'banner_image' => 'http://127.0.0.1:8000/default_images/no_image.png',

            'is_primary' => 1,
            'status' => 1,
        ]);

        $gemstones = Category::create([
            'parent_id' => null,
            'name' => 'Gemstones & Beads',
            'slug' => Str::slug('Gemstones & Beads'),
            'meta_title' => 'Gemstones & Beads',
            'meta_description' => 'Gemstones and beads collection',
            'meta_keywords' => json_encode(['gemstones', 'beads']),
            'image' => 'http://127.0.0.1:8000/default_images/no_image.png',
            'banner_image' => 'http://127.0.0.1:8000/default_images/no_image.png',

            'is_primary' => 1,
            'status' => 1,
        ]);

        $collections = Category::create([
            'parent_id' => null,
            'name' => 'Collections',
            'slug' => Str::slug('Collections'),
            'meta_title' => 'Collections',
            'meta_description' => 'Trending collections',
            'meta_keywords' => json_encode(['collection', 'trending']),
            'image' => 'http://127.0.0.1:8000/default_images/no_image.png',
            'banner_image' => 'http://127.0.0.1:8000/default_images/no_image.png',

            'is_primary' => 1,
            'status' => 1,
        ]);

        $men = Category::create([
            'parent_id' => null,
            'name' => 'Men Jewellery',
            'slug' => Str::slug('Men Jewellery'),
            'meta_title' => 'Men Jewellery',
            'meta_description' => 'Jewellery for men',
            'meta_keywords' => json_encode(['men', 'rings', 'bracelets']),
            'image' => 'http://127.0.0.1:8000/default_images/no_image.png',
            'banner_image' => 'http://127.0.0.1:8000/default_images/no_image.png',

            'is_primary' => 1,
            'status' => 1,
        ]);

        $women = Category::create([
            'parent_id' => null,
            'name' => 'Women Jewellery',
            'slug' => Str::slug('Women Jewellery'),
            'meta_title' => 'Women Jewellery',
            'meta_description' => 'Jewellery for women',
            'meta_keywords' => json_encode(['women', 'necklaces', 'earrings']),
            'image' => 'http://127.0.0.1:8000/default_images/no_image.png',
            'banner_image' => 'http://127.0.0.1:8000/default_images/no_image.png',

            'is_primary' => 1,
            'status' => 1,
        ]);

        $gifts = Category::create([
            'parent_id' => null,
            'name' => 'Gifts',
            'slug' => Str::slug('Gifts'),
            'meta_title' => 'Gifts',
            'meta_description' => 'Gift jewellery items',
            'meta_keywords' => json_encode(['gifts', 'anniversary', 'birthday']),
            'image' => 'http://127.0.0.1:8000/default_images/no_image.png',
            'banner_image' => 'http://127.0.0.1:8000/default_images/no_image.png',

            'is_primary' => 1,
            'status' => 1,
        ]);

        /* ================= SUB CATEGORIES ================= */

        // Shop Jewellery
        $shopJewellerySubs = [
            ['Rings', 'rings'],
            ['Necklaces', 'necklaces'],
            ['Bracelets', 'bracelets'],
            ['Earrings', 'earrings'],
            ['Pendants', 'pendants']
        ];

        foreach ($shopJewellerySubs as $sub) {
            Category::create([
                'parent_id' => $shopJewellery->id,
                'name' => $sub[0],
                'slug' => $sub[1],
                'meta_title' => $sub[0],
                'meta_description' => $sub[0] . ' category',
                'meta_keywords' => json_encode([$sub[0]]),
                'image' => 'http://127.0.0.1:8000/default_images/no_image.png',
                'banner_image' => 'http://127.0.0.1:8000/default_images/no_image.png',

                'is_primary' => 0,
                'status' => 1,
            ]);
        }

        // Shop by Metal
        $shopByMetalSubs = [
            ['Gold Jewellery', 'gold-jewellery', 1],
            ['Silver Jewellery', 'silver-jewellery', 2],
            ['Platinum Jewellery', 'platinum-jewellery', null],
            ['Rose Gold Jewellery', 'rose-gold-jewellery', null],
            ['Mixed Metal Jewellery', 'mixed-metal-jewellery', null]
        ];

        foreach ($shopByMetalSubs as $sub) {
            Category::create([
                'parent_id' => $shopByMetal->id,
                'name' => $sub[0],
                'slug' => $sub[1],
                'meta_title' => $sub[0],
                'meta_description' => $sub[0] . ' category',
                'meta_keywords' => json_encode([$sub[0]]),
                'image' => 'http://127.0.0.1:8000/default_images/no_image.png',
                'banner_image' => 'http://127.0.0.1:8000/default_images/no_image.png',
                'is_primary' => 0,
                'status' => 1,
            ]);
        }

        // Gemstones & Beads
        $gemstoneSubs = [
            ['Gemstones', 'gemstones'],
            ['Rough Stones', 'rough-stones'],
            ['Beads', 'beads'],
            ['Healing Stones', 'healing-stones'],
            ['Semi Precious Stones', 'semi-precious-stones']
        ];

        foreach ($gemstoneSubs as $sub) {
            Category::create([
                'parent_id' => $gemstones->id,
                'name' => $sub[0],
                'slug' => $sub[1],
                'meta_title' => $sub[0],
                'meta_description' => $sub[0] . ' category',
                'meta_keywords' => json_encode([$sub[0]]),
                'image' => 'http://127.0.0.1:8000/default_images/no_image.png',
                'banner_image' => 'http://127.0.0.1:8000/default_images/no_image.png',

                'is_primary' => 0,
                'status' => 1,
            ]);
        }

        // Collections
        $collectionSubs = [
            ['New Arrivals', 'new-arrivals'],
            ['Best Sellers', 'best-sellers'],
            ['Trending Designs', 'trending-designs'],
            ['Limited Edition', 'limited-edition'],
            ['Handcrafted Collection', 'handcrafted-collection']
        ];

        foreach ($collectionSubs as $sub) {
            Category::create([
                'parent_id' => $collections->id,
                'name' => $sub[0],
                'slug' => $sub[1],
                'meta_title' => $sub[0],
                'meta_description' => $sub[0] . ' category',
                'meta_keywords' => json_encode([$sub[0]]),
                'image' => 'http://127.0.0.1:8000/default_images/no_image.png',
                'banner_image' => 'http://127.0.0.1:8000/default_images/no_image.png',

                'is_primary' => 0,
                'status' => 1,
            ]);
        }

        // Men Jewellery
        $menSubs = [
            ['Men Rings', 'men-rings'],
            ['Men Bracelets', 'men-bracelets'],
            ['Men Chains', 'men-chains'],
            ['Men Pendants', 'men-pendants'],
            ['Men Accessories', 'men-accessories']
        ];

        foreach ($menSubs as $sub) {
            Category::create([
                'parent_id' => $men->id,
                'name' => $sub[0],
                'slug' => $sub[1],
                'meta_title' => $sub[0],
                'meta_description' => $sub[0] . ' category',
                'meta_keywords' => json_encode([$sub[0]]),
                'image' => 'http://127.0.0.1:8000/default_images/no_image.png',
                'banner_image' => 'http://127.0.0.1:8000/default_images/no_image.png',

                'is_primary' => 0,
                'status' => 1,
            ]);
        }

        // Women Jewellery
        $womenSubs = [
            ['Women Rings', 'women-rings'],
            ['Women Necklaces', 'women-necklaces'],
            ['Women Bangles', 'women-bangles'],
            ['Women Earrings', 'women-earrings'],
            ['Women Bracelets', 'women-bracelets']
        ];

        foreach ($womenSubs as $sub) {
            Category::create([
                'parent_id' => $women->id,
                'name' => $sub[0],
                'slug' => $sub[1],
                'meta_title' => $sub[0],
                'meta_description' => $sub[0] . ' category',
                'meta_keywords' => json_encode([$sub[0]]),
                'image' => 'http://127.0.0.1:8000/default_images/no_image.png',
                'banner_image' => 'http://127.0.0.1:8000/default_images/no_image.png',

                'is_primary' => 0,
                'status' => 1,
            ]);
        }

        // Gifts
        $giftSubs = [
            ['Birthday Gifts', 'birthday-gifts'],
            ['Wedding Gifts', 'wedding-gifts'],
            ['Anniversary Gifts', 'anniversary-gifts'],
            ['Festive Gifts', 'festive-gifts'],
            ['Corporate Gifts', 'corporate-gifts']
        ];

        foreach ($giftSubs as $sub) {
            Category::create([
                'parent_id' => $gifts->id,
                'name' => $sub[0],
                'slug' => $sub[1],
                'meta_title' => $sub[0],
                'meta_description' => $sub[0] . ' category',
                'meta_keywords' => json_encode([$sub[0]]),
                'image' => 'http://127.0.0.1:8000/default_images/no_image.png',
                'banner_image' => 'http://127.0.0.1:8000/default_images/no_image.png',

                'is_primary' => 0,
                'status' => 1,
            ]);
        }
    }
}
