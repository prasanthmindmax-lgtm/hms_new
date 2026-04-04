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
        Schema::create('petty_cash_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('petty_cash_id');

            $table->unsignedBigInteger('expense_category_id');

            $table->text('description')->nullable();

            $table->decimal('amount',12,2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('petty_cash_items');
    }
};
