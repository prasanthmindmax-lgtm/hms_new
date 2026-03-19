<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('bill_pay_tbl')) {
            return;
        }
        Schema::table('bill_pay_tbl', function (Blueprint $table) {
            if (!Schema::hasColumn('bill_pay_tbl', 'bank_statement_id')) {
                $table->unsignedBigInteger('bank_statement_id')->nullable()->after('company_id');
            }
            if (!Schema::hasColumn('bill_pay_tbl', 'bank_statement_status')) {
                $table->string('bank_statement_status', 50)->nullable()->after('bank_statement_id');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('bill_pay_tbl')) {
            return;
        }
        Schema::table('bill_pay_tbl', function (Blueprint $table) {
            if (Schema::hasColumn('bill_pay_tbl', 'bank_statement_id')) {
                $table->dropColumn('bank_statement_id');
            }
            if (Schema::hasColumn('bill_pay_tbl', 'bank_statement_status')) {
                $table->dropColumn('bank_statement_status');
            }
        });
    }
};
