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
            if (! Schema::hasColumn('bank_statements', 'pos_match_status')) {
                $table->string('pos_match_status', 32)->default('pos_unmatched')->index();
                $table->unsignedBigInteger('pos_settlement_account_id')->nullable()->index();
                $table->string('pos_match_against', 255)->nullable();
                $table->string('pos_extracted_key', 32)->nullable();
                $table->string('pos_matched_mid', 191)->nullable();
                $table->string('pos_matched_merchant', 255)->nullable();
                $table->date('pos_matched_settlement_date')->nullable();
                $table->unsignedBigInteger('pos_matched_by')->nullable()->index();
                $table->timestamp('pos_matched_at')->nullable();
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('bank_statements')) {
            return;
        }

        $cols = [
            'pos_matched_at',
            'pos_matched_by',
            'pos_matched_settlement_date',
            'pos_matched_merchant',
            'pos_matched_mid',
            'pos_extracted_key',
            'pos_match_against',
            'pos_settlement_account_id',
            'pos_match_status',
        ];
        $existing = array_values(array_filter($cols, fn ($c) => Schema::hasColumn('bank_statements', $c)));
        if ($existing !== []) {
            Schema::table('bank_statements', function (Blueprint $table) use ($existing) {
                $table->dropColumn($existing);
            });
        }
    }
};
