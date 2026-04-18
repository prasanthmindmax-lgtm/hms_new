<?php

use Database\Seeders\LicenceDocumentCatalogSeeder;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('licence_document_catalog', function (Blueprint $table) {
            $table->id();
            $table->string('document_key', 120);
            $table->string('label', 500);
            $table->unsignedTinyInteger('level');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['document_key', 'level'], 'licence_document_catalog_key_level_unique');
            $table->index(['level']);
            $table->index('is_active');
        });

        if (DB::table('licence_document_catalog')->count() > 0) {
            return;
        }

        $now = now();
        $rows = LicenceDocumentCatalogSeeder::defaultRows();
        foreach ($rows as &$row) {
            $row['created_at'] = $now;
            $row['updated_at'] = $now;
        }
        unset($row);

        foreach (array_chunk($rows, 50) as $chunk) {
            DB::table('licence_document_catalog')->insert($chunk);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('licence_document_catalog');
    }
};
