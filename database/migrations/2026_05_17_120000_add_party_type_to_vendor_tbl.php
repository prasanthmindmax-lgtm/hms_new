<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vendor_tbl', function (Blueprint $table) {
            if (! Schema::hasColumn('vendor_tbl', 'party_type')) {
                $table->string('party_type')->nullable()->after('vendor_type_name');
            }
        });
    }

    public function down(): void
    {
        Schema::table('vendor_tbl', function (Blueprint $table) {
            if (Schema::hasColumn('vendor_tbl', 'party_type')) {
                $table->dropColumn('party_type');
            }
        });
    }
};
