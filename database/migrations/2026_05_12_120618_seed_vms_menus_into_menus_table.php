<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Seed VMS module menus into the menus table.
     * This enables per-user VMS access via the Access Master.
     */
    public function up(): void
    {
        // Skip if VMS parent menu already exists
        if (DB::table('menus')->where('route', 'vms/dashboard')->exists()) {
            return;
        }

        $now = now();

        // ── VMS Parent (main menu) ─────────────────────────────────────
        $parentId = DB::table('menus')->insertGetId([
            'menu_name'  => 'Visitor Management',
            'route'      => 'vms/dashboard',
            'icon'       => 'qrcode.png',           // will use ti-qrcode in sidebar
            'active_ids' => 'vms_color',
            'main_menu'  => 1,
            'dropdown'   => 1,
            'sub_menus'  => 0,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // ── VMS Sub-menus ──────────────────────────────────────────────
        $subMenus = [
            ['menu_name' => 'VMS Dashboard',      'route' => 'vms/dashboard',   'icon' => 'dashboard.png'],
            ['menu_name' => 'Approvals',           'route' => 'vms/approvals',   'icon' => 'checklist.png'],
            ['menu_name' => 'Active Visitors',     'route' => 'vms/active',      'icon' => 'users.png'],
            ['menu_name' => 'Visitor History',     'route' => 'vms/history',     'icon' => 'history.png'],
            ['menu_name' => 'Pharma Vendors',      'route' => 'vms/pharma',      'icon' => 'pill.png'],
            ['menu_name' => 'Non-Pharma Vendors',  'route' => 'vms/non-pharma',  'icon' => 'briefcase.png'],
            ['menu_name' => 'Blacklisted',         'route' => 'vms/blacklist',   'icon' => 'ban.png'],
            ['menu_name' => 'QR Management',       'route' => 'vms/qr',          'icon' => 'qrcode.png'],
            ['menu_name' => 'VMS Reports',         'route' => 'vms/reports',     'icon' => 'chart-bar.png'],
            ['menu_name' => 'VMS Settings',        'route' => 'vms/settings',    'icon' => 'settings.png'],
        ];

        foreach ($subMenus as $sub) {
            DB::table('menus')->insert([
                'menu_name'  => $sub['menu_name'],
                'route'      => $sub['route'],
                'icon'       => $sub['icon'],
                'active_ids' => 'vms_color',
                'main_menu'  => 0,
                'dropdown'   => 0,
                'sub_menus'  => $parentId,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    public function down(): void
    {
        DB::table('menus')->where('active_ids', 'vms_color')->delete();
    }
};
