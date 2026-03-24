<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bill_tbl', function (Blueprint $table) {
            $table->date('timeline_date')->nullable()->after('export_amount');
            $table->string('loading_unloading_name')->nullable()->after('timeline_date');
            $table->decimal('loading_unloading_amount', 15, 2)->nullable()->after('loading_unloading_name');
        });
    }

    public function down(): void
    {
        Schema::table('bill_tbl', function (Blueprint $table) {
            $table->dropColumn(['timeline_date', 'loading_unloading_name', 'loading_unloading_amount']);
        });
    }
};
