<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('bank_bill_matches')) {
            return;
        }
        Schema::table('bank_bill_matches', function (Blueprint $table) {
            if (!Schema::hasColumn('bank_bill_matches', 'bill_pay_id')) {
                $table->unsignedBigInteger('bill_pay_id')->nullable()->after('bill_id');
            }
        });
    }

    public function down(): void
    {
        if (Schema::hasTable('bank_bill_matches') && Schema::hasColumn('bank_bill_matches', 'bill_pay_id')) {
            Schema::table('bank_bill_matches', function (Blueprint $table) {
                $table->dropColumn('bill_pay_id');
            });
        }
    }
};
