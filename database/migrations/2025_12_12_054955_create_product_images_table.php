<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('product_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_variant_id')
                ->nullable()
                ->constrained('product_variants')
                ->cascadeOnDelete();
            $table->string('image_url');
            $table->boolean('is_default')->default(false)->comment('Main image for product/variant');
            $table->integer('sort_order')->default(0)->comment('Order in carousel');
            $table->timestamps();

            $table->index(['product_id', 'product_variant_id']);
            $table->index('is_default');
            // Optional: enforce one default image per product/variant
            // $table->unique(['product_id', 'product_variant_id', 'is_default']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_images');
    }
};
