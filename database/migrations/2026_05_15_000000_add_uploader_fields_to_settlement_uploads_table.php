<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('settlement_uploads', function (Blueprint $table) {
            if (! Schema::hasColumn('settlement_uploads', 'uploaded_by')) {
                $table->unsignedBigInteger('uploaded_by')->nullable()->index()->after('error_message');
                $table->string('uploaded_by_name', 191)->nullable()->after('uploaded_by');
                $table->string('uploaded_by_email', 191)->nullable()->after('uploaded_by_name');
                $table->string('uploaded_by_username', 191)->nullable()->after('uploaded_by_email');
                $table->string('uploaded_ip', 45)->nullable()->after('uploaded_by_username');
                $table->string('upload_user_agent', 512)->nullable()->after('uploaded_ip');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('settlement_uploads')) {
            return;
        }

        $cols = [
            'upload_user_agent',
            'uploaded_ip',
            'uploaded_by_username',
            'uploaded_by_email',
            'uploaded_by_name',
            'uploaded_by',
        ];
        $existing = array_values(array_filter($cols, fn ($c) => Schema::hasColumn('settlement_uploads', $c)));
        if ($existing !== []) {
            Schema::table('settlement_uploads', function (Blueprint $table) use ($existing) {
                $table->dropColumn($existing);
            });
        }
    }
};
