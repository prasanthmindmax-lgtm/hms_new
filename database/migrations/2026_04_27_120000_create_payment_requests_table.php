<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_requests', function (Blueprint $table) {
            $table->id();
            $table->string('request_no', 40)->unique();
            $table->unsignedBigInteger('company_id')->nullable();
            $table->unsignedBigInteger('zone_id');
            $table->unsignedBigInteger('branch_id');
            $table->unsignedBigInteger('vendor_id')->nullable();
            $table->string('payment_type', 64);
            $table->decimal('amount', 14, 2);
            $table->unsignedBigInteger('purchase_order_id')->nullable();
            $table->decimal('po_total_snapshot', 14, 2)->nullable();
            $table->string('po_attachment_path', 500)->nullable();
            $table->string('document_attachment_path', 500)->nullable();
            $table->text('remarks')->nullable();
            $table->string('bank_account_number', 64)->nullable();
            $table->string('bank_ifsc_code', 20)->nullable();
            $table->text('bank_branch_details')->nullable();
            $table->string('bank_document_path', 500)->nullable();
            $table->string('status', 32)->default('pending')->index();
            $table->text('rejection_reason')->nullable();
            $table->unsignedBigInteger('reviewed_by')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->timestamps();
            $table->index(['zone_id', 'branch_id']);
            $table->index('purchase_order_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_requests');
    }
};
