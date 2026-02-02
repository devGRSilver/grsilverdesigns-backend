<?php

namespace Database\Seeders\Admin;

use App\Models\Content;
use Illuminate\Database\Seeder;

class ContentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $contents = [

            // =======================
            // BASIC PAGES
            // =======================
            [
                'title' => 'About Us',
                'slug' => 'about-us',
                'type' => 'page',
                'description' => '<p>Learn more about our company, values, and mission.</p>',
                'meta_title' => 'About Us',
                'meta_keywords' => ['about us', 'company', 'our story'],
                'meta_description' => 'Learn more about our company and what we stand for.',
                'status' => true,
                'image' => 'http://127.0.0.1:8000/default_images/no_image.png',
            ],

            [
                'title' => 'Contact Us',
                'slug' => 'contact-us',
                'type' => 'page',
                'description' => '<p>Get in touch with us for any queries or support.</p>',
                'meta_title' => 'Contact Us',
                'meta_keywords' => ['contact us', 'customer support', 'help'],
                'meta_description' => 'Contact our support team for assistance and inquiries.',
                'status' => true,
                'image' => 'http://127.0.0.1:8000/default_images/no_image.png'
            ],

            // =======================
            // LEGAL / POLICY PAGES
            // =======================
            [
                'image' => 'http://127.0.0.1:8000/default_images/no_image.png',
                'title' => 'Privacy Policy',
                'slug' => 'privacy-policy',
                'type' => 'policy',
                'description' => '<p>Our privacy policy explains how we collect and use your data.</p>',
                'meta_title' => 'Privacy Policy',
                'meta_keywords' => ['privacy policy', 'data protection', 'user privacy'],
                'meta_description' => 'Read our privacy policy to understand how we protect your data.',
                'status' => true,
                'image' => 'http://127.0.0.1:8000/default_images/no_image.png'
            ],

            [
                'title' => 'Terms & Conditions',
                'slug' => 'terms-conditions',
                'type' => 'policy',
                'description' => '<p>Terms and conditions for using our website and services.</p>',
                'meta_title' => 'Terms & Conditions',
                'meta_keywords' => ['terms and conditions', 'user agreement'],
                'meta_description' => 'Terms and conditions for using our website.',
                'status' => true,
                'image' => 'http://127.0.0.1:8000/default_images/no_image.png'
            ],

            [
                'title' => 'Refund & Cancellation Policy',
                'slug' => 'refund-policy',
                'type' => 'policy',
                'description' => '<p>Details about refunds, returns, and cancellations.</p>',
                'meta_title' => 'Refund Policy',
                'meta_keywords' => ['refund policy', 'return policy', 'cancellation'],
                'meta_description' => 'Refund and cancellation policy for orders.',
                'status' => true,
                'image' => 'http://127.0.0.1:8000/default_images/no_image.png'
            ],

            [
                'title' => 'Shipping Policy',
                'slug' => 'shipping-policy',
                'type' => 'policy',
                'description' => '<p>Shipping methods, charges, and delivery timelines.</p>',
                'meta_title' => 'Shipping Policy',
                'meta_keywords' => ['shipping policy', 'delivery information'],
                'meta_description' => 'Learn about shipping charges and delivery timelines.',
                'status' => true,
                'image' => 'http://127.0.0.1:8000/default_images/no_image.png'
            ],



            // =======================
            // E-COMMERCE IMPORTANT
            // =======================
            [
                'title' => 'Payment Policy',
                'slug' => 'payment-policy',
                'type' => 'policy',
                'description' => '<p>Payment methods, security, and transaction details.</p>',
                'meta_title' => 'Payment Policy',
                'meta_keywords' => ['payment policy', 'secure payments'],
                'meta_description' => 'Learn about accepted payment methods and security.',
                'status' => true,
                'image' => 'http://127.0.0.1:8000/default_images/no_image.png'
            ],

            [
                'title' => 'Disclaimer',
                'slug' => 'disclaimer',
                'type' => 'policy',
                'description' => '<p>Disclaimer regarding product information and liability.</p>',
                'meta_title' => 'Disclaimer',
                'meta_keywords' => ['disclaimer', 'liability'],
                'meta_description' => 'Website disclaimer and limitation of liability.',
                'status' => true,
                'image' => 'http://127.0.0.1:8000/default_images/no_image.png'
            ],
        ];

        foreach ($contents as $content) {
            Content::updateOrCreate(
                ['slug' => $content['slug']], // prevent duplicates
                $content
            );
        }
    }
}
