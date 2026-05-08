<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('bill_tbl') && ! Schema::hasColumn('bill_tbl', 'Tax_in_ex')) {
            Schema::table('bill_tbl', function (Blueprint $table) {
                $table->string('Tax_in_ex', 32)->nullable()->after('discount_tax');
            });
        }

        if (Schema::hasTable('quotation_order_tbl') && ! Schema::hasColumn('quotation_order_tbl', 'Tax_in_ex')) {
            Schema::table('quotation_order_tbl', function (Blueprint $table) {
                $table->string('Tax_in_ex', 32)->nullable()->after('discount_tax');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('bill_tbl') && Schema::hasColumn('bill_tbl', 'Tax_in_ex')) {
            Schema::table('bill_tbl', function (Blueprint $table) {
                $table->dropColumn('Tax_in_ex');
            });
        }

        if (Schema::hasTable('quotation_order_tbl') && Schema::hasColumn('quotation_order_tbl', 'Tax_in_ex')) {
            Schema::table('quotation_order_tbl', function (Blueprint $table) {
                $table->dropColumn('Tax_in_ex');
            });
        }
    }
};
