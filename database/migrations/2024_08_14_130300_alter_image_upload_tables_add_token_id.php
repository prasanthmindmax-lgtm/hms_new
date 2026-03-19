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
        Schema::table('image_upload_tables', function (Blueprint $table) {
            $table->string('ticket_id')->nullable()->after('imgName');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('image_upload_tables', function (Blueprint $table) {
            // in here i want to check column_one and column_two exists or not
            $table->dropColumn('ticket_id');
        });
    }
};
