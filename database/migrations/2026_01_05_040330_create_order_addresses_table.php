<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_addresses', function (Blueprint $table) {
            $table->id();

            $table->foreignId('order_id')
                ->constrained('orders')
                ->cascadeOnDelete();

            $table->enum('type', ['billing', 'shipping'])
                ->default('shipping')
                ->comment('Address type');

            // Contact information
            $table->string('name', 255);
            $table->string('phone', 20);
            $table->string('email', 255)->nullable();
            $table->string('company', 255)->nullable();

            // Address components
            $table->string('address_line_1', 255);
            $table->string('address_line_2', 255)->nullable();
            $table->string('address_line_3', 255)->nullable();
            $table->string('landmark', 255)->nullable();
            $table->string('city', 100);
            $table->string('state', 100);
            $table->string('postal_code', 20);
            $table->string('country_code', 2)
                ->default('IN')
                ->comment('ISO 3166-1 alpha-2 country code');
            $table->string('country', 100)->default('India');

            // Address validation fields
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();

            // Additional information
            $table->string('gstin', 15)->nullable()->comment('GST Identification Number');
            $table->string('pan', 10)->nullable()->comment('Permanent Account Number');
            $table->text('special_instructions')->nullable();

            // Address metadata
            $table->json('metadata')->nullable();

            $table->timestamps();

            // Unique constraint to prevent duplicate addresses of same type per order
            $table->unique(['order_id', 'type']);

            // Indexes
            $table->index('order_id');
            $table->index('type');
            $table->index(['country_code', 'postal_code']);
            $table->index('city');
            $table->index('state');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_addresses');
    }
};
