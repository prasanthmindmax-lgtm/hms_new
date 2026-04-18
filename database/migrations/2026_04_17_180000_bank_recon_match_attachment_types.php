<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Master list of document types for bank–bill match attachments (PO, Quotation, …).
     */
    public function up(): void
    {
        if (! Schema::hasTable('bank_recon_match_attachment_types')) {
            Schema::create('bank_recon_match_attachment_types', function (Blueprint $table) {
                $table->id();
                $table->string('name', 191);
                $table->unsignedSmallInteger('sort_order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->string('sample_file_path', 512)->nullable()->comment('Optional sample/template file in public storage');
                $table->timestamps();
            });
        }

        if (Schema::hasTable('bank_recon_match_attachment_types')) {
            $count = DB::table('bank_recon_match_attachment_types')->count();
            if ($count === 0) {
                $defaults = [
                    ['name' => 'PO', 'sort_order' => 10],
                    ['name' => 'Quotation', 'sort_order' => 20],
                    ['name' => 'Invoice', 'sort_order' => 30],
                    ['name' => 'Delivery challan', 'sort_order' => 40],
                    ['name' => 'Contract', 'sort_order' => 50],
                    ['name' => 'Payment proof', 'sort_order' => 60],
                    ['name' => 'Email / approval', 'sort_order' => 70],
                    ['name' => 'Other', 'sort_order' => 100],
                ];
                $now = now();
                foreach ($defaults as $row) {
                    DB::table('bank_recon_match_attachment_types')->insert([
                        'name' => $row['name'],
                        'sort_order' => $row['sort_order'],
                        'is_active' => true,
                        'sample_file_path' => null,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);
                }
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('bank_recon_match_attachment_types');
    }
};
