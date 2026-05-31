<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('vendor_tbl', 'status_changed_on')) {
            Schema::table('vendor_tbl', function (Blueprint $table) {
                $table->date('status_changed_on')->nullable()->after('active_status');
            });
        }

        if (! Schema::hasColumn('vendor_tbl', 'status_changed_by')) {
            Schema::table('vendor_tbl', function (Blueprint $table) {
                $table->unsignedBigInteger('status_changed_by')->nullable()->after('status_changed_on');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('vendor_tbl', 'status_changed_by')) {
            Schema::table('vendor_tbl', function (Blueprint $table) {
                $table->dropColumn('status_changed_by');
            });
        }

        if (Schema::hasColumn('vendor_tbl', 'status_changed_on')) {
            Schema::table('vendor_tbl', function (Blueprint $table) {
                $table->dropColumn('status_changed_on');
            });
        }
    }
};
