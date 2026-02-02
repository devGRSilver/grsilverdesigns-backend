<?php

namespace Database\Seeders\Admin;

use App\Models\Blog;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BlogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Blog::truncate();

        for ($i = 1; $i <= 1; $i++) {
            Blog::create([
                'title' => "Sample Blog Post {$i}",
                'slug' => Str::slug("Sample Blog Post {$i}"),
                'short_description' => "This is a short description for blog post {$i}.",
                'content' => "<p>This is the full content of blog post {$i}. You can replace this with HTML or editor content.</p>",
                'status' => true,
                'published_at' => now(),
                'created_by' => 1,
                'featured_image' => 'http://127.0.0.1:8000/default_images/no_image.png',
            ]);
        }
    }
}
