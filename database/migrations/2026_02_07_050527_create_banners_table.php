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
        Schema::create('banners', function (Blueprint $table) {
            $table->id();

            $table->string('title')->nullable();

            $table->string('type', 20)
                ->default('banner')
                ->comment('banner, slider');

            $table->string('group_key', 50)
                ->comment('home-top, home-slider, sidebar');

            $table->string('image_url')->nullable();
            $table->string('link_url')->nullable();

            $table->text('description')->nullable();
            $table->string('button_text')->nullable();

            $table->boolean('status')->default(true);

            $table->timestamps();

            $table->index(['type', 'group_key', 'status']);
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('banners');
    }
};
