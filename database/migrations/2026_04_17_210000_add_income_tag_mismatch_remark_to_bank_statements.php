<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Copy of mismatch remark on the bank statement row for quick display / audit.
     */
    public function up(): void
    {
        $col = 'income_tag_mismatch_remark';
        $exists = DB::select("SHOW COLUMNS FROM `bank_statements` LIKE '{$col}'");
        if (empty($exists)) {
            DB::statement(
                "ALTER TABLE `bank_statements` ADD COLUMN `{$col}` TEXT NULL COMMENT 'Remark when income tag applied despite MOC vs bank amount mismatch'"
            );
        }
    }

    public function down(): void
    {
        $col = 'income_tag_mismatch_remark';
        $exists = DB::select("SHOW COLUMNS FROM `bank_statements` LIKE '{$col}'");
        if (! empty($exists)) {
            DB::statement("ALTER TABLE `bank_statements` DROP COLUMN `{$col}`");
        }
    }
};
