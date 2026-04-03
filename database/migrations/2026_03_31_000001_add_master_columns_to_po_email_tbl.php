<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('po_email_tbl', function (Blueprint $table) {
            if (!Schema::hasColumn('po_email_tbl', 'label')) {
                $table->string('label')->nullable()->after('email')->comment('Friendly name for this email config');
            }
            if (!Schema::hasColumn('po_email_tbl', 'to_email')) {
                $table->string('to_email')->nullable()->after('label')->comment('Primary TO recipient');
            }
            if (!Schema::hasColumn('po_email_tbl', 'cc_emails')) {
                $table->text('cc_emails')->nullable()->after('to_email')->comment('JSON array of CC email addresses');
            }
            if (!Schema::hasColumn('po_email_tbl', 'menu_type')) {
                $table->string('menu_type')->default('Purchase Order')->after('cc_emails')
                      ->comment('Module this email config belongs to: Purchase Order, Quotation, Bill');
            }
            if (!Schema::hasColumn('po_email_tbl', 'status')) {
                $table->tinyInteger('status')->default(1)->after('menu_type')
                      ->comment('1 = Active, 0 = Inactive');
            }
        });
    }

    public function down(): void
    {
        Schema::table('po_email_tbl', function (Blueprint $table) {
            $cols = ['label', 'to_email', 'cc_emails', 'menu_type', 'status'];
            foreach ($cols as $col) {
                if (Schema::hasColumn('po_email_tbl', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
