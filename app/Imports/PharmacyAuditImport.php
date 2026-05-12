<?php

namespace App\Imports;

use App\Models\PharmacyAudit;
use App\Models\PharmacyAuditItem;
use App\Models\Tblcompany;
use App\Models\TblLocationModel;
use App\Models\TblZonesModel;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

class PharmacyAuditImport implements ToCollection, WithHeadingRow
{
    public int $importedAudits = 0;

    public int $importedLines = 0;

    public int $skippedRows = 0;

    public function collection(Collection $rows): void
    {
        $rows = $rows->filter(function ($row) {
            return collect($row)->filter(fn ($v) => $v !== null && $v !== '')->isNotEmpty();
        })->values();

        if ($rows->isEmpty()) {
            return;
        }

        $userId = auth()->id();

        $groups = [];

        foreach ($rows as $idx => $row) {
            $raw = $row instanceof Collection ? $row->toArray() : (array) $row;
            $arr = $this->normalizeRowKeys($raw);

            $companyId = $this->resolveCompanyId($arr);
            $zoneId = $this->resolveZoneId($arr);
            $branchId = $this->resolveBranchId($arr, $zoneId);
            $auditDate = $this->parseAuditDate($arr['audit_date'] ?? null);

            if ($companyId === null || $zoneId === null || $branchId === null || $auditDate === null) {
                $this->skippedRows++;
                Log::warning('PharmacyAuditImport: skipped row (location/date)', ['row' => $idx + 2]);

                continue;
            }

            $itemName = $this->trimStr($this->firstString($arr, ['name', 'item_name', 'product_name']));
            if ($itemName === null || $itemName === '') {
                $this->skippedRows++;
                Log::warning('PharmacyAuditImport: skipped row (missing name)', ['row' => $idx + 2]);

                continue;
            }

            $key = $companyId.'|'.$zoneId.'|'.$branchId.'|'.$auditDate->format('Y-m-d');

            $groups[$key][] = [
                'company_id' => $companyId,
                'company_name' => $this->companyName($companyId),
                'zone_id' => $zoneId,
                'zone_name' => $this->zoneName($zoneId),
                'branch_id' => $branchId,
                'branch_name' => $this->branchName($branchId),
                'audit_date' => $auditDate,
                'item' => $arr,
                'name' => $itemName,
            ];
        }

        if ($groups === []) {
            return;
        }

        DB::transaction(function () use ($groups, $userId) {
            foreach ($groups as $batch) {
                $head = $batch[0];
                $audit = PharmacyAudit::query()->create([
                    'audit_number' => $this->nextAuditNumber(),
                    'company_id' => $head['company_id'],
                    'company_name' => $head['company_name'],
                    'zone_id' => $head['zone_id'],
                    'zone_name' => $head['zone_name'],
                    'branch_id' => $head['branch_id'],
                    'branch_name' => $head['branch_name'],
                    'audit_date' => $head['audit_date']->format('Y-m-d'),
                    'notes' => null,
                    'total_lines' => 0,
                    'total_val' => 0,
                    'created_by' => $userId,
                ]);

                $line = 1;
                foreach ($batch as $pack) {
                    $arr = $pack['item'];
                    $mrp = $this->parseDecimal($arr['mrp'] ?? 0);
                    $sys = $this->parseInt($arr['system_quantity'] ?? $arr['system_store'] ?? $arr['s_q'] ?? $arr['system_qty'] ?? 0);
                    $man = $this->parseInt($arr['manual_quantity'] ?? $arr['manual_store'] ?? $arr['m_q'] ?? $arr['manual_qty'] ?? 0);
                    $diff = $arr['diff'] ?? $arr['difference'] ?? null;
                    $val = $arr['val'] ?? $arr['value'] ?? null;
                    if ($diff === null || $diff === '') {
                        $diff = $man - $sys;
                    } else {
                        $diff = $this->parseInt($diff);
                    }
                    if ($val === null || $val === '') {
                        $val = round((float) $diff * (float) $mrp, 2);
                    } else {
                        $val = $this->parseDecimal($val);
                    }

                    PharmacyAuditItem::query()->create([
                        'pharmacy_audit_id' => $audit->id,
                        'line_no' => $line,
                        'item_name' => $pack['name'],
                        'batch_no' => $this->nullableStr($this->firstString($arr, ['batch', 'batch_no']), 120),
                        'expiry' => $this->nullableStr($arr['expiry'] ?? null, 20),
                        'mrp' => $mrp,
                        'system_qty' => $sys,
                        'manual_qty' => $man,
                        'diff_qty' => (int) $diff,
                        'val' => $val,
                    ]);
                    $line++;
                    $this->importedLines++;
                }

                $this->recalcTotals($audit);
                $this->importedAudits++;
            }
        });
    }

    protected function recalcTotals(PharmacyAudit $audit): void
    {
        $audit->load('items');
        $audit->total_lines = $audit->items->count();
        $audit->total_val = $audit->items->sum(fn ($i) => (float) $i->val);
        $audit->save();
    }

