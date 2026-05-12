<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('licence_document_catalog', function (Blueprint $table) {
            $table->boolean('renewal_date_required')->default(true)->after('is_active');
        });
    }

    public function down(): void
    {
        Schema::table('licence_document_catalog', function (Blueprint $table) {
            $table->dropColumn('renewal_date_required');
        });
    }
};
