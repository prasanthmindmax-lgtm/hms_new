<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('branch_financial_reports') && ! Schema::hasColumn('branch_financial_reports', 'radiant_ledger_book_files')) {
            Schema::table('branch_financial_reports', function (Blueprint $table) {
                $table->text('radiant_ledger_book_files')->nullable()->after('radiant_collection_files');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('branch_financial_reports') && Schema::hasColumn('branch_financial_reports', 'radiant_ledger_book_files')) {
            Schema::table('branch_financial_reports', function (Blueprint $table) {
                $table->dropColumn('radiant_ledger_book_files');
            });
        }
    }
};
