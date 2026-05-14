<?php

namespace App\Http\Controllers;

use App\Exports\PharmacyAuditExport;
use App\Exports\PharmacyAuditImportTemplate;
use App\Imports\PharmacyAuditImport;
use App\Models\PharmacyAudit;
use App\Models\PharmacyAuditItem;
use App\Models\Tblcompany;
use App\Models\TblLocationModel;
use App\Models\TblZonesModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class PharmacyAuditController extends Controller
{
    private function userRow(): object
    {
        $u = auth()->user();
        if (! $u) {
            abort(403);
        }

        return is_object($u) ? $u : (object) (array) $u;
    }

    /**
     * @return list<int>
     */
    private function filterIntList(Request $request, string $key): array
    {
        $raw = $request->input($key);
        if ($raw === null || $raw === '') {
            return [];
        }
        $items = is_array($raw) ? $raw : explode(',', (string) $raw);
        $clean = [];
        foreach ($items as $item) {
            $val = (int) trim((string) $item);
            if ($val > 0) {
                $clean[$val] = true;
            }
        }

        return array_keys($clean);
    }

    private function auditsFilteredQuery(Request $request): Builder
    {
        $q = PharmacyAudit::query();

        $companyIds = $this->filterIntList($request, 'company_id');
        if ($companyIds !== []) {
            $q->whereIn('company_id', $companyIds);
        }
        $zoneIds = $this->filterIntList($request, 'zone_id');
        if ($zoneIds !== []) {
            $q->whereIn('zone_id', $zoneIds);
        }
        $branchIds = $this->filterIntList($request, 'branch_id');
        if ($branchIds !== []) {
            $q->whereIn('branch_id', $branchIds);
        }

        if ($request->filled('date_from')) {
            $q->whereDate('audit_date', '>=', $request->date('date_from'));
        }
        if ($request->filled('date_to')) {
            $q->whereDate('audit_date', '<=', $request->date('date_to'));
        }

        $search = trim((string) $request->input('universal_search', ''));
        if ($search !== '') {
            $like = '%'.addcslashes($search, '%_\\').'%';
            $q->where(function (Builder $sub) use ($like) {
                $sub->where('audit_number', 'like', $like)
                    ->orWhere('notes', 'like', $like)
                    ->orWhereHas('company', function (Builder $companyQuery) use ($like) {
                        $companyQuery->where('company_name', 'like', $like);
                    })
                    ->orWhereHas('zone', function (Builder $zoneQuery) use ($like) {
                        $zoneQuery->where('name', 'like', $like);
                    })
                    ->orWhereHas('branch', function (Builder $branchQuery) use ($like) {
                        $branchQuery->where('name', 'like', $like);
                    })
                    ->orWhereHas('items', function (Builder $iq) use ($like) {
                        $iq->where('item_name', 'like', $like)
                            ->orWhere('batch_no', 'like', $like);
                    });
            });
        }

        return $q;
    }

    private function nextAuditNumber(): string
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

    private function recalcTotals(PharmacyAudit $audit): void
    {
        $audit->load('items');
        $audit->total_lines = $audit->items->count();
        $audit->total_val = round($audit->items->sum(fn ($i) => (float) $i->val), 2);
        $audit->save();
    }

    /**
     * @return array<string, mixed>
     */
    private function pharmacyAuditRules(Request $request): array
    {
        return [
            'company_id' => 'required|integer|exists:company_tbl,id',
            'zone_id' => 'required|integer|exists:tblzones,id',
            'branch_id' => [
                'required',
                'integer',
                Rule::exists('tbl_locations', 'id')->where(function ($query) use ($request) {
                    $query->where('zone_id', (int) $request->input('zone_id'));
                }),
            ],
            'audit_date' => 'required|date',
            'notes' => 'nullable|string|max:5000',
            'items' => 'required|array|min:1',
            'items.*.item_name' => 'required|string|max:500',
            'items.*.batch_no' => 'nullable|string|max:120',
            'items.*.expiry' => 'nullable|date_format:Y-m',
            'items.*.mrp' => 'required|numeric|min:0',
            'items.*.system_qty' => 'required|integer',
            'items.*.manual_qty' => 'required|integer',
            'items.*.diff_qty' => 'required|integer',
            'items.*.val' => 'required|numeric',
        ];
    }

    /**
     * @return array<string, string>
     */
    private function pharmacyAuditFieldMessages(): array
    {
        return [
            'company_id.required' => 'Company field is required.',
            'company_id.exists' => 'Selected company is not valid.',
            'zone_id.required' => 'Zone field is required.',
            'zone_id.exists' => 'Selected zone is not valid.',
            'branch_id.required' => 'Branch field is required.',
            'branch_id.exists' => 'Branch must belong to the selected zone.',
            'audit_date.required' => 'Audit date field is required.',
            'notes.max' => 'Notes may not be greater than 5000 characters.',
            'items.required' => 'Add at least one line item.',
            'items.min' => 'Add at least one line item.',
            'items.*.item_name.required' => 'Each line must have an item name.',
            'items.*.mrp.required' => 'MRP is required on each line.',
            'items.*.mrp.numeric' => 'MRP must be a valid number.',
            'items.*.mrp.min' => 'MRP cannot be negative.',
            'items.*.system_qty.required' => 'System quantity is required on each line.',
            'items.*.system_qty.integer' => 'System quantity must be a whole number.',
            'items.*.manual_qty.required' => 'Manual quantity is required on each line.',
            'items.*.manual_qty.integer' => 'Manual quantity must be a whole number.',
            'items.*.diff_qty.required' => 'Difference quantity is required on each line.',
            'items.*.diff_qty.integer' => 'Difference quantity must be a whole number.',
            'items.*.val.required' => 'Value is required on each line.',
            'items.*.val.numeric' => 'Value must be a valid number.',
            'items.*.expiry.date_format' => 'Each expiry must be a valid month and year (YYYY-MM), or left blank.',
        ];
    }

    public function index(Request $request): View
    {
        $this->userRow();

        $companies = Tblcompany::query()->orderBy('company_name')->get(['id', 'company_name']);
        $zones = TblZonesModel::query()->orderBy('name')->get(['id', 'name']);
        $branches = TblLocationModel::query()->orderBy('name')->get(['id', 'name', 'zone_id']);

        $perPageChoices = [10, 15, 25, 50, 100];
        $perPage = (int) $request->input('per_page', 10);
        if (! in_array($perPage, $perPageChoices, true)) {
            $perPage = 10;
        }

        $base = $this->auditsFilteredQuery($request);

        $stats = [
            'total_audits' => (clone $base)->count(),
            'total_lines' => (int) PharmacyAuditItem::query()
                ->whereIn('pharmacy_audit_id', (clone $base)->select('id'))
                ->count(),
            'total_variance' => (float) (clone $base)->sum('total_val'),
        ];

        $records = (clone $base)
            ->with(['creator:id,user_fullname', 'company:id,company_name', 'zone:id,name', 'branch:id,name,zone_id'])
            ->orderByDesc('audit_date')
            ->orderByDesc('id')
            ->paginate($perPage)
            ->withQueryString();

        return view('superadmin.pharmacy_audit.index', [
            'admin' => auth()->user(),
            'records' => $records,
            'stats' => $stats,
            'companies' => $companies,
            'zones' => $zones,
            'branches' => $branches,
            'perPageChoices' => $perPageChoices,
            'perPage' => $perPage,
        ]);
    }

    public function create(): View
    {
        $this->userRow();
        $selectedZoneId = old('zone_id') ?: null;

        return view('superadmin.pharmacy_audit.create', [
            'admin' => auth()->user(),
            'record' => null,
            'isEdit' => false,
            'companies' => Tblcompany::query()->orderBy('company_name')->get(['id', 'company_name']),
            'zones' => TblZonesModel::query()->orderBy('name')->get(['id', 'name']),
            'branches' => TblLocationModel::query()
                ->when($selectedZoneId, fn ($q) => $q->where('zone_id', $selectedZoneId))
                ->orderBy('name')
                ->get(['id', 'name', 'zone_id']),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->userRow();

        $validated = $request->validate(
            $this->pharmacyAuditRules($request),
            $this->pharmacyAuditFieldMessages()
        );

        DB::transaction(function () use ($validated) {
            $audit = PharmacyAudit::query()->create([
                'audit_number' => $this->nextAuditNumber(),
                'company_id' => $validated['company_id'],
                'zone_id' => $validated['zone_id'],
                'branch_id' => $validated['branch_id'],
                'audit_date' => $validated['audit_date'],
                'notes' => $validated['notes'] ?? null,
                'total_lines' => 0,
                'total_val' => 0,
                'created_by' => (int) auth()->id(),
            ]);

            $line = 1;
            foreach ($validated['items'] as $row) {
                PharmacyAuditItem::query()->create([
                    'pharmacy_audit_id' => $audit->id,
                    'line_no' => $line,
                    'item_name' => $row['item_name'],
                    'batch_no' => $row['batch_no'] ?? null,
                    'expiry' => $row['expiry'] ?? null,
                    'mrp' => $row['mrp'],
                    'system_qty' => $row['system_qty'],
                    'manual_qty' => $row['manual_qty'],
                    'diff_qty' => $row['diff_qty'],
                    'val' => $row['val'],
                ]);
                $line++;
            }

            $this->recalcTotals($audit);
        });

        return redirect()
            ->route('pharmacy-audits.index')
            ->with('success', 'Pharmacy audit saved.');
    }

    public function show(PharmacyAudit $pharmacyAudit): View
    {
        $this->userRow();
        $pharmacyAudit->load(['items', 'creator:id,user_fullname', 'company:id,company_name', 'zone:id,name', 'branch:id,name,zone_id']);

        return view('superadmin.pharmacy_audit.show', [
            'admin' => auth()->user(),
            'record' => $pharmacyAudit,
        ]);
    }

    public function edit(PharmacyAudit $pharmacyAudit): View
    {
        $this->userRow();
        $selectedZoneId = old('zone_id', $pharmacyAudit->zone_id);

        $pharmacyAudit->load('items');

        return view('superadmin.pharmacy_audit.create', [
            'admin' => auth()->user(),
            'record' => $pharmacyAudit,
            'isEdit' => true,
            'companies' => Tblcompany::query()->orderBy('company_name')->get(['id', 'company_name']),
            'zones' => TblZonesModel::query()->orderBy('name')->get(['id', 'name']),
            'branches' => TblLocationModel::query()
                ->when($selectedZoneId, fn ($q) => $q->where('zone_id', $selectedZoneId))
                ->orderBy('name')
                ->get(['id', 'name', 'zone_id']),
        ]);
    }

    public function update(Request $request, PharmacyAudit $pharmacyAudit): RedirectResponse
    {
        $this->userRow();

        $validated = $request->validate(
            $this->pharmacyAuditRules($request),
            $this->pharmacyAuditFieldMessages()
        );

        DB::transaction(function () use ($validated, $pharmacyAudit) {
            $pharmacyAudit->update([
                'company_id' => $validated['company_id'],
                'zone_id' => $validated['zone_id'],
                'branch_id' => $validated['branch_id'],
                'audit_date' => $validated['audit_date'],
                'notes' => $validated['notes'] ?? null,
            ]);

            $pharmacyAudit->items()->delete();

            $line = 1;
            foreach ($validated['items'] as $row) {
                PharmacyAuditItem::query()->create([
                    'pharmacy_audit_id' => $pharmacyAudit->id,
                    'line_no' => $line,
                    'item_name' => $row['item_name'],
                    'batch_no' => $row['batch_no'] ?? null,
                    'expiry' => $row['expiry'] ?? null,
                    'mrp' => $row['mrp'],
                    'system_qty' => $row['system_qty'],
                    'manual_qty' => $row['manual_qty'],
                    'diff_qty' => $row['diff_qty'],
                    'val' => $row['val'],
                ]);
                $line++;
            }

            $this->recalcTotals($pharmacyAudit);
        });

        return redirect()
            ->route('pharmacy-audits.show', $pharmacyAudit)
            ->with('success', 'Pharmacy audit updated.');
    }

    public function export(Request $request): BinaryFileResponse
    {
        $this->userRow();

        $ids = $this->auditsFilteredQuery($request)->pluck('id');
        $rows = PharmacyAuditItem::query()
            ->whereIn('pharmacy_audit_id', $ids)
            ->with([
                'audit.company:id,company_name',
                'audit.zone:id,name',
                'audit.branch:id,name',
            ])
            ->orderByDesc('pharmacy_audit_id')
            ->orderBy('line_no')
            ->get();

        $filename = 'pharmacy_audit_export_'.date('Y-m-d_His').'.xlsx';

        return Excel::download(new PharmacyAuditExport($rows), $filename);
    }

    public function importTemplate(): BinaryFileResponse
    {
        $this->userRow();

        return Excel::download(new PharmacyAuditImportTemplate(), 'pharmacy_audit_import_template.xlsx');
    }

    public function import(Request $request): RedirectResponse
    {
        $this->userRow();

        $request->validate([
            'import_file' => 'required|file|mimes:xlsx,xls,csv|max:10240',
        ]);

        $import = new PharmacyAuditImport;

        try {
            Excel::import($import, $request->file('import_file'));
        } catch (\Throwable $e) {
            return redirect()
                ->route('pharmacy-audits.index')
                ->with('error', 'Import failed: '.$e->getMessage());
        }

        $msg = "Imported {$import->importedAudits} audit(s), {$import->importedLines} line(s).";
        if ($import->skippedRows > 0) {
            $msg .= " Skipped {$import->skippedRows} row(s).";
        }

        return redirect()
            ->route('pharmacy-audits.index')
            ->with('success', $msg);
    }
}
