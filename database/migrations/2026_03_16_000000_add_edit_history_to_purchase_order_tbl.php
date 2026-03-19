<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('purchase_order_tbl', function (Blueprint $table) {
            $table->json('edit_history')->nullable()->after('documents');
        });
    }

    public function down(): void
    {
        Schema::table('purchase_order_tbl', function (Blueprint $table) {
            $table->dropColumn('edit_history');
        });
    }
};
