<?php

use App\Constants\Constant;
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
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('variant_name')->nullable();
            $table->string('sku');
            $table->decimal('mrp_price', 10, 2);
            $table->decimal('selling_price', 10, 2);
            $table->decimal('cost_price', 10, 2)->nullable();
            $table->decimal('weight', 10, 3)->default(0);
            $table->decimal('tax_percentage', 5, 2)->default(0);
            $table->integer('stock_quantity')->default(0);
            $table->boolean('is_default')->default(false);
            $table->enum('stock_status', ['in_stock', 'out_of_stock', 'on_backorder'])
                ->default('in_stock');
            $table->boolean('status')->default(Constant::ACTIVE);
            $table->timestamps();

            $table->unique(['product_id', 'sku']);
            $table->index('product_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_variants');
    }
};
