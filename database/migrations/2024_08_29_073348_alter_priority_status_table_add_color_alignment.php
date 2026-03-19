<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('ticket_priority', function (Blueprint $table) {
            $table->string('priority_color')->after('priority_name')->change();
        });

        Schema::table('ticket_status_master', function (Blueprint $table) {
            $table->longText('status_color')->after('status_name')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ticket_status_master', function (Blueprint $table) {
            $table->dropColumn('status_color');
        });
    }
};
