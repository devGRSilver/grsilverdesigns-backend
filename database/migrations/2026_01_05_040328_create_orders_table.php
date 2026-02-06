<?php

use App\Enums\OrderStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();

            // User relation
            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            // Order identity
            $table->string('order_number', 50)->unique()
                ->comment('Public order reference');

            // Item totals
            $table->decimal('sub_total', 16, 4)->default(0)
                ->comment('Sum of (item_price × quantity), before tax & shipping');

            // Taxes
            $table->decimal('tax_amount', 16, 4)->default(0)
                ->comment('Tax calculated on taxable amount');

            // Shipping
            $table->decimal('shipping_amount', 16, 4)->default(0)
                ->comment('Shipping / delivery charges');

            // Discounts
            $table->decimal('discount_amount', 16, 4)->default(0)
                ->comment('Coupon / promotion discount');
            $table->string('coupon_code')->nullable();

            // Totals
            $table->decimal('total_amount', 16, 4)
                ->comment('sub_total + tax_amount + shipping_amount');

            $table->decimal('grand_total', 16, 4)
                ->comment('total_amount - discount_amount');


            $table->string('currency_code', 3)->default('$');

            // Order status
            $table->enum('status', array_map(
                fn($status) => $status->value,
                OrderStatus::cases()
            ))
                ->default(OrderStatus::PENDING_PAYMENT->value)
                ->index()
                ->comment('Order lifecycle status');


            // Lifecycle timestamps
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('processing_at')->nullable();
            $table->timestamp('shipped_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamp('refunded_at')->nullable();
            $table->timestamp('failed_at')->nullable();

            // Cancellation info
            $table->string('cancellation_reason')->nullable();
            $table->foreignId('cancelled_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            // Shipping info
            $table->string('shipping_method')->nullable();
            $table->string('tracking_number')->nullable();
            $table->string('courier_name')->nullable();

            // Customer snapshot

            // Meta
            $table->json('metadata')->nullable();
            $table->text('notes')->nullable();
            $table->text('customer_notes')->nullable();

            $table->timestamps();

            // Indexes
            $table->index(['user_id', 'created_at']);
            $table->index(['status', 'created_at']);
            $table->index('currency_code');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
