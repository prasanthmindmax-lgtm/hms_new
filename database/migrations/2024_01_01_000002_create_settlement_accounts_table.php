<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settlement_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('upload_id')->constrained('settlement_uploads')->cascadeOnDelete();
            $table->string('mid');          // Account / Merchant ID
            $table->string('tid')->nullable();
            $table->string('merchant_name')->nullable();
            $table->string('trading_name')->nullable();
            $table->string('branch')->nullable();
            $table->integer('transaction_count')->default(0);
            $table->decimal('total_transaction_amount', 15, 2)->default(0);
            $table->decimal('total_charges', 15, 2)->default(0);
            $table->decimal('total_taxes', 15, 2)->default(0);
            $table->decimal('total_net_settlement_amount', 15, 2)->default(0);
            $table->date('transaction_date')->nullable();
            $table->date('settlement_date')->nullable();
            $table->string('currency')->default('INR');
            $table->timestamps();

            $table->index(['upload_id', 'mid']);
            $table->index('transaction_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('settlement_accounts');
    }
};