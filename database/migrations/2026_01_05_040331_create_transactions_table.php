<?php

use App\Enums\TransactionStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();

            // Foreign keys
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('order_id')->nullable()->constrained()->nullOnDelete();

            // Transaction identifiers
            $table->string('transaction_id', 50)->unique()->comment('Internal transaction reference');

            // Amounts
            $table->decimal('amount', 16, 4)->comment('Transaction amount (positive)');
            $table->string('currency_code', 3)->default('INR')->comment('ISO 4217 currency code');



            // Order status
            $table->enum('status', array_map(
                fn($status) => $status->value,
                TransactionStatus::cases()
            ))->default(TransactionStatus::PENDING->value)
                ->index()
                ->comment('Transaction status');




            // Payment details
            $table->enum('payment_method', [
                'card',
                'upi',
                'netbanking',
                'wallet',
                'cash',
                'bank_transfer',
                'cheque',
                'e_mandate'
            ])->nullable();

            $table->string('payment_gateway', 50)->nullable();
            $table->string('gateway_transaction_id', 150)->nullable();
            $table->string('gateway_payment_id', 150)->nullable();
            $table->string('gateway_order_id', 150)->nullable();
            $table->string('gateway_signature', 512)->nullable();

            // Customer info
            $table->string('customer_email', 255)->nullable();
            $table->string('customer_phone', 20)->nullable();
            $table->ipAddress('customer_ip')->nullable();
            $table->string('user_agent', 512)->nullable();

            // Fees & net amount
            $table->decimal('gateway_fee', 10, 4)->nullable()->default(0);
            $table->decimal('tax_on_fee', 10, 4)->nullable()->default(0);
            $table->decimal('net_amount', 16, 4)->nullable()->comment('amount - (gateway_fee + tax_on_fee)');

            // Timestamps
            $table->timestamp('settled_at')->nullable();
            $table->timestamp('refunded_at')->nullable();
            $table->timestamp('failed_at')->nullable();
            $table->timestamps();

            // Errors and logs
            $table->string('error_code', 50)->nullable();
            $table->text('error_message')->nullable();
            $table->json('gateway_response')->nullable();
            $table->json('metadata')->nullable();
            $table->json('notes')->nullable();

            // Indexes
            $table->index('user_id');
            $table->index('order_id');
            $table->index('currency_code');
            $table->index('payment_method');
            $table->index('payment_gateway');
            $table->index('gateway_transaction_id');
            $table->index('customer_email');
            $table->index('settled_at');
            $table->index('created_at');
            $table->index(['user_id', 'status', 'created_at']);
            $table->index(['payment_gateway', 'status', 'created_at']);
            $table->index(['order_id', 'status']);
            $table->index(['status', 'settled_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
