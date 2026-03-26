<?php
// File: database/migrations/xxxx_xx_xx_create_deadline_notifications_table.php
// Run: php artisan migrate

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('deadline_notifications', function (Blueprint $table) {
            $table->id();
            $table->string('type');                  // bill | po | quotation
            $table->unsignedBigInteger('record_id'); // id from the source table
            $table->string('record_number')->nullable(); // bill_gen_number / po_number / quotation_number
            $table->string('recipient_email')->nullable();
            $table->string('recipient_mobile')->nullable();
            $table->string('recipient_name')->nullable();
            $table->enum('channel', ['sms', 'email', 'web']);
            $table->enum('status', ['pending', 'sent', 'failed'])->default('pending');
            $table->text('message')->nullable();
            $table->text('error')->nullable();
            $table->date('due_date')->nullable();
            $table->enum('due_status', ['due_today', 'overdue']); // due today or already passed
            $table->timestamps();

            // Prevent duplicate notifications per record+channel per day
            $table->unique(['type', 'record_id', 'channel', 'due_status'], 'unique_notif');
        });

        // Web push notifications (bell icon in UI)
        Schema::create('web_notifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable(); // null = broadcast to all
            $table->string('title');
            $table->text('message');
            $table->string('type');        // bill | po | quotation
            $table->unsignedBigInteger('record_id')->nullable();
            $table->string('record_number')->nullable();
            $table->string('url')->nullable(); // link to open when clicked
            $table->boolean('is_read')->default(false);
            $table->date('due_date')->nullable();
            $table->enum('due_status', ['due_today', 'overdue']);
            $table->timestamps();

            $table->index(['user_id', 'is_read']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('deadline_notifications');
        Schema::dropIfExists('web_notifications');
    }
};