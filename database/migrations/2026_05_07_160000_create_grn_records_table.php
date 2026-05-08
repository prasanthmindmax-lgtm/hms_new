<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('grn_records', function (Blueprint $table) {
            $table->id();
            $table->string('grn_number', 32)->unique();
            $table->unsignedBigInteger('company_id')->nullable();
            $table->string('company_name', 255)->nullable();
            $table->unsignedBigInteger('zone_id')->nullable();
            $table->string('zone_name', 120)->nullable();
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->string('branch_name', 255)->nullable();
            $table->unsignedBigInteger('vendor_id')->nullable();
            $table->string('vendor_name', 255);
            $table->string('invoice_number', 120);
            $table->date('invoice_date')->nullable();
            $table->date('received_date')->nullable();
            $table->string('received_by', 255)->nullable();
            $table->string('invoice_copy_path', 500)->nullable();
            $table->string('gps_video_path', 500)->nullable();
            $table->boolean('gps_video_uploaded')->default(false);
            $table->string('audit_approval_status', 20)->default('pending');
            $table->text('remarks')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('reviewed_by')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grn_records');
    }
};
