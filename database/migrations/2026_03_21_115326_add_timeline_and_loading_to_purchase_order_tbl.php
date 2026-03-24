<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('purchase_order_tbl', function (Blueprint $table) {
            if (!Schema::hasColumn('purchase_order_tbl', 'timeline_date')) {
                $table->string('timeline_date')->nullable()->after('subject');
            }
            if (!Schema::hasColumn('purchase_order_tbl', 'loading_unloading_name')) {
                $table->string('loading_unloading_name')->nullable()->after('export_amount');
            }
            if (!Schema::hasColumn('purchase_order_tbl', 'loading_unloading_amount')) {
                $table->decimal('loading_unloading_amount', 12, 2)->nullable()->default(0)->after('loading_unloading_name');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_order_tbl', function (Blueprint $table) {
            $table->dropColumn(['timeline_date', 'loading_unloading_name', 'loading_unloading_amount']);
        });
    }
};
