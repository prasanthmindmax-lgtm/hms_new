<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('settlement_uploads')) {
            return;
        }

        if (! Schema::hasColumn('settlement_uploads', 'duplicate_accounts_skipped')) {
            Schema::table('settlement_uploads', function (Blueprint $table) {
                $table->unsignedInteger('duplicate_accounts_skipped')->default(0)->after('total_accounts');
            });
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('settlement_uploads')
            || ! Schema::hasColumn('settlement_uploads', 'duplicate_accounts_skipped')) {
            return;
        }

        Schema::table('settlement_uploads', function (Blueprint $table) {
            $table->dropColumn('duplicate_accounts_skipped');
        });
    }
};
