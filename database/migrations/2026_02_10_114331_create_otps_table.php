<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('otps', function (Blueprint $table) {
            $table->id();

            // Phone
            $table->string('phonecode', 5);
            $table->string('phone', 15);
            $table->string('full_phone', 20)->index();
            $table->string('token', 200)->nullable(); // Added nullable()

            // OTP
            $table->string('otp', 200); // Hash in service layer
            $table->string('type', 20)->default('login');

            // Status
            $table->boolean('is_verified')->default(false);
            $table->timestamp('verified_at')->nullable();
            $table->timestamp('expires_at')->nullable()->index();

            // Security
            $table->unsignedTinyInteger('attempts')->default(0);
            $table->string('ip_address', 45)->nullable();

            // Device Info
            $table->timestamps();

            /*
            |--------------------------------------------------------------------------
            | Index Optimization
            |--------------------------------------------------------------------------
            */
            $table->index(['full_phone', 'is_verified', 'expires_at']);
            $table->index(['full_phone', 'otp']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('otps');
    }
};
