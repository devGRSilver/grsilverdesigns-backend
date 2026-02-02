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
        Schema::create('attribute_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attribute_id')
                ->constrained('attributes')
                ->cascadeOnDelete();
            $table->string('value')->comment('Attribute value, e.g., Red, Blue, XL');
            $table->integer('sort_order')->default(0)->comment('Display order');
            $table->timestamps();

            $table->index(['attribute_id', 'sort_order']);
            $table->unique(['attribute_id', 'value']); // optional uniqueness
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attribute_values');
    }
};
