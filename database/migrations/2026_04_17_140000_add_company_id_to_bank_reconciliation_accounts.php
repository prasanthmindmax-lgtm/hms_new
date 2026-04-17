<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('bank_reconciliation_accounts')) {
            return;
        }

        if (! Schema::hasColumn('bank_reconciliation_accounts', 'company_id')) {
            Schema::table('bank_reconciliation_accounts', function (Blueprint $table) {
                $table->unsignedBigInteger('company_id')->nullable()->after('id');
            });
        }

        if (Schema::hasTable('company_tbl') && Schema::hasColumn('bank_reconciliation_accounts', 'company_id')) {
            $first = DB::table('company_tbl')->orderBy('id')->value('id');
            if ($first) {
                DB::table('bank_reconciliation_accounts')
                    ->whereNull('company_id')
                    ->update(['company_id' => $first]);
            }
        }

        if (Schema::hasTable('company_tbl') && Schema::hasColumn('bank_reconciliation_accounts', 'company_id')) {
            try {
                Schema::table('bank_reconciliation_accounts', function (Blueprint $table) {
                    $table->foreign('company_id')
                        ->references('id')
                        ->on('company_tbl')
                        ->nullOnDelete();
                });
            } catch (\Throwable $e) {
            }
        }

        if (Schema::hasColumn('bank_reconciliation_accounts', 'account_number')) {
            try {
                Schema::table('bank_reconciliation_accounts', function (Blueprint $table) {
                    $table->dropUnique(['account_number']);
                });
            } catch (\Throwable $e) {
            }
        }

        if (Schema::hasColumn('bank_reconciliation_accounts', 'company_id')
            && ! $this->indexExists('bank_reconciliation_accounts', 'bank_recon_acc_company_account_idx')) {
            Schema::table('bank_reconciliation_accounts', function (Blueprint $table) {
                $table->index(['company_id', 'account_number'], 'bank_recon_acc_company_account_idx');
            });
        }
    }

    private function indexExists(string $table, string $name): bool
    {
        $conn = Schema::getConnection();
        $db = $conn->getDatabaseName();
        $row = $conn->selectOne(
            'SELECT COUNT(1) AS c FROM information_schema.statistics WHERE table_schema = ? AND table_name = ? AND index_name = ?',
            [$db, $table, $name]
        );

        return $row && (int) ($row->c ?? 0) > 0;
    }

    public function down(): void
    {
        if (! Schema::hasTable('bank_reconciliation_accounts')) {
            return;
        }

        if (Schema::hasColumn('bank_reconciliation_accounts', 'company_id')) {
            try {
                Schema::table('bank_reconciliation_accounts', function (Blueprint $table) {
                    $table->dropForeign(['company_id']);
                });
            } catch (\Throwable $e) {
            }
            try {
                Schema::table('bank_reconciliation_accounts', function (Blueprint $table) {
                    $table->dropIndex('bank_recon_acc_company_account_idx');
                });
            } catch (\Throwable $e) {
            }
            Schema::table('bank_reconciliation_accounts', function (Blueprint $table) {
                $table->dropColumn('company_id');
            });
        }

        if (Schema::hasColumn('bank_reconciliation_accounts', 'account_number')) {
            try {
                Schema::table('bank_reconciliation_accounts', function (Blueprint $table) {
                    $table->unique('account_number');
                });
            } catch (\Throwable $e) {
            }
        }
    }
};
