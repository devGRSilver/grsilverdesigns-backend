
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('order_id')
                ->constrained('orders')
                ->cascadeOnDelete();

            $table->foreignId('product_id')
                ->nullable()
                ->constrained('products')
                ->nullOnDelete();

            $table->string('product_name')
                ->comment('Snapshot of product name at time of purchase');

            $table->string('sku', 100)
                ->nullable()
                ->comment('Product SKU at time of purchase');

            $table->decimal('unit_price', 16, 4)
                ->comment('Selling price per unit at time of purchase');

            $table->decimal('cost_price', 16, 4)->default(0)
                ->comment('Cost price per unit at time of purchase');

            $table->decimal('discount_amount', 16, 4)->default(0)
                ->comment('Total discount applied to this item');

            $table->decimal('tax_amount', 16, 4)->default(0)
                ->comment('Total tax for this item');

            $table->unsignedInteger('quantity')->default(1)
                ->comment('Quantity purchased');

            $table->decimal('total', 16, 4)
                ->comment('(unit_price × quantity) - discount_amount + tax_amount');

            $table->decimal('profit', 16, 4)->default(0)
                ->comment('(unit_price - cost_price) × quantity - discount_amount');




            // Product variant information
            $table->string('variant_name', 255)->nullable();
            $table->json('variant_options')->nullable()->comment('JSON of selected variant options');

            // Return/refund tracking
            $table->unsignedInteger('quantity_returned')->default(0);
            $table->decimal('amount_refunded', 16, 4)->default(0);
            $table->timestamp('returned_at')->nullable();

            // Metadata for product snapshot
            $table->json('product_metadata')->nullable()->comment('Snapshot of product details');
            $table->json('metadata')->nullable();

            $table->timestamps();

            // Indexes for common queries
            $table->index('order_id');
            $table->index('product_id');
            $table->index('sku');
            $table->index(['order_id', 'product_id']);
        });

        // Add constraint for positive values
        if (DB::getDriverName() === 'mysql') {
            DB::statement("
                ALTER TABLE order_items 
                ADD CONSTRAINT check_order_item_amounts 
                CHECK (
                    unit_price >= 0 AND 
                    discount_amount >= 0 AND 
                    tax_amount >= 0 AND 
                    quantity > 0 AND 
                    total >= 0 AND
                    quantity_returned >= 0 AND
                    quantity_returned <= quantity AND
                    amount_refunded >= 0
                )
            ");
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
