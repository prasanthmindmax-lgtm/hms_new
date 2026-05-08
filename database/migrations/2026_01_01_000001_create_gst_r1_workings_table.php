<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gst_r1_workings', function (Blueprint $table) {
            $table->id();

            // Identifiers
            $table->string('zone')->nullable();
            $table->string('branch');
            $table->string('month');          // e.g. "April 2026"
            $table->year('fin_year')->default(2026); // financial year

            // Under GST 0%
            $table->decimal('gst0_qty',     15, 4)->default(0);
            $table->decimal('gst0_taxable', 15, 4)->default(0);

            // Under GST 5%
            $table->decimal('gst5_qty',     15, 4)->default(0);
            $table->decimal('gst5_taxable', 15, 4)->default(0);
            $table->decimal('gst5_cgst',    15, 4)->default(0);
            $table->decimal('gst5_sgst',    15, 4)->default(0);

            // Under GST 12%
            $table->decimal('gst12_qty',     15, 4)->default(0);
            $table->decimal('gst12_taxable', 15, 4)->default(0);
            $table->decimal('gst12_cgst',    15, 4)->default(0);
            $table->decimal('gst12_sgst',    15, 4)->default(0);

            // Under GST 18%
            $table->decimal('gst18_qty',     15, 4)->default(0);
            $table->decimal('gst18_taxable', 15, 4)->default(0);
            $table->decimal('gst18_cgst',    15, 4)->default(0);
            $table->decimal('gst18_sgst',    15, 4)->default(0);

            // Totals
            $table->decimal('total_pharmacy',  15, 4)->default(0);
            $table->decimal('total_gst',       15, 4)->default(0);
            $table->decimal('exempt_sales',    15, 4)->default(0);
            $table->decimal('total_turnover',  15, 4)->default(0);
            $table->decimal('collection',      15, 4)->default(0);
            $table->decimal('difference',      15, 4)->default(0);

            $table->string('source')->default('manual'); // manual | import
            $table->timestamps();

            $table->index(['zone', 'branch', 'month']);
            $table->unique(['branch', 'month'], 'unique_branch_month');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gst_r1_workings');
    }
};