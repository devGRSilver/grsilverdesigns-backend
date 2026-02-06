<?php

use App\Constants\Constant;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->foreignId('category_id')
                ->constrained('categories')
                ->cascadeOnDelete();



            $table->enum('product_type', ['with_variant', 'without_variant'])
                ->default('with_variant');

            $table->string('name');
            $table->string('slug', 191)->unique();
            $table->string('sku')->unique()->comment('Base/Default SKU for the product');
            $table->string('main_image')->nullable();
            $table->string('secondary_image')->nullable();

            $table->longText('short_description')->nullable();
            $table->longText('description')->nullable();

            $table->string('marketing_label')->nullable();

            $table->decimal('cost_price', 10, 2)->nullable()
                ->comment('Purchase cost (for non-variant products)');

            $table->decimal('mrp_price', 10, 2)->nullable()
                ->comment('Maximum Retail Price (for non-variant products)');

            $table->decimal('selling_price', 10, 2)->nullable()
                ->comment('Final selling price (for non-variant products)');

            $table->decimal('tax_percentage', 5, 2)->default(0)
                ->comment('Default tax percentage');


            $table->enum('stock_status', ['in_stock', 'out_of_stock', 'on_backorder'])
                ->default('in_stock')
                ->comment('Overall stock status');

            $table->boolean('is_featured')->default(false);
            $table->boolean('status')->default(true)
                ->comment('1 = Active/Published, 0 = Inactive/Draft');



            // SEO
            $table->string('seo_title')->nullable();
            $table->string('seo_image')->nullable();
            $table->text('seo_description')->nullable();
            $table->text('seo_keywords')->nullable();

            $table->boolean('personalize')->default(false)
                ->comment('Whether product can be personalized');

            // Variant configuration
            $table->json('variant_attributes')->nullable()
                ->comment('JSON of variant attributes like [{"name":"Color","values":["Red","Blue"]}]');

            // ========== AGGREGATED DATA (For products WITH variants) ==========
            $table->decimal('min_price', 10, 2)->nullable()
                ->comment('Minimum price among all variants');

            $table->decimal('max_price', 10, 2)->nullable()
                ->comment('Maximum price among all variants');



            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['status', 'is_featured']);
            $table->index(['selling_price', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
