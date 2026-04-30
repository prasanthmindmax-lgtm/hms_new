<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('bank_statements')) {
            return;
        }
        Schema::table('bank_statements', function (Blueprint $table) {
            if (! Schema::hasColumn('bank_statements', 'salary_matched_by')) {
                $table->unsignedBigInteger('salary_matched_by')->nullable();
            }
            if (! Schema::hasColumn('bank_statements', 'salary_matched_at')) {
                $table->timestamp('salary_matched_at')->nullable();
            }
            if (! Schema::hasColumn('bank_statements', 'bank_recon_salary_row_id')) {
                $table->unsignedBigInteger('bank_recon_salary_row_id')->nullable()->index();
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('bank_statements')) {
            return;
        }
        Schema::table('bank_statements', function (Blueprint $table) {
            if (Schema::hasColumn('bank_statements', 'bank_recon_salary_row_id')) {
                $table->dropIndex(['bank_recon_salary_row_id']);
            }
        });
        Schema::table('bank_statements', function (Blueprint $table) {
            if (Schema::hasColumn('bank_statements', 'salary_matched_by')) {
                $table->dropColumn('salary_matched_by');
            }
            if (Schema::hasColumn('bank_statements', 'salary_matched_at')) {
                $table->dropColumn('salary_matched_at');
            }
            if (Schema::hasColumn('bank_statements', 'bank_recon_salary_row_id')) {
                $table->dropColumn('bank_recon_salary_row_id');
            }
        });
    }
};
