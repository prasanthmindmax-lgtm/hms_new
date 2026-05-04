<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bill_tbl', function (Blueprint $table) {
            $table->unsignedBigInteger('payment_request_id')->nullable()->after('purchase_id');
            $table->string('bill_pr_link_mode', 32)->nullable()->after('payment_request_id');
        });
    }

    public function down(): void
    {
        Schema::table('bill_tbl', function (Blueprint $table) {
            $table->dropColumn(['payment_request_id', 'bill_pr_link_mode']);
        });
    }
};
