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
        Schema::create('metals', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique()
                ->comment('Metal Name: Gold, Silver, Platinum etc.');
            $table->decimal('price_per_gram', 12, 4)
                ->comment('1 Gram metal price');
            $table->char('currency', 3)->default('USD')
                ->comment('Currency code (ISO 4217)');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('metals');
    }
};
