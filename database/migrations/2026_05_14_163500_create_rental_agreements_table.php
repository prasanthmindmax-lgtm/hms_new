<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('rental_agreements')) {
            Schema::create('rental_agreements', function (Blueprint $table) {
                $table->id();
                $table->string('agreement_type', 30)->default('hospital');
                $table->string('agreement_number', 40)->unique();
                $table->unsignedBigInteger('company_id')->nullable();
                $table->unsignedBigInteger('zone_id')->nullable();
                $table->unsignedBigInteger('branch_id')->nullable();
                $table->date('agreement_date');
                $table->string('owner_name', 255);
                $table->unsignedBigInteger('vendor_id')->nullable();
                $table->text('additional_party_names')->nullable();
                $table->text('address');
                $table->string('agreement_period', 120);
                $table->decimal('advance_amount', 15, 2)->default(0);
                $table->decimal('monthly_rent_amount', 15, 2)->default(0);
                $table->string('gst_type', 30);
                $table->decimal('gst_percentage', 8, 2)->nullable();
                $table->decimal('gst_amount', 15, 2)->nullable();
                $table->unsignedBigInteger('gst_tax_id')->nullable();
                $table->string('gst_tax_name', 120)->nullable();
                $table->string('gst_tax_type', 10)->nullable();
                $table->decimal('cgst_amount', 15, 2)->nullable();
                $table->decimal('sgst_amount', 15, 2)->nullable();
                $table->decimal('igst_amount', 15, 2)->nullable();
                $table->unsignedBigInteger('tds_tax_id')->nullable();
                $table->string('tds_tax_name', 120)->nullable();
                $table->decimal('tds_rate', 8, 4)->nullable();
                $table->unsignedBigInteger('tds_section_id')->nullable();
                $table->string('tds_section', 40)->nullable();
                $table->decimal('tds_amount', 15, 2)->nullable();
                $table->boolean('rcm_applicable')->default(false);
                $table->decimal('rcm_value', 15, 2)->nullable();
                $table->decimal('maintenance_amount', 15, 2)->nullable();
                $table->string('eb_number', 120)->nullable();
                $table->decimal('sq_ft_area', 12, 2)->nullable();
                $table->string('rent_revision', 120)->nullable();
                $table->decimal('rent_hike_percentage', 8, 2)->nullable();
                $table->date('end_of_agreement_date');
                $table->string('termination_period', 120)->nullable();
                $table->string('date_of_rent_payment', 120)->nullable();
                $table->string('pan_number', 30)->nullable();
                $table->string('contact_person_name', 255)->nullable();
                $table->string('contact_person_number', 30)->nullable();
                $table->string('attachment_path', 255)->nullable();
                $table->string('attachment_original_name', 255)->nullable();
                $table->string('building_photo_path', 255)->nullable();
                $table->string('building_photo_original_name', 255)->nullable();
                $table->unsignedBigInteger('created_by')->nullable();
                $table->timestamps();

                $table->index('agreement_type');
                $table->index('company_id');
                $table->index('zone_id');
                $table->index('branch_id');
                $table->index('vendor_id');
                $table->index('agreement_date');
                $table->index('end_of_agreement_date');
                $table->index('gst_type');
                $table->index('rcm_applicable');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('rental_agreements');
    }
};
