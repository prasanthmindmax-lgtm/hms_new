<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('bank_reconciliation_accounts')) {
            Schema::create('bank_reconciliation_accounts', function (Blueprint $table) {
                $table->id();
                $table->string('account_number', 64);
                $table->string('bank_name', 191)->nullable();
                $table->string('branch_name', 191)->nullable();
                $table->string('ifsc_code', 32)->nullable();
                $table->string('account_holder_name', 191)->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();
                $table->unique('account_number');
            });
        }

        if (! Schema::hasTable('bank_statement_upload_batches')) {
            Schema::create('bank_statement_upload_batches', function (Blueprint $table) {
                $table->id();
                $table->foreignId('bank_account_id')->constrained('bank_reconciliation_accounts')->cascadeOnDelete();
                $table->string('upload_batch_id', 64)->unique();
                $table->string('original_file_name', 255);
                $table->string('stored_file_name', 255);
                $table->unsignedInteger('rows_imported')->default(0);
                $table->unsignedInteger('duplicates')->default(0);
                $table->unsignedInteger('skipped')->default(0);
                $table->unsignedBigInteger('user_id')->nullable();
                $table->foreign('user_id')
                    ->references('id')
                    ->on('users')
                    ->nullOnDelete();
                $table->timestamps();
            });
        }

        if (Schema::hasTable('bank_statements') && ! Schema::hasColumn('bank_statements', 'bank_account_id')) {
            Schema::table('bank_statements', function (Blueprint $table) {
                $table->unsignedBigInteger('bank_account_id')->nullable()->after('user_id');
                $table->foreign('bank_account_id')
                    ->references('id')
                    ->on('bank_reconciliation_accounts')
                    ->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('bank_statement_upload_batches');

        if (Schema::hasTable('bank_statements') && Schema::hasColumn('bank_statements', 'bank_account_id')) {
            Schema::table('bank_statements', function (Blueprint $table) {
                $table->dropForeign(['bank_account_id']);
                $table->dropColumn('bank_account_id');
            });
        }

        Schema::dropIfExists('bank_reconciliation_accounts');
    }
};
