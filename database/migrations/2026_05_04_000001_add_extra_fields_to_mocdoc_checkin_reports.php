<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mocdoc_checkin_reports', function (Blueprint $table) {
            $table->string('checkinkey', 255)->nullable()->unique()->after('id');
            $table->string('phid', 50)->nullable()->index()->after('checkinkey');
            $table->string('age', 30)->nullable()->after('dob');
            $table->string('gender', 10)->nullable()->after('age');
            $table->string('state', 100)->nullable()->after('city');
            $table->string('bookeddr_name', 255)->nullable()->after('state');
            $table->string('visittype', 50)->nullable()->after('bookeddr_name');
            $table->string('opno', 50)->nullable()->after('visittype');
        });
    }

    public function down(): void
    {
        Schema::table('mocdoc_checkin_reports', function (Blueprint $table) {
            $table->dropColumn(['checkinkey', 'phid', 'age', 'gender', 'state', 'bookeddr_name', 'visittype', 'opno']);
        });
    }
};
