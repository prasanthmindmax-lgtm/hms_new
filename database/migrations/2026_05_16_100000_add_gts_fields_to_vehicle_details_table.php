<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vehicle_details', function (Blueprint $table) {
            if (!Schema::hasColumn('vehicle_details', 'gts_installed')) {
                $table->string('gts_installed', 10)->nullable()->after('vehicle_incharge_admin');
            }
            if (!Schema::hasColumn('vehicle_details', 'gts_status')) {
                $table->string('gts_status', 20)->nullable()->after('gts_installed');
            }
        });
    }

    public function down(): void
    {
        Schema::table('vehicle_details', function (Blueprint $table) {
            if (Schema::hasColumn('vehicle_details', 'gts_status')) {
                $table->dropColumn('gts_status');
            }
            if (Schema::hasColumn('vehicle_details', 'gts_installed')) {
                $table->dropColumn('gts_installed');
            }
        });
    }
};
