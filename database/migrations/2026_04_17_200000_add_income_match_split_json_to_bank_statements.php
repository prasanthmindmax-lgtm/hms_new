<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Store JSON metadata for multi-date income tagging (amount splits per collection date).
     */
    public function up(): void
    {
        if (Schema::hasTable('bank_statements') && ! Schema::hasColumn('bank_statements', 'income_match_split_json')) {
            Schema::table('bank_statements', function (Blueprint $table) {
                $table->longText('income_match_split_json')->nullable()->after('income_matched_date');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('bank_statements') && Schema::hasColumn('bank_statements', 'income_match_split_json')) {
            Schema::table('bank_statements', function (Blueprint $table) {
                $table->dropColumn('income_match_split_json');
            });
        }
    }
};
