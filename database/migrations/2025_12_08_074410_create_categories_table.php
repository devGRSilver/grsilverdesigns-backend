<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();

            // Self-referencing parent category
            $table->foreignId('parent_id')
                ->nullable()
                ->constrained('categories')
                ->cascadeOnDelete()
                ->cascadeOnUpdate()
                ->comment('Parent Category ID');

            $table->string('name');
            $table->string('slug', 191)->unique()->comment('SEO Friendly URL Slug');

            // SEO fields
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->text('meta_keywords')->nullable();

            // Images
            $table->string('image')->nullable();
            $table->integer('sort_order')->default(1);




            $table->string('banner_image')->nullable();

            $table->boolean('is_primary')->default(0);
            $table->boolean('status')->default(1);

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['status', 'is_primary']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
