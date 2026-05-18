<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settlement_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('upload_id')->constrained('settlement_uploads')->cascadeOnDelete();
            $table->foreignId('account_id')->constrained('settlement_accounts')->cascadeOnDelete();
            $table->string('mid');
            $table->string('tid')->nullable();
            $table->string('merchant_name')->nullable();
            $table->string('trading_name')->nullable();
            $table->date('transaction_date')->nullable();
            $table->string('currency')->default('INR');
            $table->string('mode_of_payment')->nullable();
            $table->string('card_number')->nullable();
            $table->string('card_scheme')->nullable();
            $table->string('card_program')->nullable();
            $table->string('card_type')->nullable();
            $table->string('card_category')->nullable();
            $table->string('transaction_id')->nullable();
            $table->string('invoice_number')->nullable();
            $table->string('batch_number')->nullable();
            $table->string('rrn')->nullable();
            $table->string('arn')->nullable();
            $table->string('transaction_type')->nullable();
            $table->decimal('transaction_amount', 15, 2)->default(0);
            $table->decimal('charges', 15, 2)->default(0);
            $table->decimal('taxes', 15, 2)->default(0);
            $table->decimal('net_settlement_amount', 15, 2)->default(0);
            $table->string('auth_code')->nullable();
            $table->string('utr_reference')->nullable();
            $table->string('transaction_datetime')->nullable();
            $table->date('settlement_date')->nullable();
            $table->string('status')->default('SETTLED');
            $table->timestamps();

            $table->index(['upload_id', 'mid']);
            $table->index('transaction_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('settlement_transactions');
    }
};