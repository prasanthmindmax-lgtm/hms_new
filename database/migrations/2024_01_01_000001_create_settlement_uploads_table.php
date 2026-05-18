<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settlement_uploads', function (Blueprint $table) {
            $table->id();
            $table->string('original_filename');
            $table->string('stored_filename');
            $table->string('file_path');
            $table->string('file_size');
            $table->string('mime_type')->default('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            $table->integer('total_rows')->default(0);
            $table->integer('total_accounts')->default(0);
            $table->decimal('total_transaction_amount', 15, 2)->default(0);
            $table->decimal('total_net_settlement_amount', 15, 2)->default(0);
            $table->enum('status', ['processing', 'completed', 'failed'])->default('processing');
            $table->text('error_message')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('settlement_uploads');
    }
};