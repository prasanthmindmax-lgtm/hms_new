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
        Schema::create('ticket_details', function (Blueprint $table) {
            $table->id();
            $table->integer('location_id')->nullable();
            $table->string('ticket_no')->nullable();
            $table->integer('staff_id')->nullable();
            $table->integer('dept_id')->nullable();
            $table->string('priotity_level')->nullable();
            $table->string('assigned_by')->nullable();
            $table->string('ticket_status')->nullable();
            $table->string('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ticket_details');
    }
};
