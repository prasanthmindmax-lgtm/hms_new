<?php
/**
 * Run this via: php artisan migrate or execute the SQL below manually.
 * MySQL ALTER statements for discount form: Counselled By (Include/Not Include) and Attachments.
 */

// SQL to run manually if not using Laravel migrate:
/*
ALTER TABLE hms_discount_form
  ADD COLUMN dis_counselled_by_include VARCHAR(500) NULL AFTER dis_counselled_by,
  ADD COLUMN dis_counselled_by_not_include VARCHAR(500) NULL AFTER dis_counselled_by_include,
  ADD COLUMN dis_attachments TEXT NULL COMMENT 'JSON array of file paths' AFTER dis_admin_sign;
*/

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hms_discount_form', function (Blueprint $table) {
            if (!Schema::hasColumn('hms_discount_form', 'dis_counselled_by_include')) {
                $table->string('dis_counselled_by_include', 500)->nullable()->after('dis_counselled_by');
            }
            if (!Schema::hasColumn('hms_discount_form', 'dis_counselled_by_not_include')) {
                $table->string('dis_counselled_by_not_include', 500)->nullable()->after('dis_counselled_by_include');
            }
            if (!Schema::hasColumn('hms_discount_form', 'dis_attachments')) {
                $table->text('dis_attachments')->nullable()->comment('JSON array of file paths')->after('dis_admin_sign');
            }
        });
    }

    public function down(): void
    {
        Schema::table('hms_discount_form', function (Blueprint $table) {
            $table->dropColumn(['dis_counselled_by_include', 'dis_counselled_by_not_include', 'dis_attachments']);
        });
    }
};
