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
        Schema::create('petty_cash', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('report_id')->nullable();
            $table->date('expense_date')->nullable();
            $table->unsignedBigInteger('vendor_id')->nullable();
            $table->unsignedBigInteger('expense_category_id')->nullable();
            $table->unsignedBigInteger('company_id')->nullable();
            $table->unsignedBigInteger('zone_id')->nullable();
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->string('branch_ids', 500)->nullable();
            $table->string('currency')->default('INR');
            $table->decimal('total_amount',12,2)->default(0);
            $table->boolean('claim_reimbursement')->default(0);
            $table->string('tax_type', 64)->nullable();
            $table->string('supply_kind', 16)->nullable();
            $table->string('gstin', 20)->nullable();
            $table->boolean('reverse_charge')->default(false);
            $table->string('destination_of_supply', 128)->nullable();
            $table->string('gst_tax_label', 191)->nullable();
            $table->string('sac_hsn', 64)->nullable();
            $table->string('invoice_no', 128)->nullable();
            $table->text('notes')->nullable();
            $table->string('reference_no')->nullable();
            $table->string('status')->default('pending');
            $table->text('reject_reason')->nullable();
            $table->string('expense_type')->default('single');
            $table->string('receipt_path')->nullable();
            $table->json('attachment_paths')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('petty_cash');
    }
};
