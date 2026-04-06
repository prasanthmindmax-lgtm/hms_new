<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Keyword stored per bank line so Radiant cash-pickup alerts can match when
     * the narration does not follow the usual BY CASH + location pattern.
     */
    public function up(): void
    {
        $col = 'radiant_match_against';
        $exists = DB::select("SHOW COLUMNS FROM `bank_statements` LIKE '{$col}'");
        if (empty($exists)) {
            DB::statement(
                "ALTER TABLE `bank_statements` ADD COLUMN `{$col}` VARCHAR(255) NULL ".
                "COMMENT 'Radiant pickup location/keyword to match this row against alert search'"
            );
        }
    }

    public function down(): void
    {
        $col = 'radiant_match_against';
        $exists = DB::select("SHOW COLUMNS FROM `bank_statements` LIKE '{$col}'");
        if (! empty($exists)) {
            DB::statement("ALTER TABLE `bank_statements` DROP COLUMN `{$col}`");
        }
    }
};
