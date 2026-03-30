<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Adds bank statement ID and reference number columns to income_reconciliation_table
     * for each payment mode (cash, card/UPI shared, neft, other).
     */
    public function up(): void
    {
        // income_reconciliation_table uses raw DB::statement since it may not have a Schema definition
        $columns = [
            // Cash
            ['cash_bank_id',        'BIGINT UNSIGNED NULL COMMENT "Bank statement ID matched for cash"'],
            ['cash_bank_ref_no',    'VARCHAR(255) NULL COMMENT "Bank reference/transaction ID for cash"'],
            // Card & UPI share the same bank entry
            ['card_upi_bank_id',    'BIGINT UNSIGNED NULL COMMENT "Bank statement ID matched for card/UPI"'],
            ['card_upi_bank_ref_no','VARCHAR(255) NULL COMMENT "Bank reference/transaction ID for card/UPI"'],
            // NEFT
            ['neft_bank_id',        'BIGINT UNSIGNED NULL COMMENT "Bank statement ID matched for NEFT"'],
            ['neft_bank_ref_no',    'VARCHAR(255) NULL COMMENT "Bank reference/transaction ID for NEFT"'],
            // Others
            ['other_bank_id',       'BIGINT UNSIGNED NULL COMMENT "Bank statement ID matched for others"'],
            ['other_bank_ref_no',   'VARCHAR(255) NULL COMMENT "Bank reference/transaction ID for others"'],
        ];

        foreach ($columns as [$col, $definition]) {
            // Only add if column does not already exist
            $exists = DB::select("SHOW COLUMNS FROM `income_reconciliation_table` LIKE '{$col}'");
            if (empty($exists)) {
                DB::statement("ALTER TABLE `income_reconciliation_table` ADD COLUMN `{$col}` {$definition}");
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $columns = [
            'cash_bank_id', 'cash_bank_ref_no',
            'card_upi_bank_id', 'card_upi_bank_ref_no',
            'neft_bank_id', 'neft_bank_ref_no',
            'other_bank_id', 'other_bank_ref_no',
        ];

        foreach ($columns as $col) {
            $exists = DB::select("SHOW COLUMNS FROM `income_reconciliation_table` LIKE '{$col}'");
            if (!empty($exists)) {
                DB::statement("ALTER TABLE `income_reconciliation_table` DROP COLUMN `{$col}`");
            }
        }
    }
};
