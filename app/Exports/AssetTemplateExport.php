<?php

namespace App\Exports;

use App\Models\AssetCategory;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class AssetTemplateExport implements FromArray, WithHeadings
{
    protected AssetCategory $category;

    public function __construct(int $categoryId)
    {
        $this->category = AssetCategory::query()
            ->where('id', $categoryId)
            ->where('is_active', 1)
            ->firstOrFail();
    }

    public function headings(): array
    {
        return $this->buildHeadingsList();
    }

    protected function buildHeadingsList(): array
    {
        $base = [
            'category_id',
            'category',
            'company_name',
            'zone_name',
            'branch_name',
            'department_name',
            'asset_code',
            'model',
            'serial_number',
            'status',
            'purchase_date',
            'warranty_expiry',
            'responsible_person',
            'remarks',
        ];

        $template = $this->inferDashboardTemplate((string) $this->category->name);

        $typeCols = match ($template) {
            'system' => ['system_model', 'monitor_model', 'os_installed', 'processor', 'ssd_hdd', 'ram', 'brand'],
            'printer' => ['brand'],
            'cctv' => ['camera_name', 'brand', 'ip_address'],
            'switch', 'router' => ['brand', 'ip_address', 'device_username', 'device_password'],
            'nvr' => ['dvr_name', 'brand', 'ip_address', 'dvr_channel'],
            'dvr' => ['dvr_name', 'brand', 'ip_address', 'dvr_channel'],
            'all' => [
                'system_model', 'monitor_model', 'os_installed', 'processor', 'ssd_hdd', 'ram',
                'brand', 'ip_address', 'camera_name', 'dvr_name', 'dvr_channel', 'device_username', 'device_password',
            ],
            default => ['system_model', 'monitor_model', 'os_installed', 'processor', 'ssd_hdd', 'ram', 'brand'],
        };

        return array_merge($base, $typeCols);
    }

    protected function inferDashboardTemplate(string $name): string
    {
        $t = mb_strtolower($name);
        if (str_contains($t, 'printer')) {
            return 'printer';
        }
        if (str_contains($t, 'cctv') || str_contains($t, 'camera')) {
            return 'cctv';
        }
        if (str_contains($t, 'nvr')) {
            return 'nvr';
        }
        if (str_contains($t, 'dvr')) {
            return 'dvr';
        }
        if (str_contains($t, 'router')) {
            return 'router';
        }
        if (str_contains($t, 'switch')) {
            return 'switch';
        }
        if (
            str_contains($t, 'monitor')
            || str_contains($t, 'cpu')
            || str_contains($t, 'desktop')
            || str_contains($t, 'laptop')
        ) {
            return 'system';
        }

        return 'all';
    }

    public function array(): array
    {
        $heads = $this->headings();
        $flip  = array_flip($heads);
        $row   = array_fill(0, count($heads), '');

        $row[$flip['category_id']]          = (string) $this->category->id;
        $row[$flip['category']]             = (string) $this->category->name;
        $row[$flip['company_name']]         = 'Example Company Ltd';
        $row[$flip['zone_name']]            = 'TN CENTRAL';
        $row[$flip['branch_name']]          = 'Main Branch';
        $row[$flip['department_name']]      = 'IT';
        $row[$flip['asset_code']]           = 'AST-TPL-001';
        $row[$flip['model']]                = 'Sample model';
        $row[$flip['serial_number']]        = 'SN-TPL-001';
        $row[$flip['status']]               = 'available';
        $row[$flip['purchase_date']]        = '01/04/2026';
        $row[$flip['warranty_expiry']]      = '01/04/2028';
        $row[$flip['responsible_person']]   = 'IT Admin';
        $row[$flip['remarks']]              = 'Imported row example — replace with real data';

        $this->fillTemplateSpecificExample($flip, $row);

        return [$row];
    }

    protected function fillTemplateSpecificExample(array $flip, array &$row): void
    {
        $template = $this->inferDashboardTemplate((string) $this->category->name);

        $set = function (string $key, string $value) use ($flip, &$row): void {
            if (isset($flip[$key])) {
                $row[$flip[$key]] = $value;
            }
        };

        switch ($template) {
            case 'system':
                $set('system_model', 'Dell OptiPlex 7090');
                $set('monitor_model', 'Dell P2422H');
                $set('os_installed', 'Windows 11 Pro');
                $set('processor', 'Intel i5');
                $set('ssd_hdd', '512GB SSD');
                $set('ram', '16GB');
                $set('brand', 'Dell');
                break;
            case 'printer':
                $set('brand', 'HP');
                break;
            case 'cctv':
                $set('camera_name', 'Lobby Cam 01');
                $set('brand', 'Hikvision');
                $set('ip_address', '192.168.1.100');
                break;
            case 'switch':
            case 'router':
                $set('brand', 'Cisco');
                $set('ip_address', '192.168.1.1');
                $set('device_username', 'admin');
                $set('device_password', 'change-me');
                break;
            case 'nvr':
            case 'dvr':
                $set('dvr_name', 'NVR-01');
                $set('brand', 'Hikvision');
                $set('ip_address', '192.168.1.50');
                $set('dvr_channel', '16');
                break;
            default:
                $set('system_model', '—');
                $set('brand', '—');
                break;
        }
    }
}
