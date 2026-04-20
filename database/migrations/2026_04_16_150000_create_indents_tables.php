<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('indents', function (Blueprint $table) {
            $table->id();
            $table->string('indent_no', 40)->unique();

            $table->unsignedBigInteger('company_id')->nullable();
            $table->unsignedBigInteger('zone_id')->nullable();
            $table->unsignedBigInteger('branch_id');

            $table->unsignedBigInteger('from_department_id')->nullable();
            $table->unsignedBigInteger('to_department_id')->nullable();

            $table->text('purpose')->nullable();
            $table->date('required_date')->nullable();
            $table->text('remarks')->nullable();

            $table->string('status', 32)->default('pending');

            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();

            $table->unsignedBigInteger('rejected_by')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->text('rejection_reason')->nullable();

            $table->unsignedBigInteger('last_status_by')->nullable();

            $table->timestamps();

            $table->index(['from_department_id', 'status']);
            $table->index(['to_department_id', 'status']);
            $table->index(['branch_id', 'status']);
            $table->index('created_at');
        });

        Schema::create('indent_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('indent_id')->constrained('indents')->cascadeOnDelete();

            $table->unsignedBigInteger('consumable_store_id');
            $table->string('item_name', 500);
            $table->string('item_category', 191)->nullable();

            $table->decimal('quantity_requested', 14, 2);
            $table->decimal('quantity_issued', 14, 2)->default(0);

            $table->timestamps();

            $table->index('consumable_store_id');
        });

        Schema::create('indent_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('indent_id')->constrained('indents')->cascadeOnDelete();

            $table->unsignedBigInteger('user_id');
            $table->string('action', 64);
            $table->json('payload')->nullable();

            $table->timestamp('created_at')->useCurrent();

            $table->index(['indent_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('indent_histories');
        Schema::dropIfExists('indent_lines');
        Schema::dropIfExists('indents');
    }
};
