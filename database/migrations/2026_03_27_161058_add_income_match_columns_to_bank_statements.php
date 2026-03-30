<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Add income reconciliation match-tracking columns to bank_statements.
     * Tracks whether a bank statement row has been tagged to an income_reconciliation_table record,
     * who tagged it, which branch/date, and when.
     */
    public function up(): void
    {
        $columns = [
            // enum-like status: 'income_unmatched' (default) or 'income_matched'
            ['income_match_status',     "VARCHAR(30) NOT NULL DEFAULT 'income_unmatched' COMMENT 'income_matched or income_unmatched'"],
            // FK to income_reconciliation_table.id
            ['income_reconciliation_id',"BIGINT UNSIGNED NULL COMMENT 'Linked income_reconciliation_table row'"],
            // denormalised for quick display
            ['income_matched_branch',   "VARCHAR(255) NULL COMMENT 'Branch name that was matched'"],
            ['income_matched_date',     "VARCHAR(20) NULL COMMENT 'Date matched (d/m/Y)'"],
            ['income_matched_by',       "BIGINT UNSIGNED NULL COMMENT 'User ID who applied the income tag'"],
            ['income_matched_at',       "TIMESTAMP NULL COMMENT 'When the income tag was applied'"],
        ];

        foreach ($columns as [$col, $definition]) {
            $exists = DB::select("SHOW COLUMNS FROM `bank_statements` LIKE '{$col}'");
            if (empty($exists)) {
                DB::statement("ALTER TABLE `bank_statements` ADD COLUMN `{$col}` {$definition}");
            }
        }
    }

    public function down(): void
    {
        $columns = [
            'income_match_status',
            'income_reconciliation_id',
            'income_matched_branch',
            'income_matched_date',
            'income_matched_by',
            'income_matched_at',
        ];

        foreach ($columns as $col) {
            $exists = DB::select("SHOW COLUMNS FROM `bank_statements` LIKE '{$col}'");
            if (!empty($exists)) {
                DB::statement("ALTER TABLE `bank_statements` DROP COLUMN `{$col}`");
            }
        }
    }
};
