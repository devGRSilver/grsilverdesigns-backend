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
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->string('description')->nullable();
            $table->enum('type', ['percentage', 'fixed_amount']);
            $table->decimal('value', 10, 2)->nullable();
            $table->integer('usage_limit')->nullable();
            $table->integer('usage_count')->default(0);
            $table->integer('user_limit')->nullable()->comment('Max uses per user');
            $table->decimal('min_purchase_amount', 10, 2)->nullable();
            $table->integer('min_items')->nullable();
            $table->dateTime('starts_at');
            $table->dateTime('expires_at');
            $table->boolean('status')->default(true);
            $table->boolean('first_order_only')->default(false);
            $table->boolean('free_shipping')->default(false);
            $table->json('included_products')->nullable();
            $table->json('included_categories')->nullable();
            $table->json('included_users')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};
