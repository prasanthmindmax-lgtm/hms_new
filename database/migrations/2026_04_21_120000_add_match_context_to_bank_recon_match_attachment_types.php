<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * bill = expense bill match only; income = income tag only; both = either flow.
     */
    public function up(): void
    {
        if (! Schema::hasTable('bank_recon_match_attachment_types')) {
            return;
        }
        if (! Schema::hasColumn('bank_recon_match_attachment_types', 'match_context')) {
            Schema::table('bank_recon_match_attachment_types', function (Blueprint $table) {
                $table->string('match_context', 16)->default('both')->after('name');
            });
            DB::table('bank_recon_match_attachment_types')->update(['match_context' => 'both']);
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('bank_recon_match_attachment_types') && Schema::hasColumn('bank_recon_match_attachment_types', 'match_context')) {
            Schema::table('bank_recon_match_attachment_types', function (Blueprint $table) {
                $table->dropColumn('match_context');
            });
        }
    }
};
