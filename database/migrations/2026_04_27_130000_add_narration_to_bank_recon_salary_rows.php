<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('bank_recon_salary_rows') && ! Schema::hasColumn('bank_recon_salary_rows', 'narration')) {
            Schema::table('bank_recon_salary_rows', function (Blueprint $table) {
                $table->text('narration')->nullable()->after('credited_date');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('bank_recon_salary_rows') && Schema::hasColumn('bank_recon_salary_rows', 'narration')) {
            Schema::table('bank_recon_salary_rows', function (Blueprint $table) {
                $table->dropColumn('narration');
            });
        }
    }
};
