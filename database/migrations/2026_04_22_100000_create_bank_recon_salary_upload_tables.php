<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('bank_recon_salary_uploads')) {
            Schema::create('bank_recon_salary_uploads', function (Blueprint $table) {
                $table->id();
                $table->string('file_name', 512);
                $table->unsignedBigInteger('user_id');
                $table->unsignedInteger('row_count')->default(0);
                $table->unsignedInteger('matched_count')->default(0);
                $table->timestamps();
                $table->index('created_at');
            });
        }

        if (! Schema::hasTable('bank_recon_salary_rows')) {
            Schema::create('bank_recon_salary_rows', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('salary_upload_id');
                $table->unsignedInteger('sheet_row_index')->default(0);
                $table->string('utr', 200)->default('');
                $table->string('utr_normalized', 200)->default('')->index();
                $table->string('serial_no', 32)->nullable();
                $table->string('ec_id', 64)->nullable();
                $table->string('employee_name', 255)->nullable();
                $table->string('designation', 255)->nullable();
                $table->string('branch', 255)->nullable();
                $table->string('employee_category', 64)->nullable();
                $table->decimal('pf', 14, 2)->nullable();
                $table->decimal('esi', 14, 2)->nullable();
                $table->decimal('tds', 14, 2)->nullable();
                $table->decimal('net_paid', 16, 2)->nullable();
                $table->string('credited_date', 64)->nullable();
                $table->unsignedBigInteger('bank_statement_id')->nullable();
                $table->string('match_status', 32)->default('unmatched');
                $table->timestamp('matched_at')->nullable();
                $table->string('match_note', 500)->nullable();
                $table->timestamps();
                $table->index('salary_upload_id');
                $table->index('bank_statement_id');
                $table->foreign('salary_upload_id')
                    ->references('id')->on('bank_recon_salary_uploads')->cascadeOnDelete();
            });
        }

        if (Schema::hasTable('bank_recon_salary_rows') && Schema::hasTable('bank_statements')) {
            // Avoid duplicate FK in case migration re-run after partial success
            try {
                Schema::table('bank_recon_salary_rows', function (Blueprint $table) {
                    $table->foreign('bank_statement_id', 'bank_recon_salary_rows_stmt_fk')
                        ->references('id')->on('bank_statements')->nullOnDelete();
                });
            } catch (\Throwable $e) {
                // FK may already exist
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('bank_recon_salary_rows');
        Schema::dropIfExists('bank_recon_salary_uploads');
    }
};
