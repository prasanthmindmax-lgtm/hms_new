<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Bank recon nature + attachments live on bank_statements (with bill_tbl), not bank_bill_matches.
 * Adds columns on bank_statements, copies from bank_bill_matches if present, then drops legacy columns on bank_bill_matches.
 */
return new class extends Migration
{
    public function up(): void
    {
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

        if (
            Schema::hasTable('bank_bill_matches')
            && Schema::hasTable('bank_statements')
            && (Schema::hasColumn('bank_bill_matches', 'br_nature_account_ids')
                || Schema::hasColumn('bank_bill_matches', 'br_nature_account_names')
                || Schema::hasColumn('bank_bill_matches', 'attachments_json'))
        ) {
            $rows = DB::table('bank_bill_matches')
                ->select('bank_statement_id', 'br_nature_account_ids', 'br_nature_account_names', 'attachments_json')
                ->whereNotNull('bank_statement_id')
                ->get();

            foreach ($rows as $r) {
                $sid = (int) $r->bank_statement_id;
                if ($sid < 1) {
                    continue;
                }
                $update = [];
                if (Schema::hasColumn('bank_statements', 'br_nature_account_ids') && $r->br_nature_account_ids !== null && $r->br_nature_account_ids !== '') {
                    $update['br_nature_account_ids'] = $r->br_nature_account_ids;
                }
                if (Schema::hasColumn('bank_statements', 'br_nature_account_names') && $r->br_nature_account_names !== null && $r->br_nature_account_names !== '') {
                    $update['br_nature_account_names'] = $r->br_nature_account_names;
                }
                if (Schema::hasColumn('bank_statements', 'attachments_json') && $r->attachments_json !== null && $r->attachments_json !== '') {
                    $update['attachments_json'] = $r->attachments_json;
                }
                if ($update !== []) {
                    DB::table('bank_statements')->where('id', $sid)->update($update);
                }
            }

            Schema::table('bank_bill_matches', function (Blueprint $table) {
                if (Schema::hasColumn('bank_bill_matches', 'attachments_json')) {
                    $table->dropColumn('attachments_json');
                }
                if (Schema::hasColumn('bank_bill_matches', 'br_nature_account_names')) {
                    $table->dropColumn('br_nature_account_names');
                }
                if (Schema::hasColumn('bank_bill_matches', 'br_nature_account_ids')) {
                    $table->dropColumn('br_nature_account_ids');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('bank_bill_matches')) {
            Schema::table('bank_bill_matches', function (Blueprint $table) {
                if (! Schema::hasColumn('bank_bill_matches', 'br_nature_account_ids')) {
                    $table->string('br_nature_account_ids', 500)->nullable();
                }
                if (! Schema::hasColumn('bank_bill_matches', 'br_nature_account_names')) {
                    $table->text('br_nature_account_names')->nullable();
                }
                if (! Schema::hasColumn('bank_bill_matches', 'attachments_json')) {
                    $table->longText('attachments_json')->nullable();
                }
            });
        }

        if (
            Schema::hasTable('bank_statements')
            && Schema::hasTable('bank_bill_matches')
            && Schema::hasColumn('bank_statements', 'attachments_json')
        ) {
            $stmts = DB::table('bank_statements')
                ->select('id', 'br_nature_account_ids', 'br_nature_account_names', 'attachments_json')
                ->where(function ($q) {
                    $q->whereNotNull('attachments_json')
                        ->orWhereNotNull('br_nature_account_ids')
                        ->orWhereNotNull('br_nature_account_names');
                })
                ->get();

            foreach ($stmts as $s) {
                $match = DB::table('bank_bill_matches')
                    ->where('bank_statement_id', $s->id)
                    ->orderByDesc('id')
                    ->first();
                if (! $match) {
                    continue;
                }
                $u = [];
                if (Schema::hasColumn('bank_bill_matches', 'br_nature_account_ids') && $s->br_nature_account_ids !== null) {
                    $u['br_nature_account_ids'] = $s->br_nature_account_ids;
                }
                if (Schema::hasColumn('bank_bill_matches', 'br_nature_account_names') && $s->br_nature_account_names !== null) {
                    $u['br_nature_account_names'] = $s->br_nature_account_names;
                }
                if (Schema::hasColumn('bank_bill_matches', 'attachments_json') && $s->attachments_json !== null) {
                    $u['attachments_json'] = $s->attachments_json;
                }
                if ($u !== []) {
                    DB::table('bank_bill_matches')->where('id', $match->id)->update($u);
                }
            }
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
