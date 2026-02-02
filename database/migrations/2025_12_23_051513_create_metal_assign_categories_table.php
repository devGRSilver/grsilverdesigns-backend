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
        Schema::create('metal_assign_categories', function (Blueprint $table) {
            $table->id();

            $table->foreignId('metal_id')
                ->constrained('metals')
                ->cascadeOnDelete()
                ->comment('metals table primary key');

            $table->foreignId('category_id')
                ->constrained('categories')
                ->cascadeOnDelete();

            $table->foreignId('sub_category_id')
                ->constrained('categories')
                ->cascadeOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('metal_assign_categories');
    }
};
