<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_no', 32)->unique();
            $table->unsignedBigInteger('location_id');
            $table->unsignedBigInteger('from_department_id');
            $table->unsignedBigInteger('to_department_id');
            $table->unsignedBigInteger('ticket_category_id');
            $table->string('priority', 32);
            $table->string('subject', 500);
            $table->text('description');
            $table->json('solution')->nullable();
            $table->json('attachments')->nullable();
            $table->string('status', 32)->default('open');
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('status_updated_by')->nullable();
            $table->timestamp('status_updated_at')->nullable();
            $table->index(['to_department_id', 'status']);
            $table->index('created_by');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
