<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mocdoc_checkin_reports', function (Blueprint $table) {
            $table->id();
            $table->date('checkin_date')->index();
            $table->string('checkin_time', 20)->nullable();
            $table->string('patient_name', 255)->nullable();
            $table->string('mobile', 30)->nullable();
            $table->date('dob')->nullable();
            $table->string('purpose', 255)->nullable();
            $table->string('ptsource', 255)->nullable();
            $table->string('city', 255)->nullable();
            $table->string('mocdoc_location_key', 50)->index();
            $table->string('mocdoc_location_name', 255)->nullable();
            $table->timestamp('synced_at')->nullable()->index();
            $table->timestamps();

            $table->index(['checkin_date', 'mocdoc_location_key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mocdoc_checkin_reports');
    }
};
