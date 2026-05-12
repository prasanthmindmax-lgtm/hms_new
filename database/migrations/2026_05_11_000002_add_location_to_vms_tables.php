<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Add location_id + branch_type to vms_qr_codes
        Schema::table('vms_qr_codes', function (Blueprint $table) {
            if (!Schema::hasColumn('vms_qr_codes', 'location_id')) {
                $table->unsignedBigInteger('location_id')->nullable()->after('branch');
            }
            if (!Schema::hasColumn('vms_qr_codes', 'branch_type')) {
                $table->string('branch_type', 50)->nullable()->after('location_id'); // hospital / regional_office
            }
        });

        // Add location_id + branch_type to vms_visitors
        Schema::table('vms_visitors', function (Blueprint $table) {
            if (!Schema::hasColumn('vms_visitors', 'location_id')) {
                $table->unsignedBigInteger('location_id')->nullable()->after('branch');
            }
            if (!Schema::hasColumn('vms_visitors', 'branch_type')) {
                $table->string('branch_type', 50)->nullable()->after('location_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('vms_qr_codes', function (Blueprint $table) {
            $table->dropColumn(['location_id', 'branch_type']);
        });
        Schema::table('vms_visitors', function (Blueprint $table) {
            $table->dropColumn(['location_id', 'branch_type']);
        });
    }
};
