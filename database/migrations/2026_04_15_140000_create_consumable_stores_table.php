<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('consumable_stores', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('grn_id');
            $table->string('grn_number', 100);
            $table->unsignedBigInteger('department_id')->nullable();
            $table->text('item_name');
            $table->decimal('quantity', 14, 2)->default(0);
            $table->decimal('unit_price', 14, 2)->default(0);
            $table->timestamps();

            $table->index('grn_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('consumable_stores');
    }
};
