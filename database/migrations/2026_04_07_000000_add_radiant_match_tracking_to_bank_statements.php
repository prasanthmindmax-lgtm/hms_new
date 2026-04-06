<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Radiant cash-pickup reconciliation display + linking (parallel to income tag columns).
     */
    public function up(): void
    {
        $columns = [
            ['radiant_match_status', "VARCHAR(30) NOT NULL DEFAULT 'radiant_unmatched' COMMENT 'radiant_matched or radiant_unmatched'"],
            ['radiant_cash_pickup_id', "BIGINT UNSIGNED NULL COMMENT 'radiant_cash_pickups.id'"],
            ['radiant_matched_location', 'VARCHAR(255) NULL'],
            ['radiant_matched_pickup_date', 'VARCHAR(40) NULL'],
            ['radiant_matched_by', 'BIGINT UNSIGNED NULL'],
            ['radiant_matched_at', 'TIMESTAMP NULL'],
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
        foreach ([
            'radiant_matched_at',
            'radiant_matched_by',
            'radiant_matched_pickup_date',
            'radiant_matched_location',
            'radiant_cash_pickup_id',
            'radiant_match_status',
        ] as $col) {
            $exists = DB::select("SHOW COLUMNS FROM `bank_statements` LIKE '{$col}'");
            if (! empty($exists)) {
                DB::statement("ALTER TABLE `bank_statements` DROP COLUMN `{$col}`");
            }
        }
    }
};
