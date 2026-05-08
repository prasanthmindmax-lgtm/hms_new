<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payment_requests', function (Blueprint $table) {
            $table->unsignedBigInteger('bill_id')->nullable()->after('purchase_order_id');
            $table->decimal('bill_total_snapshot', 14, 2)->nullable()->after('po_total_snapshot');
            $table->decimal('bill_balance_snapshot', 14, 2)->nullable()->after('bill_total_snapshot');
            $table->index('bill_id');
        });
    }

    public function down(): void
    {
        Schema::table('payment_requests', function (Blueprint $table) {
            $table->dropColumn('bill_id');
            $table->dropColumn('bill_total_snapshot');
            $table->dropColumn('bill_balance_snapshot');
        });
    }
};
