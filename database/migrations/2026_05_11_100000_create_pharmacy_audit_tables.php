<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pharmacy_audits', function (Blueprint $table) {
            $table->id();
            $table->string('audit_number', 40)->unique();
            $table->unsignedBigInteger('company_id')->nullable();
            $table->unsignedBigInteger('zone_id')->nullable();
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->date('audit_date');
            $table->text('notes')->nullable();
            $table->unsignedInteger('total_lines')->default(0);
            $table->decimal('total_val', 15, 2)->default(0);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->index(['audit_date']);
            $table->index(['company_id', 'zone_id', 'branch_id']);
        });

        Schema::create('pharmacy_audit_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pharmacy_audit_id')->constrained('pharmacy_audits')->cascadeOnDelete();
            $table->unsignedSmallInteger('line_no')->default(1);
            $table->string('item_name', 500);
            $table->string('batch_no', 120)->nullable();
            $table->string('expiry', 20)->nullable();
            $table->decimal('mrp', 12, 2)->default(0);
            $table->integer('system_qty')->default(0);
            $table->integer('manual_qty')->default(0);
            $table->integer('diff_qty')->default(0);
            $table->decimal('val', 15, 2)->default(0);
            $table->timestamps();

            $table->index(['pharmacy_audit_id', 'line_no']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pharmacy_audit_items');
        Schema::dropIfExists('pharmacy_audits');
    }
};
