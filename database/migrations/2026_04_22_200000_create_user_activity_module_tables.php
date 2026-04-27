<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_activity_sessions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->index();
            $table->string('laravel_session_fingerprint', 64)->nullable()->index();
            $table->timestamp('started_at');
            $table->timestamp('last_seen_at')->nullable();
            $table->timestamp('ended_at')->nullable();
            $table->unsignedInteger('duration_seconds')->nullable();
            $table->string('ip_address', 64)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();
        });

        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->index();
            $table->string('username', 100)->nullable()->index();
            $table->string('user_fullname', 255)->nullable();
            $table->string('user_email', 255)->nullable();
            $table->string('activity_module', 128)->nullable()->index();
            $table->unsignedBigInteger('user_activity_session_id')->nullable()->index();
            $table->string('type', 32)->index();
            $table->string('http_method', 12)->nullable();
            $table->string('route_name', 256)->nullable();
            $table->string('path', 512);
            $table->string('url_query', 512)->nullable();
            $table->json('request_snapshot')->nullable();
            $table->string('label', 512)->nullable();
            $table->unsignedInteger('records_count')->default(0);
            $table->unsignedInteger('action_duration_ms')->nullable();
            $table->unsignedInteger('server_duration_ms')->nullable(); 
            $table->timestamp('created_at')->useCurrent();
            $table->index(['user_id', 'type', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
        Schema::dropIfExists('user_activity_sessions');
    }
};
