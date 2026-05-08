<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('gst_r1_workings', function (Blueprint $table) {
            // Add state column (geographical state: Tamil Nadu, Karnataka, etc.)
            // zone will remain for business-zone (from tblzones)
            if (!Schema::hasColumn('gst_r1_workings', 'state')) {
                $table->string('state', 150)->nullable()->after('zone');
            }
        });

        // Migrate existing data: zone values that are state names → move to state, clear zone
        $statePatterns = ['Tamil Nadu', 'Andhra Pradesh', 'Kerala', 'Karnataka', 'Telangana',
                          'ISWARY', 'Pondicherry', 'Puducherry'];

        foreach ($statePatterns as $state) {
            DB::table('gst_r1_workings')
                ->where('zone', 'like', "%{$state}%")
                ->update(['state' => DB::raw('zone'), 'zone' => null]);
        }
    }

    public function down(): void
    {
        // Restore: move state back to zone
        DB::table('gst_r1_workings')
            ->whereNotNull('state')
            ->whereNull('zone')
            ->update(['zone' => DB::raw('state'), 'state' => null]);

        Schema::table('gst_r1_workings', function (Blueprint $table) {
            if (Schema::hasColumn('gst_r1_workings', 'state')) {
                $table->dropColumn('state');
            }
        });
    }
};
