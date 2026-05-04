<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Indexed parsed transaction date for bank_statements list/export filters and ORDER BY.
 * Avoids STR_TO_DATE(bs.transaction_date, ...) on every row (full scans on large tables).
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('bank_statements')) {
            return;
        }
        if (Schema::hasColumn('bank_statements', 'transaction_date_sort')) {
            return;
        }

        $driver = DB::getDriverName();
        if ($driver === 'mysql' || $driver === 'mariadb') {
            DB::statement(
                'ALTER TABLE bank_statements '
                .'ADD COLUMN transaction_date_sort DATE '
                .'AS (STR_TO_DATE(transaction_date, \'%d/%b/%Y\')) STORED, '
                .'ADD INDEX bank_statements_transaction_date_sort_idx (transaction_date_sort)'
            );
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('bank_statements')) {
            return;
        }
        if (! Schema::hasColumn('bank_statements', 'transaction_date_sort')) {
            return;
        }

        if (DB::getDriverName() === 'mysql' || DB::getDriverName() === 'mariadb') {
            Schema::table('bank_statements', function (Blueprint $table) {
                $table->dropColumn('transaction_date_sort');
            });
        }
    }
};
