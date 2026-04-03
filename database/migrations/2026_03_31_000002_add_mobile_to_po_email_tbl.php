<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('po_email_tbl', function (Blueprint $table) {
            if (!Schema::hasColumn('po_email_tbl', 'mobile_number')) {
                $table->string('mobile_number', 20)->nullable()->after('cc_emails')
                      ->comment('Mobile / WhatsApp number for notification');
            }
            // Widen menu_type to text so it can hold a JSON array of menu names
            if (Schema::hasColumn('po_email_tbl', 'menu_type')) {
                $table->text('menu_type')->nullable()->change();
            }
        });
    }

    public function down(): void
    {
        Schema::table('po_email_tbl', function (Blueprint $table) {
            if (Schema::hasColumn('po_email_tbl', 'mobile_number')) {
                $table->dropColumn('mobile_number');
            }
        });
    }
};
