<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // QR Codes
        if (!Schema::hasTable('vms_qr_codes')) {
            Schema::create('vms_qr_codes', function (Blueprint $table) {
                $table->id();
                $table->uuid('uuid')->unique();
                $table->string('label');
                $table->string('location')->nullable();
                $table->string('branch')->nullable();
                $table->boolean('is_active')->default(true);
                $table->unsignedBigInteger('scan_count')->default(0);
                $table->unsignedBigInteger('created_by')->nullable();
                $table->timestamps();
            });
        }

        // Visitors
        if (!Schema::hasTable('vms_visitors')) {
            Schema::create('vms_visitors', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('qr_code_id')->nullable();
                $table->string('visitor_name');
                $table->string('visitor_phone', 20);
                $table->string('visitor_email')->nullable();
                $table->string('visitor_type'); // pharma, non_pharma, patient_relative, job_applicant, government, other
                $table->string('company_name')->nullable();
                $table->string('purpose');
                $table->string('person_to_meet')->nullable();
                $table->string('department')->nullable();
                $table->time('appointment_time')->nullable();
                $table->text('equipment_carried')->nullable();
                $table->string('id_type')->nullable();   // aadhar, driving_license, passport, etc.
                $table->string('id_number')->nullable();
                $table->string('photo')->nullable();
                $table->boolean('declaration_agreed')->default(false);
                $table->string('badge_number')->nullable();
                $table->string('branch')->nullable();
                $table->string('status')->default('pending'); // pending, approved, rejected, inside, checked_out
                $table->timestamp('entry_time')->nullable();
                $table->timestamp('exit_time')->nullable();
                $table->unsignedBigInteger('approved_by')->nullable();
                $table->timestamp('approved_at')->nullable();
                $table->text('rejection_reason')->nullable();
                $table->timestamps();
            });
        }

        // Blacklist
        if (!Schema::hasTable('vms_blacklist')) {
            Schema::create('vms_blacklist', function (Blueprint $table) {
                $table->id();
                $table->string('visitor_name');
                $table->string('visitor_phone', 20)->nullable();
                $table->string('company_name')->nullable();
                $table->string('visitor_type')->nullable();
                $table->text('reason');
                $table->unsignedInteger('incidents')->default(1);
                $table->boolean('is_active')->default(true);
                $table->unsignedBigInteger('blacklisted_by')->nullable();
                $table->timestamp('blacklisted_at')->useCurrent();
                $table->timestamps();
            });
        }

        // Settings
        if (!Schema::hasTable('vms_settings')) {
            Schema::create('vms_settings', function (Blueprint $table) {
                $table->id();
                $table->string('key')->unique();
                $table->text('value')->nullable();
                $table->timestamps();
            });

            // Seed default settings
            DB::table('vms_settings')->insert([
                ['key' => 'hospital_name',      'value' => "Dr. Aravind's IVF & Pregnancy Centre", 'created_at' => now(), 'updated_at' => now()],
                ['key' => 'default_branch',     'value' => 'Main Hospital',                         'created_at' => now(), 'updated_at' => now()],
                ['key' => 'max_visit_duration', 'value' => '60',                                    'created_at' => now(), 'updated_at' => now()],
                ['key' => 'otp_enabled',        'value' => '0',                                     'created_at' => now(), 'updated_at' => now()],
                ['key' => 'auto_approve',       'value' => '0',                                     'created_at' => now(), 'updated_at' => now()],
                ['key' => 'doctors_list',       'value' => "Dr. Aravind (IVF Specialist)\nDr. Priya (Gynaecologist)\nAdmin Manager\nPurchase Dept.", 'created_at' => now(), 'updated_at' => now()],
                ['key' => 'departments_list',   'value' => "OPD\nLab\nPharmacy\nWard\nICU\nAdministration", 'created_at' => now(), 'updated_at' => now()],
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('vms_settings');
        Schema::dropIfExists('vms_blacklist');
        Schema::dropIfExists('vms_visitors');
        Schema::dropIfExists('vms_qr_codes');
    }
};
