<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bill_tbl', function (Blueprint $table) {
            if (! Schema::hasColumn('bill_tbl', 'department_id')) {
                $table->unsignedBigInteger('department_id')->nullable()->after('bill_category');
            }
        });
    }

    public function down(): void
    {
        Schema::table('bill_tbl', function (Blueprint $table) {
            if (Schema::hasColumn('bill_tbl', 'department_id')) {
                $table->dropColumn('department_id');
            }
        });
    }
};
