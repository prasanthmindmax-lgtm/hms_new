<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tbl_quotation', function (Blueprint $table) {
            if (!Schema::hasColumn('tbl_quotation', 'edit_history')) {
                $table->json('edit_history')->nullable()->after('updated_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('tbl_quotation', function (Blueprint $table) {
            if (Schema::hasColumn('tbl_quotation', 'edit_history')) {
                $table->dropColumn('edit_history');
            }
        });
    }
};
