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
        Schema::create('users', function (Blueprint $table) {

            $table->id()->comment('Primary key');
            $table->string('name', 100)->default('Gr User')
                ->comment('Full name of the user');

            $table->string('phonecode', 5)
                ->nullable()
                ->comment('International phone country code (e.g., 91, 1)');

            $table->string('phone', 20)
                ->nullable()
                ->comment('User phone number without country code');

            $table->timestamp('phone_verified_at')
                ->nullable()
                ->comment('Timestamp when phone was verified');

            $table->string('email', 150)
                ->nullable()
                ->comment('User email address');

            $table->timestamp('email_verified_at')
                ->nullable()
                ->comment('Timestamp when email was verified');

            $table->string('password')
                ->nullable()
                ->comment('Hashed password (nullable for OTP-only accounts)');

            $table->rememberToken()
                ->comment('Remember me authentication token');



            $table->string('profile_picture')
                ->nullable()
                ->comment('Path or URL of user profile picture');

            $table->boolean('status')
                ->default(true)
                ->comment('Account status: 1=active, 0=suspended');

            $table->boolean('profile_complete')
                ->default(false)
                ->comment('Profile completion flag: 1=complete, 0=incomplete');



            $table->timestamp('last_login_at')
                ->nullable()
                ->comment('Last successful login timestamp');

            $table->tinyInteger('device_type')
                ->nullable()
                ->comment('Device type: 1=iOS, 2=Android, 3=Web, 4=Other');

            $table->string('device_token')
                ->nullable()
                ->comment('Push notification token (FCM/APNs)');

            $table->string('user_agent', 255)
                ->nullable()
                ->comment('User device/browser user agent string');

            $table->string('ip_address', 45)
                ->nullable()
                ->comment('Last login IP address (IPv4/IPv6 supported)');



            $table->char('country', 2)
                ->nullable()
                ->comment('ISO country code (e.g., IN, US)');

            $table->char('country_name', 50)
                ->nullable()
                ->comment('ISO country name (e.g., INDIA)');

            $table->string('city', 100)
                ->nullable()
                ->comment('User city');

            $table->string('timezone', 40)
                ->default('UTC')
                ->comment('User timezone identifier');

            $table->char('currency', 3)
                ->default('USD')
                ->comment('ISO currency code (e.g., USD, INR)');

            $table->decimal('latitude', 10, 7)
                ->nullable()
                ->comment('User latitude coordinate');

            $table->decimal('longitude', 10, 7)
                ->nullable()
                ->comment('User longitude coordinate');



            $table->timestamps(); // created_at, updated_at
            $table->softDeletes()->comment('Soft delete timestamp');



            $table->unique(['phonecode', 'phone']);
            $table->unique('email');

            $table->index('status');
            $table->index('profile_complete');
            $table->index(['email_verified_at', 'phone_verified_at']);
            $table->index(['country', 'city']);
            $table->index('deleted_at');
        });


        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
