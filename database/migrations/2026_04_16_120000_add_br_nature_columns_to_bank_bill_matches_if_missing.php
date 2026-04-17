<?php

use Illuminate\Database\Migrations\Migration;

/**
 * Superseded by 2026_04_17: bank recon columns belong on bank_statements (and bill_tbl), not bank_bill_matches.
 * Left as no-op so migration order stays valid for databases that already ran the old version.
 */
return new class extends Migration
{
    public function up(): void
    {
        //
    }

    public function down(): void
    {
        //
    }
};
