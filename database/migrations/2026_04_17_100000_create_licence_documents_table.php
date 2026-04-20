<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('licence_documents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('branch_id');
            $table->unsignedTinyInteger('level');
            $table->string('document_key', 120);
            $table->string('file_path', 500)->nullable();
            $table->string('original_filename', 500)->nullable();
            $table->date('renewal_date')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            $table->unique(['branch_id', 'level', 'document_key'], 'licence_documents_unique_slot');
            $table->index(['branch_id', 'level']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('licence_documents');
    }
};