    protected function nextAuditNumber(): string
    {
        $y = date('Y');
        $prefix = 'PHA-'.$y.'-';
        $last = PharmacyAudit::query()
            ->where('audit_number', 'like', $prefix.'%')
            ->orderByDesc('id')
            ->value('audit_number');
        $n = 1;
        if ($last && preg_match('/^'.preg_quote($prefix, '/').'(\d+)$/', (string) $last, $m)) {
            $n = (int) $m[1] + 1;
        }

        return $prefix.str_pad((string) $n, 5, '0', STR_PAD_LEFT);
    }

    protected function companyName(?int $id): ?string
    {
        if (! $id) {
            return null;
        }

        return Tblcompany::query()->where('id', $id)->value('company_name');
    }

    protected function zoneName(?int $id): ?string
    {
        if (! $id) {
            return null;
        }

        return TblZonesModel::query()->where('id', $id)->value('name');
    }

    protected function branchName(?int $id): ?string
    {
        if (! $id) {
            return null;
        }

        return TblLocationModel::query()->where('id', $id)->value('name');
    }

    protected function normalizeRowKeys(array $row): array
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

    protected function firstString(array $row, array $keys): ?string
    {
        foreach ($keys as $k) {
            if (! array_key_exists($k, $row)) {
                continue;
            }
            $s = $this->trimStr($row[$k]);

            return $s;
        }

        return null;
    }

    protected function trimStr(mixed $v): ?string
    {
        if ($v === null) {
            return null;
        }
        $s = trim((string) $v);

        return $s === '' ? null : $s;
    }

    protected function nullableStr(?string $v, int $max): ?string
    {
        if ($v === null || $v === '') {
            return null;
        }

        return mb_substr($v, 0, $max);
    }

    protected function resolveCompanyId(array $row): ?int
    {
        if (isset($row['company_id']) && (int) $row['company_id'] > 0) {
            $id = (int) $row['company_id'];
            if (Tblcompany::query()->where('id', $id)->exists()) {
                return $id;
            }
        }
        $name = $this->trimStr($row['company'] ?? $row['company_name'] ?? null);
        if ($name === null) {
            return null;
        }
        $id = Tblcompany::query()->where('company_name', $name)->value('id');
        if ($id) {
            return (int) $id;
        }
        $id = Tblcompany::query()->where('company_name', 'like', '%'.$name.'%')->value('id');

        return $id ? (int) $id : null;
    }

    protected function resolveZoneId(array $row): ?int
    {
        if (isset($row['zone_id']) && (int) $row['zone_id'] > 0) {
            $id = (int) $row['zone_id'];
            if (TblZonesModel::query()->where('id', $id)->exists()) {
                return $id;
            }
        }
        $name = $this->trimStr($row['zone'] ?? $row['zone_name'] ?? null);
        if ($name === null) {
            return null;
        }
        $id = TblZonesModel::query()->where('name', $name)->value('id');
        if ($id) {
            return (int) $id;
        }
        $id = TblZonesModel::query()->where('name', 'like', '%'.$name.'%')->value('id');

        return $id ? (int) $id : null;
    }

    protected function resolveBranchId(array $row, ?int $zoneId): ?int
    {
        if (isset($row['branch_id']) && (int) $row['branch_id'] > 0) {
            $id = (int) $row['branch_id'];
            $q = TblLocationModel::query()->where('id', $id);
            if ($zoneId) {
                $q->where('zone_id', $zoneId);
            }
            if ($q->exists()) {
                return $id;
            }
        }
        $name = $this->trimStr($row['branch'] ?? $row['branch_name'] ?? $row['location'] ?? null);
        if ($name === null) {
            return null;
        }
        $q = TblLocationModel::query()->where('name', $name);
        if ($zoneId) {
            $q->where('zone_id', $zoneId);
        }
        $id = $q->value('id');
        if ($id) {
            return (int) $id;
        }
        $q = TblLocationModel::query()->where('name', 'like', '%'.$name.'%');
        if ($zoneId) {
            $q->where('zone_id', $zoneId);
        }
        $id = $q->value('id');

        return $id ? (int) $id : null;
    }

    protected function parseAuditDate(mixed $v): ?Carbon
    {
        if ($v === null || $v === '') {
            return null;
        }
        if (is_numeric($v)) {
            try {
                return Carbon::instance(ExcelDate::excelToDateTimeObject((float) $v))->startOfDay();
            } catch (\Throwable) {
                return null;
            }
        }
        $s = trim((string) $v);
        foreach (['Y-m-d', 'd/m/Y', 'd-m-Y', 'm/d/Y'] as $fmt) {
            try {
                return Carbon::createFromFormat($fmt, $s)->startOfDay();
            } catch (\Throwable) {
                continue;
            }
        }
        try {
            return Carbon::parse($s)->startOfDay();
        } catch (\Throwable) {
            return null;
        }
    }

    protected function parseDecimal(mixed $v): float
    {
        if ($v === null || $v === '') {
            return 0.0;
        }
        if (is_numeric($v)) {
            return round((float) $v, 2);
        }
        $s = str_replace([',', ' '], '', (string) $v);

        return round((float) $s, 2);
    }

    protected function parseInt(mixed $v): int
    {
        if ($v === null || $v === '') {
            return 0;
        }
        if (is_numeric($v)) {
            return (int) $v;
        }
        $s = preg_replace('/[^\d\-]/', '', (string) $v) ?? '';

        return (int) $s;
    }
}
