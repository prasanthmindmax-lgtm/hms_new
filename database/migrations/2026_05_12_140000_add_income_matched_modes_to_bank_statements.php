<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Store which income reconciliation mode(s) each bank line was tagged with,
     * so multiple statements on the same date can be summed into one income row.
     */
    public function up(): void
    {
        if (Schema::hasTable('bank_statements') && ! Schema::hasColumn('bank_statements', 'income_matched_modes')) {
            Schema::table('bank_statements', function (Blueprint $table) {
                $table->json('income_matched_modes')->nullable()->after('income_matched_date');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('bank_statements') && Schema::hasColumn('bank_statements', 'income_matched_modes')) {
            Schema::table('bank_statements', function (Blueprint $table) {
                $table->dropColumn('income_matched_modes');
            });
        }
    }
};
