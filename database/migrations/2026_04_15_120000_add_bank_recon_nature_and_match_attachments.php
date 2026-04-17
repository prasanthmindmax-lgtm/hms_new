<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('bill_tbl')) {
            Schema::table('bill_tbl', function (Blueprint $table) {
                if (! Schema::hasColumn('bill_tbl', 'br_nature_account_ids')) {
                    $table->string('br_nature_account_ids', 500)->nullable();
                }
                if (! Schema::hasColumn('bill_tbl', 'br_nature_account_names')) {
                    $table->text('br_nature_account_names')->nullable();
                }
            });
        }

        if (Schema::hasTable('bank_statements')) {
            Schema::table('bank_statements', function (Blueprint $table) {
                if (! Schema::hasColumn('bank_statements', 'br_nature_account_ids')) {
                    $table->string('br_nature_account_ids', 500)->nullable();
                }
                if (! Schema::hasColumn('bank_statements', 'br_nature_account_names')) {
                    $table->text('br_nature_account_names')->nullable();
                }
                if (! Schema::hasColumn('bank_statements', 'attachments_json')) {
                    $table->longText('attachments_json')->nullable();
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('bill_tbl')) {
            Schema::table('bill_tbl', function (Blueprint $table) {
                if (Schema::hasColumn('bill_tbl', 'br_nature_account_names')) {
                    $table->dropColumn('br_nature_account_names');
                }
                if (Schema::hasColumn('bill_tbl', 'br_nature_account_ids')) {
                    $table->dropColumn('br_nature_account_ids');
                }
            });
        }

        if (Schema::hasTable('bank_statements')) {
            Schema::table('bank_statements', function (Blueprint $table) {
                if (Schema::hasColumn('bank_statements', 'attachments_json')) {
                    $table->dropColumn('attachments_json');
                }
                if (Schema::hasColumn('bank_statements', 'br_nature_account_names')) {
                    $table->dropColumn('br_nature_account_names');
                }
                if (Schema::hasColumn('bank_statements', 'br_nature_account_ids')) {
                    $table->dropColumn('br_nature_account_ids');
                }
            });
        }
    }
};
