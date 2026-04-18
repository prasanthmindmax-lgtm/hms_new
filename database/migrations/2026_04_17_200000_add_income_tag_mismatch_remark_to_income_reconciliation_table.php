<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * User remark when income tag is applied despite MOC DOC vs bank amount mismatch.
     */
    public function up(): void
    {
        $col = 'income_tag_mismatch_remark';
        $exists = DB::select("SHOW COLUMNS FROM `income_reconciliation_table` LIKE '{$col}'");
        if (empty($exists)) {
            DB::statement(
                "ALTER TABLE `income_reconciliation_table` ADD COLUMN `{$col}` TEXT NULL COMMENT 'Remark when tagging despite MOC vs bank amount difference'"
            );
        }
    }

    public function down(): void
    {
        $col = 'income_tag_mismatch_remark';
        $exists = DB::select("SHOW COLUMNS FROM `income_reconciliation_table` LIKE '{$col}'");
        if (! empty($exists)) {
            DB::statement("ALTER TABLE `income_reconciliation_table` DROP COLUMN `{$col}`");
        }
    }
};
