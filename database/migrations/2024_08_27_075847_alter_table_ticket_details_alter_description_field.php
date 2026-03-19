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
        Schema::table('ticket_details', function (Blueprint $table) {
            $table->longText('description')->change()->nullable();
        });

        Schema::table('ticket_activities', function (Blueprint $table) {
            $table->longText('description')->change()->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ticket_details', function (Blueprint $table) {
            $table->dropColumn('description');
        });

        Schema::table('ticket_activities', function (Blueprint $table) {
            $table->dropColumn('description');
        });
    }
};
