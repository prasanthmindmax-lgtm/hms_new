<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mocdoc_registration_reports', function (Blueprint $table) {
            $table->id();
            $table->string('phid', 100)->unique()->index();
            $table->string('prefix', 30)->nullable()->index();
            $table->string('name', 255)->nullable();
            $table->string('mobile', 30)->nullable();
            $table->string('gender', 20)->nullable();
            $table->string('age', 20)->nullable();
            $table->string('area', 255)->nullable()->index();
            $table->date('reg_date')->nullable()->index();
            $table->timestamp('synced_at')->nullable()->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mocdoc_registration_reports');
    }
};
