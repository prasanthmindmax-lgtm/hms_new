<?php

namespace App\Imports;

use App\Models\Asset;
use App\Models\AssetCategory;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class AssetImport implements ToCollection, WithHeadingRow
{
    public int $imported = 0;

    public int $skipped = 0;

    public function __construct(
        protected ?int $defaultCategoryId = null
    ) {}

    public function collection(Collection $rows)
    {
        $rows = $rows->filter(function ($row) {
            return collect($row)->filter(fn ($v) => $v !== null && $v !== '')->isNotEmpty();
        })->values();

        if ($rows->isEmpty()) {
            return;
        }

        $userId = auth()->id();

        foreach ($rows as $idx => $row) {
            $raw  = $row instanceof Collection ? $row->toArray() : (array) $row;
            $rowArr = $this->normalizeImportRowKeys($raw);

            $categoryId = $this->resolveCategoryId($rowArr);
            if ($categoryId === null) {
                $this->skipped++;
                Log::warning('AssetImport: skipped row — could not resolve category', ['row' => $idx + 2, 'keys_sample' => array_slice(array_keys($rowArr), 0, 8)]);

                continue;
            }

            $cat = AssetCategory::query()->where('id', $categoryId)->where('is_active', 1)->first();
            if (! $cat) {
                $this->skipped++;
                Log::warning('AssetImport: skipped row — category not found or inactive', ['category_id' => $categoryId, 'row' => $idx + 2]);

                continue;
            }

            $assetCode = $this->trimStr($rowArr['asset_code'] ?? null);
            if ($assetCode !== null && $assetCode !== '' && Asset::query()->where('asset_code', $assetCode)->exists()) {
                $this->skipped++;
                Log::warning('AssetImport: skipped row — duplicate asset_code', ['asset_code' => $assetCode, 'row' => $idx + 2]);

                continue;
            }

            $companyId = $this->resolveCompanyId($rowArr);
            $zoneId    = $this->resolveZoneId($rowArr);
            $branchId  = $this->resolveBranchId($rowArr, $zoneId);
            $deptId    = $this->resolveDepartmentId($rowArr);

            $status = $this->normalizeStatus($rowArr['status'] ?? null);
            $typeAttributes = $this->buildTypeAttributes($rowArr, (string) $cat->name);

            $payload = [
                'company_id'         => $companyId,
                'zone_id'            => $zoneId,
                'branch_id'          => $branchId,
                'department_id'      => $deptId,
                'category_id'        => $categoryId,
                'asset_code'         => $assetCode !== '' ? $assetCode : null,
                'model'              => $this->nullableStr($rowArr['model'] ?? null, 255),
                'serial_number'      => $this->nullableStr($this->firstString($rowArr, ['serial_number', 'serial']), 255),
                'purchase_date'      => $this->parseDate($rowArr['purchase_date'] ?? null),
                'warranty_expiry'    => $this->parseDate($rowArr['warranty_expiry'] ?? null),
                'status'             => $status,
                'responsible_person' => $this->nullableStr($this->firstString($rowArr, ['responsible_person', 'responsible']), 255),
                'remarks'            => $this->nullableStr($rowArr['remarks'] ?? null, 5000),
                'type_attributes'    => $typeAttributes,
                'created_by'         => $userId,
                'updated_by'         => $userId,
            ];

            try {
                Asset::create($payload);
                $this->imported++;
            } catch (\Throwable $e) {
                $this->skipped++;
                Log::error('AssetImport: create failed', ['row' => $idx + 2, 'message' => $e->getMessage()]);
            }
        }
    }

    protected function normalizeImportRowKeys(array $row): array
    {
        $out = [];
        foreach ($row as $key => $value) {
            if (! is_string($key)) {
                $out[$key] = $value;

                continue;
            }
            $k = mb_strtolower(trim($key));
            $k = str_replace(['-', ' ', '.'], '_', $k);
            $k = preg_replace('/_+/', '_', $k) ?? $k;
            $k = trim($k, '_');
            if ($k === '') {
                continue;
            }
            $out[$k] = $value;
        }

        return $out;
    }

    protected function resolveCategoryId(array $row): ?int
    {
        $idRaw = $row['category_id'] ?? null;
        if ($idRaw !== null && $idRaw !== '') {
            $id = is_numeric($idRaw) ? (int) round((float) $idRaw) : (int) $idRaw;
            if ($id > 0 && AssetCategory::query()->where('id', $id)->where('is_active', 1)->exists()) {
                return $id;
            }
        }

        $name = $this->trimStr($row['category'] ?? null);
        if ($name !== null && $name !== '') {
            $c = AssetCategory::query()
                ->whereRaw('LOWER(TRIM(name)) = ?', [mb_strtolower($name)])
                ->where('is_active', 1)
                ->first();
            if ($c) {
                return (int) $c->id;
            }
        }

        if ($this->defaultCategoryId !== null && $this->defaultCategoryId > 0) {
            if (AssetCategory::query()->where('id', $this->defaultCategoryId)->where('is_active', 1)->exists()) {
                return $this->defaultCategoryId;
            }
        }

        return null;
    }

    protected function resolveCompanyId(array $row): ?int
    {
        $idRaw = $row['company_id'] ?? null;
        if ($idRaw !== null && $idRaw !== '') {
            $id = is_numeric($idRaw) ? (int) round((float) $idRaw) : (int) $idRaw;
            if ($id > 0 && DB::table('company_tbl')->where('id', $id)->exists()) {
                return $id;
            }
        }
        $name = $this->trimStr($this->firstString($row, ['company_name', 'company']));
        if ($name === null || $name === '') {
            return null;
        }
        $c = DB::table('company_tbl')->where('company_name', $name)->first();

        return $c ? (int) $c->id : null;
    }

    protected function resolveZoneId(array $row): ?int
    {
        $idRaw = $row['zone_id'] ?? null;
        if ($idRaw !== null && $idRaw !== '') {
            $id = is_numeric($idRaw) ? (int) round((float) $idRaw) : (int) $idRaw;
            if ($id > 0 && DB::table('tblzones')->where('id', $id)->exists()) {
                return $id;
            }
        }
        $name = $this->trimStr($this->firstString($row, ['zone_name', 'zone']));
        if ($name === null || $name === '') {
            return null;
        }
        $z = DB::table('tblzones')->where('name', $name)->first();

        return $z ? (int) $z->id : null;
    }

    protected function resolveBranchId(array $row, ?int $zoneId): ?int
    {
        $idRaw = $row['branch_id'] ?? null;
        if ($idRaw !== null && $idRaw !== '') {
            $id = is_numeric($idRaw) ? (int) round((float) $idRaw) : (int) $idRaw;
            $q  = DB::table('tbl_locations')->where('id', $id);
            if ($zoneId !== null) {
                $q->where('zone_id', $zoneId);
            }
            if ($q->exists()) {
                return $id;
            }
        }
        $name = $this->trimStr($this->firstString($row, ['branch_name', 'branch']));
        if ($name === null || $name === '') {
            return null;
        }
        $q = DB::table('tbl_locations')->where('name', $name);
        if ($zoneId !== null) {
            $q->where('zone_id', $zoneId);
        }
        $b = $q->first();

        return $b ? (int) $b->id : null;
    }

    protected function resolveDepartmentId(array $row): ?int
    {
        $idRaw = $row['department_id'] ?? null;
        if ($idRaw !== null && $idRaw !== '') {
            $id = is_numeric($idRaw) ? (int) round((float) $idRaw) : (int) $idRaw;
            if ($id > 0 && DB::table('departments')->where('id', $id)->where('is_active', 1)->exists()) {
                return $id;
            }
        }
        $name = $this->trimStr($this->firstString($row, ['department_name', 'department']));
        if ($name === null || $name === '') {
            return null;
        }
        $d = DB::table('departments')->where('name', $name)->where('is_active', 1)->first();

        return $d ? (int) $d->id : null;
    }

    protected function buildTypeAttributes(array $row, string $categoryName): ?array
    {
        $keys = [
            'system_model', 'monitor_model', 'os_installed', 'processor', 'ssd_hdd', 'ram',
            'brand', 'ip_address', 'camera_name', 'dvr_name', 'dvr_channel', 'device_username', 'device_password',
        ];
        $out = [];
        foreach ($keys as $k) {
            $v = $this->trimStr($row[$k] ?? null);
            if ($v !== null && $v !== '') {
                $out[$k] = mb_substr($v, 0, 2000);
            }
        }
        $out['ui_template'] = $this->inferUiTemplate($categoryName);
        if ($out['ui_template'] === 'general') {
            $out['ui_template'] = 'system';
        }

        return $out;
    }

    protected function inferUiTemplate(string $name): string
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

    protected function normalizeStatus($raw): string
    {
        $s = strtolower(trim((string) ($raw ?? '')));
        if ($s === '' || $s === 'available') {
            return Asset::STATUS_AVAILABLE;
        }
        if (in_array($s, ['assigned', 'in use', 'in_use'], true)) {
            return Asset::STATUS_ASSIGNED;
        }
        if (in_array($s, ['maintenance', 'repair'], true)) {
            return Asset::STATUS_MAINTENANCE;
        }
        if (in_array($s, ['retired', 'stock', 'scrapped'], true)) {
            return Asset::STATUS_RETIRED;
        }
        if (in_array($s, Asset::STATUSES, true)) {
            return $s;
        }

        return Asset::STATUS_AVAILABLE;
    }

    protected function parseDate($value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }
        if (is_numeric($value)) {
            try {
                return Date::excelToDateTimeObject((float) $value)->format('Y-m-d');
            } catch (\Throwable $e) {
                return null;
            }
        }
        $str = trim((string) $value);
        try {
            return Carbon::createFromFormat('d/m/Y', $str)->format('Y-m-d');
        } catch (\Exception $e) {
            try {
                return Carbon::parse($str)->format('Y-m-d');
            } catch (\Exception $e2) {
                Log::warning('AssetImport: invalid date', ['value' => $str]);

                return null;
            }
        }
    }

    protected function nullableStr($v, int $maxLen = 5000): ?string
    {
        $s = $this->trimStr($v);

        return $s === '' ? null : mb_substr($s, 0, $maxLen);
    }

    protected function trimStr($v): string
    {
        if ($v === null) {
            return '';
        }
        if ($v instanceof \PhpOffice\PhpSpreadsheet\RichText\RichText) {
            return trim($v->getPlainText());
        }

        return trim((string) $v);
    }

    protected function firstString(array $row, array $keys): ?string
    {
        foreach ($keys as $key) {
            if (! array_key_exists($key, $row)) {
                continue;
            }
            $s = $this->trimStr($row[$key]);
            if ($s !== '') {
                return $s;
            }
        }

        return null;
    }
}
