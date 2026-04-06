<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\TblZonesModel;
use App\Models\Tblcompany;
use App\Models\Tblvendor;
use App\Models\Tblaccount;
use App\Models\PettyCash;
use App\Models\Advance;
use App\Models\ExpenseReport;
use App\Models\PettyCashHistory;
use App\Exports\PettyCashTemplateExport;
use App\Exports\PettyCashExport;
use App\Imports\PettyCashImport;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Excel as ExcelWriter;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;


class PettyCashController extends Controller
{
    /**
     * @return array{0: ?int, 1: ?string} [primary branch_id, comma-separated branch_ids]
     */
    protected function normalizePettyCashBranchPayload(?string $raw): array
    {
        $ids = array_values(array_unique(array_filter(array_map('intval', explode(',', (string) $raw)))));
        if ($ids === []) {
            return [null, null];
        }

        return [$ids[0], implode(',', $ids)];
    }

    /**
     * @param  \Illuminate\Database\Query\Builder  $query
     */
    protected function applyAdvanceBranchFilter($query, ?string $branchIdCsv, string $idColumn = 'advances.branch_id', string $idsColumn = 'advances.branch_ids'): void
    {
        if ($branchIdCsv === null || $branchIdCsv === '') {
            return;
        }
        $ids = array_values(array_filter(array_map('intval', explode(',', $branchIdCsv))));
        if ($ids === []) {
            return;
        }
        $query->where(function ($q) use ($ids, $idColumn, $idsColumn) {
            foreach ($ids as $bid) {
                $q->orWhere(function ($qq) use ($bid, $idColumn, $idsColumn) {
                    $qq->where($idColumn, $bid)
                        ->orWhereRaw('FIND_IN_SET(?, ' . $idsColumn . ')', [$bid]);
                });
            }
        });
    }

    /**
     * @param  object|array<string,mixed>  $row
     * @return list<int>
     */
    protected function advanceBranchIdListFromRow($row): array
    {
        $ids = [];
        $branchId = is_array($row) ? ($row['branch_id'] ?? null) : ($row->branch_id ?? null);
        if ($branchId !== null && (int) $branchId !== 0) {
            $ids[] = (int) $branchId;
        }
        $csv = is_array($row) ? ($row['branch_ids'] ?? '') : ($row->branch_ids ?? '');
        foreach (explode(',', (string) $csv) as $p) {
            $i = (int) trim($p);
            if ($i !== 0) {
                $ids[] = $i;
            }
        }

        return array_values(array_unique($ids));
    }

    protected function hydrateAdvancesBranchNames($paginator): void
    {
        $items = $paginator->getCollection();
        $allIds = [];
        foreach ($items as $item) {
            foreach ($this->advanceBranchIdListFromRow($item) as $bid) {
                $allIds[$bid] = true;
            }
        }
        $idList = array_keys($allIds);
        if ($idList === []) {
            return;
        }
        $nameById = DB::table('tbl_locations')->whereIn('id', $idList)->pluck('name', 'id');
        foreach ($items as $item) {
            $labels = [];
            foreach ($this->advanceBranchIdListFromRow($item) as $bid) {
                if (isset($nameById[$bid])) {
                    $labels[] = $nameById[$bid];
                }
            }
            $item->branch_names_display = $labels !== [] ? implode(', ', $labels) : ($item->branch_name ?? '');
        }
    }

    protected function hydratePettyCashBranchNames($paginator): void
    {
        $items = $paginator->getCollection();
        $allIds = [];
        foreach ($items as $item) {
            foreach ($this->advanceBranchIdListFromRow($item) as $bid) {
                $allIds[$bid] = true;
            }
        }
        $idList = array_keys($allIds);
        if ($idList === []) {
            return;
        }
        $nameById = DB::table('tbl_locations')->whereIn('id', $idList)->pluck('name', 'id');
        foreach ($items as $item) {
            $labels = [];
            foreach ($this->advanceBranchIdListFromRow($item) as $bid) {
                if (isset($nameById[$bid])) {
                    $labels[] = $nameById[$bid];
                }
            }
            $fallback = optional($item->branch)->name ?? '';
            $item->branch_names_display = $labels !== [] ? implode(', ', $labels) : $fallback;
        }
    }

    /**
     * @return list<string>
     */
    protected function decodePettyCashAttachmentPathsJson($raw): array
    {
        if ($raw === null || $raw === '') {
            return [];
        }
        if (is_array($raw)) {
            return array_values(array_filter($raw, 'is_string'));
        }
        $a = json_decode((string) $raw, true);

        return is_array($a) ? array_values(array_filter($a, 'is_string')) : [];
    }

    /**
     * Stored paths for a petty cash row (JSON column + legacy receipt_path).
     *
     * @return list<string>
     */
    protected function allowedPettyCashAttachmentPathsFromRow(?object $row): array
    {
        if (!$row) {
            return [];
        }
        $paths = $this->decodePettyCashAttachmentPathsJson($row->attachment_paths ?? null);
        $rp = trim((string) ($row->receipt_path ?? ''));
        if ($rp !== '' && !in_array($rp, $paths, true)) {
            array_unshift($paths, $rp);
        }

        return array_values(array_unique($paths));
    }

    /**
     * Client sends JSON array of paths to keep when editing; must be a subset of server state.
     *
     * @return list<string>
     */
    protected function sanitizeKeptPettyCashAttachmentPaths(string $existingFilesJson, array $allowedFromDb): array
    {
        $decoded = json_decode($existingFilesJson, true);
        if (!is_array($decoded)) {
            return $allowedFromDb;
        }
        if (count($decoded) === 0) {
            return [];
        }
        $allowed = array_flip($allowedFromDb);
        $out = [];
        foreach ($decoded as $p) {
            if (!is_string($p) || $p === '') {
                continue;
            }
            if (str_contains($p, '..') || !str_starts_with($p, 'petty_cash_receipts/')) {
                continue;
            }
            if (isset($allowed[$p])) {
                $out[] = $p;
            }
        }

        return array_values(array_unique($out));
    }

    /**
     * @param  list<string>  $currentPaths
     * @return list<string>
     */
    protected function appendUploadedPettyCashFiles(Request $request, array $currentPaths, int $maxFiles = 5, int $maxKbEach = 10240): array
    {
        $files = $request->file('uploads', []);
        if (!is_array($files)) {
            $files = $files ? [$files] : [];
        }
        $dir = public_path('petty_cash_receipts');
        if (!is_dir($dir)) {
            mkdir($dir, 0775, true);
        }
        $paths = $currentPaths;
        foreach ($files as $file) {
            if (!$file || !$file->isValid()) {
                continue;
            }
            if (count($paths) >= $maxFiles) {
                break;
            }
            if ($file->getSize() > $maxKbEach * 1024) {
                continue;
            }
            $filename = time() . '_' . uniqid('', true) . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $file->getClientOriginalName());
            $file->move($dir, $filename);
            $paths[] = 'petty_cash_receipts/' . $filename;
        }

        return $paths;
    }

    /**
     * Match advances that share any branch with the given advance (primary or branch_ids).
     *
     * @param  \Illuminate\Database\Query\Builder  $query
     * @param  object|array<string,mixed>  $advance
     */
    protected function applyAdvancePeerBranchScope($query, $advance): void
    {
        $ids = $this->advanceBranchIdListFromRow($advance);
        if ($ids === []) {
            return;
        }
        $query->where(function ($q) use ($ids) {
            foreach ($ids as $bid) {
                $q->orWhere(function ($qq) use ($bid) {
                    $qq->where('branch_id', $bid)->orWhereRaw('FIND_IN_SET(?, branch_ids)', [$bid]);
                });
            }
        });
    }

    public function getPettyCash(Request $request)
    {
        $admin = auth()->user();
        if ($request->has('items_only')) {

            $items = DB::table('petty_cash_items')
                ->leftJoin('expense_categories', 'expense_categories.id', '=', 'petty_cash_items.expense_category_id')
                ->where('petty_cash_items.petty_cash_id', $request->id)
                ->select(
                    'expense_categories.name as category_name',
                    'petty_cash_items.description',
                    'petty_cash_items.amount'
                )
                ->get();

            return response()->json([
                'items' => $items
            ]);
        }

        $perPage = $request->get('per_page', 10);

        $TblZonesModel = TblZonesModel::orderBy('id', 'asc')->get();
        $Tblcompany = Tblcompany::orderBy('id', 'asc')->get();
        $Tblvendor = Tblvendor::orderBy('id', 'asc')->get();
        $Tblaccount = Tblaccount::orderBy('id', 'asc')->get();

        // MAIN QUERY
        $query = PettyCash::with(['report', 'vendor', 'zone', 'company', 'branch', 'category'])
            ->orderBy('id', 'desc');

        //  FILTERS

        if ($request->filled('zone_id')) {
            $query->whereIn('zone_id', explode(',', $request->zone_id));
        }

        if ($request->filled('branch_id')) {
            $this->applyAdvanceBranchFilter($query, $request->branch_id, 'petty_cash.branch_id', 'petty_cash.branch_ids');
        }

        if ($request->filled('company_id')) {
            $query->whereIn('company_id', explode(',', $request->company_id));
        }

        if ($request->filled('vendor_id')) {
            $query->whereIn('vendor_id', explode(',', $request->vendor_id));
        }

        if ($request->filled('status_name')) {
            $query->whereIn('status', explode(',', strtolower($request->status_name)));
        }

        if ($request->filled('date_from') && $request->filled('date_to')) {
            try {
                $from = Carbon::createFromFormat('d/m/Y', trim((string) $request->date_from))->startOfDay();
                $to   = Carbon::createFromFormat('d/m/Y', trim((string) $request->date_to))->endOfDay();

                $query->whereBetween('expense_date', [$from, $to]);
            } catch (\Exception $e) {
                // Ignore malformed dates from client; list loads without date filter
            }
        }

        if ($request->filled('universal_search')) {
            $search = $request->universal_search;

            $query->where(function ($q) use ($search) {
                $q->whereHas('report', function ($r) use ($search) {
                    $r->where('report_name', 'like', "%$search%")
                        ->orWhere('report_id', 'like', "%$search%");
                })
                    ->orWhereHas('vendor', function ($v) use ($search) {
                        $v->where('display_name', 'like', "%$search%");
                    })
                    ->orWhereHas('zone', function ($z) use ($search) {
                        $z->where('name', 'like', "%$search%");
                    })
                    ->orWhereHas('company', function ($c) use ($search) {
                        $c->where('company_name', 'like', "%$search%");
                    })
                    ->orWhereHas('branch', function ($b) use ($search) {
                        $b->where('name', 'like', "%$search%");
                    });
            });
        }

        $filteredData = (clone $query)->get();

        $filteredStats = [
            'total' => $filteredData->count(),
            'total_amount' => $filteredData->sum('total_amount'),

            'approved' => $filteredData->where('status', 'approved')->count(),
            'approved_amount' => $filteredData->where('status', 'approved')->sum('total_amount'),

            'pending' => $filteredData->where('status', 'pending')->count(),
            'pending_amount' => $filteredData->where('status', 'pending')->sum('total_amount'),

            'rejected' => $filteredData->where('status', 'rejected')->count(),
            'rejected_amount' => $filteredData->where('status', 'rejected')->sum('total_amount'),

            'draft' => $filteredData->where('status', 'draft')->count(),
            'draft_amount' => $filteredData->where('status', 'draft')->sum('total_amount'),
        ];

        $pettycashlist = $query->paginate($perPage)->appends($request->all());
        $this->hydratePettyCashBranchNames($pettycashlist);

        if ($request->ajax()) {
            $html = view('superadmin.pettycash.pettycash_rows', [
                'pettycashlist' => $pettycashlist,
                'perPage' => $perPage
            ])->render();

            return response()->json([
                'html' => $html,
                'stats' => $filteredStats
            ]);
        }

        $allData = PettyCash::all();

        $stats = [
            'total' => $allData->count(),
            'total_amount' => $allData->sum('total_amount'),

            'approved' => $allData->where('status', 'approved')->count(),
            'approved_amount' => $allData->where('status', 'approved')->sum('total_amount'),

            'pending' => $allData->where('status', 'pending')->count(),
            'pending_amount' => $allData->where('status', 'pending')->sum('total_amount'),

            'rejected' => $allData->where('status', 'rejected')->count(),
            'rejected_amount' => $allData->where('status', 'rejected')->sum('total_amount'),

            'draft' => $allData->where('status', 'draft')->count(),
            'draft_amount' => $allData->where('status', 'draft')->sum('total_amount'),
        ];

        return view('superadmin.pettycash.pettycash_dashboard', [
            'admin' => $admin,
            'pettycashlist' => $pettycashlist,
            'TblZonesModel' => $TblZonesModel,
            'Tblcompany' => $Tblcompany,
            'Tblvendor' => $Tblvendor,
            'Tblaccount' => $Tblaccount,
            'stats' => $stats,
            'perPage' => $perPage
        ]);
    }

    public function getPettyCashCreate(Request $request)
    {
        $admin = auth()->user();
        $id = $request->id;

        $expenseTypes = DB::table('expense_types')->get();
        $categories   = DB::table('expense_categories')->get();
        $vendors      = DB::table('vendor_tbl')->get();
        $accounts     = DB::table('account_tbl')->get();
        $zones        = DB::table('tblzones')->get();
        $companies    = DB::table('company_tbl')->get();

        $last = PettyCash::latest()->first();

        if ($last && $last->report_id) {
            $num = intval(substr($last->report_id, 4)) + 1;
        } else {
            $num = 1;
        }

        $nextReportId = 'RID-' . str_pad($num, 4, '0', STR_PAD_LEFT);

        $pettycash = null;
        $pettycashItems = [];
        $pettyCashEditContext = null;

        if ($id) {

            $pettycash = PettyCash::with(['vendor', 'zone', 'company', 'branch', 'report'])
                ->find($id);

            if ($pettycash) {
                $pcSt = strtolower((string) ($pettycash->status ?? ''));
                if (in_array($pcSt, ['approved', 'reimbursed'], true)) {
                    return redirect()->route('superadmin.getpettycash')
                        ->with('warning', 'Approved or reimbursed expenses cannot be edited.');
                }

                $pettycashItems = DB::table('petty_cash_items')
                    ->where('petty_cash_id', $id)
                    ->get();

                $code  = $pettycash->report->report_id ?? '';
                $name  = $pettycash->report->report_name ?? '';
                $start = $pettycash->report->start_date ?? '';
                $end   = $pettycash->report->end_date ?? '';

                $pettycash->report_search_display = trim(
                    $code . ' - ' . $name . ' (' . $start . ' to ' . $end . ')'
                );

                $pettycash->zone_name    = $pettycash->zone->name ?? '';
                $pettycash->company_name = $pettycash->company->company_name ?? '';

                $branchIdsCsv = trim((string) ($pettycash->branch_ids ?? ''));
                if ($branchIdsCsv === '' && $pettycash->branch_id) {
                    $branchIdsCsv = (string) $pettycash->branch_id;
                }
                $branchIdList = array_values(array_filter(array_map('intval', explode(',', $branchIdsCsv))));
                $orderedBranchNames = [];
                foreach ($branchIdList as $bid) {
                    $bn = DB::table('tbl_locations')->where('id', $bid)->value('name');
                    if ($bn) {
                        $orderedBranchNames[] = $bn;
                    }
                }
                $pettycash->branch_display_names = implode(', ', $orderedBranchNames);
                $pettycash->branch_name          = $pettycash->branch_display_names ?: ($pettycash->branch->name ?? '');

                $pettyCashEditContext = [
                    'zone_id'         => $pettycash->zone_id,
                    'branch_id'       => $pettycash->branch_id,
                    'branch_ids_csv'  => implode(',', $branchIdList),
                    'company_id'      => $pettycash->company_id,
                    'zone_name'       => $pettycash->zone_name,
                    'branch_name'     => $pettycash->branch_display_names,
                    'company_name'    => $pettycash->company_name,
                ];
            } else {
                $pettycashItems = collect([]);
            }
        } else {
            $pettycashItems = collect([
                (object)[
                    'expense_category_id' => null,
                    'description' => '',
                    'amount' => 0
                ]
            ]);
        }

        $reportsData = ExpenseReport::select(
            'id',
            DB::raw("CONCAT(report_id, ' - ', report_name, ' (', start_date, ' to ', end_date, ')') as report_display")
        )->get();

        return view('superadmin.pettycash.pettycash_create', compact(
            'admin',
            'nextReportId',
            'expenseTypes',
            'categories',
            'vendors',
            'accounts',
            'zones',
            'companies',
            'pettycash',
            'pettycashItems',
            'reportsData',
            'pettyCashEditContext'
        ));
    }

    public function savePettyCash(Request $request)
    {
        $expenseDate = null;

        if ($request->expense_date) {
            $expenseDate = Carbon::createFromFormat('d/m/Y', $request->expense_date)->format('Y-m-d');
        }

        $request->validate([
            'report_id' => 'required|exists:expense_reports,id',
            'report_name' => 'nullable|string|max:255',
            'zone_id' => 'required',
            'branch_id' => 'required|string',
            'company_id' => 'required',
            'items' => 'required|array|min:1',
            'items.*.expense_category_id' => 'required',
            'items.*.amount' => 'required|numeric|min:0',
            'tax_type' => 'nullable|string|max:64',
            'supply_kind' => 'nullable|string|in:goods,service',
            'gstin' => 'nullable|string|max:20',
            'reverse_charge' => 'nullable|boolean',
            'destination_of_supply' => [
                'nullable',
                'string',
                'max:128',
                Rule::requiredIf(in_array($request->input('tax_type'), ['domestic_expense', 'import'], true)),
            ],
            'gst_tax_label' => [
                'nullable',
                'string',
                'max:191',
                Rule::requiredIf(in_array($request->input('tax_type'), ['domestic_expense', 'import'], true)),
            ],
            'sac_hsn' => 'nullable|string|max:64',
            'invoice_no' => 'nullable|string|max:128',
        ]);

        [$primaryBranchId, $branchIdsCsv] = $this->normalizePettyCashBranchPayload($request->branch_id);
        if ($primaryBranchId === null) {
            throw ValidationException::withMessages([
                'branch_id' => ['Please select at least one branch.'],
            ]);
        }

        $rid = (int) $request->report_id;
        $sumBefore = (float) DB::table('petty_cash')->where('report_id', $rid)->sum('total_amount');

        $id = $request->id;

        $expenseType = in_array($request->expense_type, ['single', 'itemized'])
            ? $request->expense_type
            : 'single';

        $receiptPathSingle = null;
        if ($expenseType === 'single' && $request->hasFile('receipt')) {
            $file = $request->file('receipt');
            $dir  = public_path('petty_cash_receipts');
            if (!is_dir($dir)) {
                mkdir($dir, 0775, true);
            }
            $filename = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $file->getClientOriginalName());
            $file->move($dir, $filename);
            $receiptPathSingle = 'petty_cash_receipts/' . $filename;
        }

        $itemizedAttachmentPathsJson = null;
        $itemizedReceiptPath = null;
        if ($expenseType === 'itemized') {
            $existingPcForFiles = $id ? DB::table('petty_cash')->where('id', $id)->first() : null;
            $allowed            = $this->allowedPettyCashAttachmentPathsFromRow($existingPcForFiles);
            $rawExisting        = $request->input('existing_files');
            if ($rawExisting !== null && $rawExisting !== '') {
                $kept = $this->sanitizeKeptPettyCashAttachmentPaths((string) $rawExisting, $allowed);
            } else {
                $kept = $allowed;
            }
            $paths = $this->appendUploadedPettyCashFiles($request, $kept);
            if ($paths !== []) {
                $itemizedAttachmentPathsJson = json_encode(array_values(array_unique($paths)));
                $itemizedReceiptPath         = $paths[0];
            }
        }

        $data = [
            'report_id' => $request->report_id,
            'expense_date' => $expenseDate,
            'vendor_id' => $request->vendor_id,
            'zone_id' => $request->zone_id,
            'company_id' => $request->company_id,
            'branch_id' => $primaryBranchId,
            'branch_ids' => $branchIdsCsv,
            'expense_category_id' => $request->expense_category_id ?? ($request->items[0]['expense_category_id'] ?? null),
            'currency' => $request->currency ?? 'INR',
            'reference_no' => $request->reference_no,
            'claim_reimbursement' => $request->filled('claim_reimbursement') ? 1 : 0,
            'tax_type' => $request->input('tax_type'),
            'supply_kind' => $request->input('supply_kind'),
            'gstin' => $request->input('gstin'),
            'reverse_charge' => $request->boolean('reverse_charge'),
            'destination_of_supply' => $request->input('destination_of_supply'),
            'gst_tax_label' => $request->input('gst_tax_label'),
            'sac_hsn' => $request->input('sac_hsn'),
            'invoice_no' => $request->input('invoice_no'),
            'notes' => $request->has('notes') ? $request->input('notes') : $request->input('reference_no'),
            'total_amount' => $request->total_amount ?? 0,
            'status' => 'pending',
            'expense_type' => $expenseType,
            'updated_by' => auth()->id(),
            'updated_at' => now(),
        ];

        if ($expenseType === 'single') {
            $data['attachment_paths'] = null;
            if ($request->input('remove_receipt') === '1') {
                $data['receipt_path'] = null;
            } elseif ($receiptPathSingle) {
                $data['receipt_path'] = $receiptPathSingle;
            }
        } else {
            $data['attachment_paths'] = $itemizedAttachmentPathsJson;
            $data['receipt_path']     = $itemizedReceiptPath;
        }

        if ($id) {
            $existingPc = DB::table('petty_cash')->where('id', $id)->first();
            if (!$existingPc) {
                return response()->json(['success' => false, 'message' => 'Petty cash not found.'], 404);
            }
            $existingSt = strtolower((string) ($existingPc->status ?? ''));
            if (in_array($existingSt, ['approved', 'reimbursed'], true)) {
                return response()->json([
                    'success' => false,
                    'message' => 'This expense is approved and cannot be modified.',
                ], 422);
            }

            DB::table('petty_cash')
                ->where('id', $id)
                ->update($data);

            $pettyCashId = $id;
            $message = "Petty Cash Updated Successfully!";
        } else {

            $data['created_by'] = auth()->id();
            $data['created_at'] = now();

            $pettyCashId = DB::table('petty_cash')->insertGetId($data);

            $message = "Petty Cash Saved Successfully!";
        }

        if ($pettyCashId) {
            DB::table('petty_cash_items')
                ->where('petty_cash_id', $pettyCashId)
                ->delete();

            foreach ($request->items as $row) {

                $amount = floatval($row['amount'] ?? 0);

                if ($amount <= 0) continue;

                DB::table('petty_cash_items')->insert([
                    'petty_cash_id' => $pettyCashId,
                    'expense_category_id' => $row['expense_category_id'],
                    'description' => $row['description'] ?? null,
                    'amount' => $amount,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        $sumAfter = (float) DB::table('petty_cash')->where('report_id', $rid)->sum('total_amount');
        if (abs($sumBefore - $sumAfter) > 0.00001) {
            PettyCashHistory::record(
                $rid,
                'report_updated',
                'Report updated. Total changed to ₹' . number_format($sumAfter, 2, '.', '')
            );
        }

        return response()->json([
            'success' => true,
            'message' => $message,
            'petty_cash_id' => $pettyCashId,
            'save_and_new' => $request->input('save_action') === 'new',
        ]);
    }

    public function savePettyCashBulk(Request $request)
    {
        $request->validate([
            'report_id' => 'required|exists:expense_reports,id',
            'zone_id' => 'required',
            'branch_id' => 'required|string',
            'company_id' => 'required',
            'bulk_rows' => 'required|array|min:1',
        ]);

        [$bulkPrimaryBranchId, $bulkBranchIdsCsv] = $this->normalizePettyCashBranchPayload($request->branch_id);
        if ($bulkPrimaryBranchId === null) {
            throw ValidationException::withMessages([
                'branch_id' => ['Please select at least one branch.'],
            ]);
        }

        $created   = 0;
        $reportId  = (int) $request->report_id;
        $sumBefore = (float) DB::table('petty_cash')->where('report_id', $reportId)->sum('total_amount');

        DB::transaction(function () use ($request, &$created, $bulkPrimaryBranchId, $bulkBranchIdsCsv) {
            foreach ($request->bulk_rows as $row) {
                $dateStr = trim((string) ($row['expense_date'] ?? ''));
                if ($dateStr === '') {
                    continue;
                }

                $catId = $row['expense_category_id'] ?? null;
                if ($catId === null || $catId === '') {
                    continue;
                }

                try {
                    $expenseDate = Carbon::createFromFormat('d/m/Y', $dateStr)->format('Y-m-d');
                } catch (\Throwable $e) {
                    throw ValidationException::withMessages([
                        'bulk_rows' => ['Invalid petty cash date format (use dd/mm/yyyy).'],
                    ]);
                }

                $amount = floatval($row['amount'] ?? 0);
                if ($amount <= 0) {
                    continue;
                }

                $vendorId = ! empty($row['vendor_id']) ? (int) $row['vendor_id'] : null;

                $data = [
                    'report_id' => (int) $request->report_id,
                    'expense_date' => $expenseDate,
                    'vendor_id' => $vendorId,
                    'zone_id' => (int) $request->zone_id,
                    'company_id' => (int) $request->company_id,
                    'branch_id' => $bulkPrimaryBranchId,
                    'branch_ids' => $bulkBranchIdsCsv,
                    'expense_category_id' => (int) $catId,
                    'currency' => $request->input('currency', 'INR'),
                    'reference_no' => $row['reference_no'] ?? null,
                    'claim_reimbursement' => ! empty($row['claim_reimbursement']) ? 1 : 0,
                    'notes' => null,
                    'total_amount' => $amount,
                    'status' => 'pending',
                    'created_by' => auth()->id(),
                    'created_at' => now(),
                    'updated_by' => auth()->id(),
                    'updated_at' => now(),
                ];

                $pettyCashId = DB::table('petty_cash')->insertGetId($data);

                DB::table('petty_cash_items')->insert([
                    'petty_cash_id' => $pettyCashId,
                    'expense_category_id' => (int) $catId,
                    'description' => $row['description'] ?? null,
                    'amount' => $amount,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                ++$created;
            }
        });

        if ($created === 0) {
            throw ValidationException::withMessages([
                'bulk_rows' => ['No valid rows to save (check dates and amounts).'],
            ]);
        }

        $sumAfter = (float) DB::table('petty_cash')->where('report_id', $reportId)->sum('total_amount');
        if (abs($sumBefore - $sumAfter) > 0.00001) {
            PettyCashHistory::record(
                $reportId,
                'report_updated',
                'Report updated. Total changed to ₹' . number_format($sumAfter, 2, '.', '')
            );
        }

        return response()->json([
            'success' => true,
            'message' => $created . ' petty cash ' . ($created === 1 ? 'entry' : 'entries') . ' saved.',
            'created' => $created,
        ]);
    }

    public function pettyCashApprover(Request $request)
    {
        $request->validate([
            'approver_id' => 'required|integer|exists:petty_cash,id',
            'value' => 'required|string',
            'reason' => 'nullable|string'
        ]);

        $id = $request->input('approver_id');
        $value = strtolower($request->input('value'));
        $reason = $request->input('reason');

        // Normalize
        if (in_array($value, ['approve', 'approved'], true)) {
            $value = 'approved';
        } elseif (in_array($value, ['reject', 'rejected'], true)) {
            $value = 'rejected';
        } else {
            return response()->json(['success' => false, 'message' => 'Invalid status provided.'], 422);
        }

        if ($value === 'rejected' && empty($reason)) {
            return response()->json([
                'success' => false,
                'message' => 'Please enter reject reason'
            ], 422);
        }

        $updateData = [
            'status' => $value,
            'updated_by' => auth()->id(),
            'updated_at' => now()
        ];

        if ($value === 'rejected') {
            $updateData['reject_reason'] = $reason;
        }

        DB::table('petty_cash')
            ->where('id', $id)
            ->update($updateData);

        $label = $value === 'approved' ? 'Approved' : 'Rejected';

        return response()->json([
            'success' => true,
            'message' => "Petty Cash {$label} successfully."
        ]);
    }

    public function downloadPettyCashTemplate()
    {
        return Excel::download(new PettyCashTemplateExport(), 'petty_cash_import_template.xlsx');
    }

    public function importPettyCashExcel(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv',
        ]);

        Excel::import(new PettyCashImport(), $request->file('file'));

        return redirect()->route('superadmin.getpettycash')
            ->with('success', 'Petty cash import has been processed.');
    }

    public function exportPettyCash(Request $request)
    {
        $format = $request->get('format', 'xlsx');
        if (!in_array($format, ['csv', 'xlsx'])) {
            $format = 'xlsx';
        }

        $writerType = $format === 'csv'
            ? Excel::CSV
            : Excel::XLSX;

        $fileName = 'Petty_Cash_' . now()->format('Y_m_d_His') . '.' . $format;

        return Excel::download(
            new PettyCashExport($request, $format),
            $fileName,
            $writerType,
            [
                'Content-Type' => $format === 'csv'
                    ? 'text/csv'
                    : 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ]
        );
    }

    public function getPettyCashReports(Request $request)
    {
        $admin = auth()->user();
        $TblZonesModel = TblZonesModel::orderBy('id', 'asc')->paginate(10);
        $Tblcompany = Tblcompany::orderBy('id', 'asc')->paginate(10);

        $stats = [
            'total' => 0,
            'total_amount' => 0,
            'approved' => 0,
            'approved_amount' => 0,
            'pending' => 0,
            'pending_amount' => 0,
            'rejected' => 0,
            'rejected_amount' => 0,
            'draft' => 0,
            'draft_amount' => 0,
        ];

        return view(
            'superadmin.pettycash.pettycash_reports',
            compact('admin', 'TblZonesModel', 'Tblcompany', 'stats')
        );
    }

    /**
     * Base query for petty cash report line items (aligned with petty cash dashboard / Zoho-style expense lines).
     */
    protected function pettyCashReportsLineQueryBase(): \Illuminate\Database\Query\Builder
    {
        return DB::table('petty_cash')
            ->leftJoin('vendor_tbl', 'vendor_tbl.id', '=', 'petty_cash.vendor_id')
            ->leftJoin('expense_categories', 'expense_categories.id', '=', 'petty_cash.expense_category_id')
            ->leftJoin('tblzones', 'tblzones.id', '=', 'petty_cash.zone_id')
            ->leftJoin('company_tbl', 'company_tbl.id', '=', 'petty_cash.company_id')
            ->leftJoin('tbl_locations', 'tbl_locations.id', '=', 'petty_cash.branch_id')
            ->leftJoin('expense_reports', 'expense_reports.id', '=', 'petty_cash.report_id')
            ->select(
                'petty_cash.*',
                'vendor_tbl.display_name as vendor_name',
                'expense_categories.name as category_name',
                'tblzones.name as zone_name',
                'company_tbl.company_name as company_name',
                'tbl_locations.name as branch_name',
                'expense_reports.report_id as expense_report_code',
                'expense_reports.report_name as expense_report_name'
            );
    }

    /**
     * Apply shared filters to petty cash line report query (same rules as getPettyCashAjax).
     */
    protected function applyPettyCashReportsLineFilters(\Illuminate\Database\Query\Builder $query, Request $request): void
    {
        if ($request->boolean('pending_approvals_only')) {
            $query->where('petty_cash.status', 'pending');
        }

        if ($request->filled('date_from') && $request->filled('date_to')) {
            $query->whereBetween('petty_cash.expense_date', [
                Carbon::createFromFormat('d/m/Y', $request->date_from)->startOfDay(),
                Carbon::createFromFormat('d/m/Y', $request->date_to)->endOfDay(),
            ]);
        }

        if ($request->filled('zone_id')) {
            $zoneIds = array_values(array_filter(array_map('intval', explode(',', $request->zone_id))));
            if ($zoneIds !== []) {
                $query->whereIn('petty_cash.zone_id', $zoneIds);
            }
        }

        if ($request->filled('branch_id')) {
            $branchIds = array_values(array_filter(array_map('intval', explode(',', $request->branch_id))));
            if ($branchIds !== []) {
                $query->whereIn('petty_cash.branch_id', $branchIds);
            }
        }

        if ($request->filled('company_id')) {
            $companyIds = array_values(array_filter(array_map('intval', explode(',', $request->company_id))));
            if ($companyIds !== []) {
                $query->whereIn('petty_cash.company_id', $companyIds);
            }
        }

        if ($request->filled('vendor_id')) {
            $vendorIds = array_values(array_filter(array_map('intval', explode(',', $request->vendor_id))));
            if ($vendorIds !== []) {
                $query->whereIn('petty_cash.vendor_id', $vendorIds);
            }
        }

        if ($request->filled('status_name')) {
            $rawStatuses = explode(',', $request->status_name);
            $statuses = [];
            foreach ($rawStatuses as $s) {
                $s = strtolower(trim((string) $s));
                if ($s === '') {
                    continue;
                }
                if (in_array($s, ['approve', 'approved'], true)) {
                    $statuses[] = 'approved';
                } elseif (in_array($s, ['reject', 'rejected'], true)) {
                    $statuses[] = 'rejected';
                } elseif ($s === 'save') {
                    $statuses[] = 'pending';
                } else {
                    $statuses[] = $s;
                }
            }
            $statuses = array_values(array_unique($statuses));
            if ($statuses !== []) {
                $query->whereIn('petty_cash.status', $statuses);
            }
        }

        if ($request->filled('universal_search')) {
            $search = $request->universal_search;
            $query->where(function ($q) use ($search) {
                $q->where('expense_reports.report_id', 'LIKE', '%' . $search . '%')
                    ->orWhere('expense_reports.report_name', 'LIKE', '%' . $search . '%')
                    ->orWhere('vendor_tbl.display_name', 'LIKE', '%' . $search . '%')
                    ->orWhere('expense_categories.name', 'LIKE', '%' . $search . '%')
                    ->orWhere('tblzones.name', 'LIKE', '%' . $search . '%')
                    ->orWhere('company_tbl.company_name', 'LIKE', '%' . $search . '%')
                    ->orWhere('tbl_locations.name', 'LIKE', '%' . $search . '%')
                    ->orWhere('petty_cash.reference_no', 'LIKE', '%' . $search . '%');
            });
        }
    }

    /**
     * Line-level roll-up mapped to expense report workflow labels (used when status is null).
     */
    protected function expenseReportRollupWorkflowSql(): string
    {
        return "(CASE
            WHEN SUM(CASE WHEN LOWER(pc.status) IN ('rejected','reject') THEN 1 ELSE 0 END) > 0 THEN 'rejected'
            WHEN SUM(CASE WHEN LOWER(pc.status) = 'pending' THEN 1 ELSE 0 END) > 0 THEN 'pending_approval'
            WHEN SUM(CASE WHEN LOWER(pc.status) = 'draft' THEN 1 ELSE 0 END) > 0 THEN 'draft'
            ELSE 'approved' END)";
    }

    protected function expenseReportEffectiveWorkflowSql(): string
    {
        $rollup = $this->expenseReportRollupWorkflowSql();

        return "COALESCE(MAX(er.status), {$rollup})";
    }

    protected function expenseReportAdvanceAppliedSubquerySql(): string
    {
        return "(SELECT COALESCE(SUM(a.advance_amount),0) FROM advances a WHERE a.report_id = er.id AND a.status NOT IN ('rejected','draft'))";
    }

    protected function expenseReportReimbursableSumSql(): string
    {
        return 'COALESCE(SUM(CASE WHEN pc.claim_reimbursement = 1 THEN pc.total_amount ELSE 0 END),0)';
    }

    protected function actorInitials(?string $name): string
    {
        if ($name === null || trim($name) === '') {
            return '?';
        }
        $parts = preg_split('/\s+/', trim($name));
        $parts = array_values(array_filter($parts));
        $s = '';
        foreach (array_slice($parts, 0, 2) as $p) {
            $s .= strtoupper(substr($p, 0, 1));
        }

        return $s !== '' ? $s : '?';
    }

    /**
     * Effective workflow for a single expense report (DB column overrides line roll-up).
     */
    protected function getExpenseReportEffectiveWorkflow(int $erId): string
    {
        $report = DB::table('expense_reports')->where('id', $erId)->first();
        if (!$report) {
            return '';
        }
        $stored = $report->status ?? null;
        if ($stored !== null && $stored !== '') {
            return (string) $stored;
        }

        $rows = DB::table('petty_cash')->where('report_id', $erId)->pluck('status');
        if ($rows->isEmpty()) {
            return 'draft';
        }
        foreach ($rows as $st) {
            $s = strtolower((string) $st);
            if (in_array($s, ['rejected', 'reject'], true)) {
                return 'rejected';
            }
        }
        foreach ($rows as $st) {
            if (strtolower((string) $st) === 'pending') {
                return 'pending_approval';
            }
        }
        foreach ($rows as $st) {
            if (strtolower((string) $st) === 'draft') {
                return 'draft';
            }
        }

        return 'approved';
    }

    /**
     * Stats for line-item report (matches dashboard card semantics).
     */
    protected function computePettyCashReportLineStats(\Illuminate\Database\Query\Builder $statsBase): array
    {
        return [
            'total' => (clone $statsBase)->count(),
            'total_amount' => (clone $statsBase)->sum('petty_cash.total_amount'),
            'approved' => (clone $statsBase)->where('petty_cash.status', 'approved')->count(),
            'approved_amount' => (clone $statsBase)->where('petty_cash.status', 'approved')->sum('petty_cash.total_amount'),
            'pending' => (clone $statsBase)->where('petty_cash.status', 'pending')->count(),
            'pending_amount' => (clone $statsBase)->where('petty_cash.status', 'pending')->sum('petty_cash.total_amount'),
            'rejected' => (clone $statsBase)->where('petty_cash.status', 'rejected')->count(),
            'rejected_amount' => (clone $statsBase)->where('petty_cash.status', 'rejected')->sum('petty_cash.total_amount'),
            'draft' => (clone $statsBase)->where('petty_cash.status', 'draft')->count(),
            'draft_amount' => (clone $statsBase)->where('petty_cash.status', 'draft')->sum('petty_cash.total_amount'),
        ];
    }

    public function getPettyCashReportsAjax(Request $request)
    {
        if ($request->input('report_view') === 'pending') {
            $request->merge([
                'report_view' => 'summary',
                'report_workflow_tab' => 'pending_approval',
            ]);
        }

        $perPage = (int) ($request->per_page ?? 15);
        $view = $request->input('report_view', 'lines');

        if ($view === 'summary') {
            return $this->getPettyCashReportsAjaxSummary($request, $perPage);
        }

        $query = $this->pettyCashReportsLineQueryBase();
        $this->applyPettyCashReportsLineFilters($query, $request);

        $pettycashlist = (clone $query)->orderBy('petty_cash.id', 'desc')->paginate($perPage);
        $statsBase = clone $query;
        $pettycashStats = $this->computePettyCashReportLineStats($statsBase);

        $html = view('superadmin.pettycash.pettycash_reports_rows', [
            'list' => $pettycashlist,
            'view' => 'lines',
        ])->render();

        $pagination = $pettycashlist->appends($request->all())->links('pagination::bootstrap-4')->toHtml();

        return response()->json([
            'html' => $html,
            'pagination' => $pagination,
            'stats' => $pettycashStats,
            'report_view' => 'lines',
            'total' => $pettycashlist->total(),
            'per_page' => $pettycashlist->perPage(),
            'current_page' => $pettycashlist->currentPage(),
            'last_page' => $pettycashlist->lastPage(),
        ]);
    }

    /**
     * Zoho-style aggregated rows: one row per expense report with rolled-up status and totals.
     */
    protected function getPettyCashReportsAjaxSummary(Request $request, int $perPage)
    {
        $rollupWorkflow = $this->expenseReportRollupWorkflowSql();
        $effectiveWorkflow = $this->expenseReportEffectiveWorkflowSql();
        $advanceSumSql = $this->expenseReportAdvanceAppliedSubquerySql();
        $reimbursableSumSql = $this->expenseReportReimbursableSumSql();
        $toReimburseSql = "(CASE WHEN ({$effectiveWorkflow}) = 'reimbursed' THEN 0 ELSE GREATEST(0, {$reimbursableSumSql} - {$advanceSumSql}) END)";

        $query = DB::table('expense_reports as er')
            ->join('petty_cash as pc', 'pc.report_id', '=', 'er.id')
            ->leftJoin('tblzones', 'tblzones.id', '=', 'pc.zone_id')
            ->leftJoin('company_tbl', 'company_tbl.id', '=', 'pc.company_id')
            ->leftJoin('tbl_locations', 'tbl_locations.id', '=', 'pc.branch_id')
            ->leftJoin('vendor_tbl', 'vendor_tbl.id', '=', 'pc.vendor_id')
            ->leftJoin('users', 'users.id', '=', 'er.created_by')
            ->select(
                'er.id as er_id',
                'er.report_id as expense_report_code',
                'er.report_name',
                'er.start_date',
                'er.end_date',
                'er.status',
                'er.submitted_at',
                'er.approved_at',
                'er.reimbursed_at',
                'er.approver_name',
                DB::raw('MAX(COALESCE(NULLIF(TRIM(users.user_fullname), ""), users.username)) as submitter_name'),
                DB::raw('COUNT(pc.id) as entry_count'),
                DB::raw('COALESCE(SUM(pc.total_amount),0) as total_amount'),
                DB::raw("{$reimbursableSumSql} as reimbursable_amount"),
                DB::raw('COALESCE(SUM(CASE WHEN COALESCE(pc.claim_reimbursement,0) = 0 THEN pc.total_amount ELSE 0 END),0) as non_reimbursable_amount'),
                DB::raw($rollupWorkflow . ' as rollup_status'),
                DB::raw("{$effectiveWorkflow} as workflow_status"),
                DB::raw("{$advanceSumSql} as advance_applied_total"),
                DB::raw("{$toReimburseSql} as to_reimburse_amount")
            )
            ->groupBy(
                'er.id',
                'er.report_id',
                'er.report_name',
                'er.start_date',
                'er.end_date',
                'er.status',
                'er.submitted_at',
                'er.approved_at',
                'er.reimbursed_at',
                'er.approver_name',
                'er.created_by'
            );

        if ($request->filled('date_from') && $request->filled('date_to')) {
            $query->whereBetween('pc.expense_date', [
                Carbon::createFromFormat('d/m/Y', $request->date_from)->startOfDay(),
                Carbon::createFromFormat('d/m/Y', $request->date_to)->endOfDay(),
            ]);
        }

        if ($request->filled('zone_id')) {
            $zoneIds = array_values(array_filter(array_map('intval', explode(',', $request->zone_id))));
            if ($zoneIds !== []) {
                $query->whereIn('pc.zone_id', $zoneIds);
            }
        }

        if ($request->filled('branch_id')) {
            $branchIds = array_values(array_filter(array_map('intval', explode(',', $request->branch_id))));
            if ($branchIds !== []) {
                $query->whereIn('pc.branch_id', $branchIds);
            }
        }

        if ($request->filled('company_id')) {
            $companyIds = array_values(array_filter(array_map('intval', explode(',', $request->company_id))));
            if ($companyIds !== []) {
                $query->whereIn('pc.company_id', $companyIds);
            }
        }

        if ($request->filled('vendor_id')) {
            $vendorIds = array_values(array_filter(array_map('intval', explode(',', $request->vendor_id))));
            if ($vendorIds !== []) {
                $query->whereIn('pc.vendor_id', $vendorIds);
            }
        }

        if ($request->filled('universal_search')) {
            $search = $request->universal_search;
            $query->where(function ($q) use ($search) {
                $q->where('er.report_id', 'LIKE', '%' . $search . '%')
                    ->orWhere('er.report_name', 'LIKE', '%' . $search . '%')
                    ->orWhere('vendor_tbl.display_name', 'LIKE', '%' . $search . '%')
                    ->orWhere('tblzones.name', 'LIKE', '%' . $search . '%')
                    ->orWhere('company_tbl.company_name', 'LIKE', '%' . $search . '%')
                    ->orWhere('tbl_locations.name', 'LIKE', '%' . $search . '%');
            });
        }

        if ($request->boolean('pending_approvals_only')) {
            $query->havingRaw("({$effectiveWorkflow}) = ?", ['pending_approval']);
        } elseif ($request->filled('status_name')) {
            $rawStatuses = explode(',', $request->status_name);
            $statuses = [];
            foreach ($rawStatuses as $s) {
                $s = strtolower(trim((string) $s));
                if ($s === '') {
                    continue;
                }
                if (in_array($s, ['approve', 'approved'], true)) {
                    $statuses[] = 'approved';
                } elseif (in_array($s, ['reject', 'rejected'], true)) {
                    $statuses[] = 'rejected';
                } elseif ($s === 'save' || $s === 'pending') {
                    $statuses[] = 'pending_approval';
                } elseif ($s === 'draft') {
                    $statuses[] = 'draft';
                } else {
                    $statuses[] = $s;
                }
            }
            $statuses = array_values(array_unique($statuses));
            if ($statuses !== []) {
                $query->havingRaw(
                    "({$effectiveWorkflow}) in (" . implode(',', array_fill(0, count($statuses), '?')) . ')',
                    $statuses
                );
            }
        }

        $allGroups = (clone $query)->orderBy('er.id', 'desc')->get();

        $workflowCounts = [
            'all' => $allGroups->count(),
            'pending_approval' => $allGroups->where('workflow_status', 'pending_approval')->count(),
            'approved' => $allGroups->where('workflow_status', 'approved')->count(),
            'reimbursed' => $allGroups->where('workflow_status', 'reimbursed')->count(),
            'draft' => $allGroups->where('workflow_status', 'draft')->count(),
            'rejected' => $allGroups->where('workflow_status', 'rejected')->count(),
        ];

        $pettycashStats = [
            'total' => $allGroups->count(),
            'total_amount' => $allGroups->sum('total_amount'),
            'approved' => $allGroups->where('workflow_status', 'approved')->count(),
            'approved_amount' => $allGroups->where('workflow_status', 'approved')->sum('total_amount'),
            'pending' => $allGroups->where('workflow_status', 'pending_approval')->count(),
            'pending_amount' => $allGroups->where('workflow_status', 'pending_approval')->sum('total_amount'),
            'rejected' => $allGroups->where('workflow_status', 'rejected')->count(),
            'rejected_amount' => $allGroups->where('workflow_status', 'rejected')->sum('total_amount'),
            'draft' => $allGroups->where('workflow_status', 'draft')->count(),
            'draft_amount' => $allGroups->where('workflow_status', 'draft')->sum('total_amount'),
        ];

        $tab = $request->input('report_workflow_tab', 'all');
        $listQuery = clone $query;
        if ($tab !== 'all' && in_array($tab, ['pending_approval', 'approved', 'reimbursed', 'draft', 'rejected'], true)) {
            $listQuery->havingRaw("({$effectiveWorkflow}) = ?", [$tab]);
        }

        $list = $listQuery->orderBy('er.id', 'desc')->paginate($perPage);

        $html = view('superadmin.pettycash.pettycash_reports_rows', [
            'list' => $list,
            'view' => 'summary',
        ])->render();

        $pagination = $list->appends($request->all())->links('pagination::bootstrap-4')->toHtml();

        return response()->json([
            'html' => $html,
            'pagination' => $pagination,
            'stats' => $pettycashStats,
            'workflow_counts' => $workflowCounts,
            'report_view' => 'summary',
            'total' => $list->total(),
            'per_page' => $list->perPage(),
            'current_page' => $list->currentPage(),
            'last_page' => $list->lastPage(),
        ]);
    }

    public function exportPettyCashReports(Request $request)
    {
        if ($request->input('report_view') === 'pending') {
            $request->merge([
                'report_view' => 'summary',
                'report_workflow_tab' => 'pending_approval',
            ]);
        }

        $view = $request->input('report_view', 'lines');

        if ($view === 'summary') {
            $rollupWorkflow = $this->expenseReportRollupWorkflowSql();
            $effectiveWorkflow = $this->expenseReportEffectiveWorkflowSql();
            $advanceSumSql = $this->expenseReportAdvanceAppliedSubquerySql();
            $reimbursableSumSql = $this->expenseReportReimbursableSumSql();
            $toReimburseSql = "(CASE WHEN ({$effectiveWorkflow}) = 'reimbursed' THEN 0 ELSE GREATEST(0, {$reimbursableSumSql} - {$advanceSumSql}) END)";

            $q = DB::table('expense_reports as er')
                ->join('petty_cash as pc', 'pc.report_id', '=', 'er.id')
                ->leftJoin('tblzones', 'tblzones.id', '=', 'pc.zone_id')
                ->leftJoin('company_tbl', 'company_tbl.id', '=', 'pc.company_id')
                ->leftJoin('tbl_locations', 'tbl_locations.id', '=', 'pc.branch_id')
                ->leftJoin('vendor_tbl', 'vendor_tbl.id', '=', 'pc.vendor_id')
                ->leftJoin('users', 'users.id', '=', 'er.created_by')
                ->select(
                    'er.id as er_id',
                    'er.report_id as expense_report_code',
                    'er.report_name',
                    'er.start_date',
                    'er.end_date',
                    'er.approver_name',
                    DB::raw('MAX(COALESCE(NULLIF(TRIM(users.user_fullname), ""), users.username)) as submitter_name'),
                    DB::raw('COUNT(pc.id) as entry_count'),
                    DB::raw('COALESCE(SUM(pc.total_amount),0) as total_amount'),
                    DB::raw("{$reimbursableSumSql} as reimbursable_amount"),
                    DB::raw('COALESCE(SUM(CASE WHEN COALESCE(pc.claim_reimbursement,0) = 0 THEN pc.total_amount ELSE 0 END),0) as non_reimbursable_amount'),
                    DB::raw($rollupWorkflow . ' as rollup_status'),
                    DB::raw("{$effectiveWorkflow} as workflow_status"),
                    DB::raw("{$advanceSumSql} as advance_applied_total"),
                    DB::raw("{$toReimburseSql} as to_reimburse_amount")
                )
                ->groupBy(
                    'er.id',
                    'er.report_id',
                    'er.report_name',
                    'er.start_date',
                    'er.end_date',
                    'er.status',
                    'er.submitted_at',
                    'er.approved_at',
                    'er.reimbursed_at',
                    'er.approver_name',
                    'er.created_by'
                );

            if ($request->filled('date_from') && $request->filled('date_to')) {
                $q->whereBetween('pc.expense_date', [
                    Carbon::createFromFormat('d/m/Y', $request->date_from)->startOfDay(),
                    Carbon::createFromFormat('d/m/Y', $request->date_to)->endOfDay(),
                ]);
            }
            if ($request->filled('zone_id')) {
                $zoneIds = array_values(array_filter(array_map('intval', explode(',', $request->zone_id))));
                if ($zoneIds !== []) {
                    $q->whereIn('pc.zone_id', $zoneIds);
                }
            }
            if ($request->filled('branch_id')) {
                $branchIds = array_values(array_filter(array_map('intval', explode(',', $request->branch_id))));
                if ($branchIds !== []) {
                    $q->whereIn('pc.branch_id', $branchIds);
                }
            }
            if ($request->filled('company_id')) {
                $companyIds = array_values(array_filter(array_map('intval', explode(',', $request->company_id))));
                if ($companyIds !== []) {
                    $q->whereIn('pc.company_id', $companyIds);
                }
            }
            if ($request->filled('vendor_id')) {
                $vendorIds = array_values(array_filter(array_map('intval', explode(',', $request->vendor_id))));
                if ($vendorIds !== []) {
                    $q->whereIn('pc.vendor_id', $vendorIds);
                }
            }
            if ($request->filled('universal_search')) {
                $search = $request->universal_search;
                $q->where(function ($sub) use ($search) {
                    $sub->where('er.report_id', 'LIKE', '%' . $search . '%')
                        ->orWhere('er.report_name', 'LIKE', '%' . $search . '%')
                        ->orWhere('vendor_tbl.display_name', 'LIKE', '%' . $search . '%')
                        ->orWhere('tblzones.name', 'LIKE', '%' . $search . '%')
                        ->orWhere('company_tbl.company_name', 'LIKE', '%' . $search . '%')
                        ->orWhere('tbl_locations.name', 'LIKE', '%' . $search . '%');
                });
            }
            if ($request->boolean('pending_approvals_only')) {
                $q->havingRaw("({$effectiveWorkflow}) = ?", ['pending_approval']);
            } elseif ($request->filled('status_name')) {
                $rawStatuses = explode(',', $request->status_name);
                $statuses = [];
                foreach ($rawStatuses as $s) {
                    $s = strtolower(trim((string) $s));
                    if ($s === '') {
                        continue;
                    }
                    if (in_array($s, ['approve', 'approved'], true)) {
                        $statuses[] = 'approved';
                    } elseif (in_array($s, ['reject', 'rejected'], true)) {
                        $statuses[] = 'rejected';
                    } elseif ($s === 'save' || $s === 'pending') {
                        $statuses[] = 'pending_approval';
                    } elseif ($s === 'draft') {
                        $statuses[] = 'draft';
                    } else {
                        $statuses[] = $s;
                    }
                }
                $statuses = array_values(array_unique($statuses));
                if ($statuses !== []) {
                    $q->havingRaw(
                        "({$effectiveWorkflow}) in (" . implode(',', array_fill(0, count($statuses), '?')) . ')',
                        $statuses
                    );
                }
            }

            $tab = $request->input('report_workflow_tab', 'all');
            if ($tab !== 'all' && in_array($tab, ['pending_approval', 'approved', 'reimbursed', 'draft', 'rejected'], true)) {
                $q->havingRaw("({$effectiveWorkflow}) = ?", [$tab]);
            }

            $summaryRows = $q->orderBy('er.id', 'desc')->get();
            $csvHeaders = [
                'Report ID',
                'Report name',
                'Period start',
                'Period end',
                'Expense count',
                'Total',
                'Reimbursable',
                'Non-reimbursable',
                'Workflow status',
                'Submitter',
                'Approver',
                'To reimburse',
                'Advance applied',
            ];

            $callback = function () use ($summaryRows, $csvHeaders) {
                $file = fopen('php://output', 'w');
                fputcsv($file, $csvHeaders);
                foreach ($summaryRows as $r) {
                    fputcsv($file, [
                        $r->expense_report_code ?? '',
                        $r->report_name ?? '',
                        $r->start_date ?? '',
                        $r->end_date ?? '',
                        $r->entry_count ?? 0,
                        $r->total_amount ?? 0,
                        $r->reimbursable_amount ?? 0,
                        $r->non_reimbursable_amount ?? 0,
                        $r->workflow_status ?? '',
                        $r->submitter_name ?? '',
                        $r->approver_name ?? '',
                        $r->to_reimburse_amount ?? 0,
                        $r->advance_applied_total ?? 0,
                    ]);
                }
                fclose($file);
            };

            $fileName = 'petty_cash_expense_reports_' . now()->format('Ymd_His') . '.csv';

            return response()->streamDownload($callback, $fileName, [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
            ]);
        }

        $query = $this->pettyCashReportsLineQueryBase();
        $this->applyPettyCashReportsLineFilters($query, $request);

        $reportRows = $query->orderBy('petty_cash.id', 'desc')->get();

        $csvHeaders = [
            'Date',
            'Report ID',
            'Report Name',
            'Vendor',
            'Zone',
            'Company',
            'Branch',
            'Category',
            'Amount',
            'Status',
            'Reference No',
            'Notes',
        ];

        $callback = function () use ($reportRows, $csvHeaders) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $csvHeaders);

            foreach ($reportRows as $r) {
                fputcsv($file, [
                    $r->expense_date ? Carbon::parse($r->expense_date)->format('d/m/Y') : '',
                    $r->expense_report_code ?? '',
                    $r->expense_report_name ?? '',
                    $r->vendor_name,
                    $r->zone_name,
                    $r->company_name,
                    $r->branch_name,
                    $r->category_name,
                    $r->total_amount,
                    ucfirst((string) $r->status),
                    $r->reference_no,
                    $r->notes,
                ]);
            }
            fclose($file);
        };

        $fileName = 'petty_cash_reports_' . now()->format('Ymd_His') . '.csv';

        return response()->streamDownload($callback, $fileName, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ]);
    }

    // ADVANCES MODULE

    public function getAdvances(Request $request)
    {
        $perPage = $request->per_page ?? 15;
        $admin = auth()->user();

        $advanceslist = Advance::with(['vendor', 'zone', 'company', 'branch', 'report'])
            ->orderBy('id', 'desc')
            ->paginate($perPage);

        $TblZonesModel = TblZonesModel::orderBy('id', 'asc')->get();
        $Tblcompany    = Tblcompany::orderBy('id', 'asc')->get();

        $advStatRows = DB::table('advances')->select('status', 'advance_amount', 'report_id')->get();
        $advStatAgg  = static function ($rows, string $status): array {
            $f = $rows->filter(static fn($r) => strtolower((string) ($r->status ?? '')) === $status);

            return [(int) $f->count(), (float) $f->sum('advance_amount')];
        };
        [$dC, $dA] = $advStatAgg($advStatRows, 'draft');
        $pendingUnreported = $advStatRows->filter(static function ($r) {
            if (strtolower((string) ($r->status ?? '')) !== 'pending') {
                return false;
            }

            return (int) ($r->report_id ?? 0) === 0;
        });
        $pC = (int) $pendingUnreported->count();
        $pA = (float) $pendingUnreported->sum('advance_amount');
        [$apC, $apA] = $advStatAgg($advStatRows, 'applied');
        [$clC, $clA] = $advStatAgg($advStatRows, 'closed');
        [$rjC, $rjA] = $advStatAgg($advStatRows, 'rejected');
        $advancesStats = [
            'total'           => $advStatRows->count(),
            'total_amount'    => (float) $advStatRows->sum('advance_amount'),
            'draft'           => $dC,
            'draft_amount'    => $dA,
            'pending'         => $pC,
            'pending_amount'  => $pA,
            'applied'         => $apC,
            'applied_amount'  => $apA,
            'closed'          => $clC,
            'closed_amount'   => $clA,
            'rejected'        => $rjC,
            'rejected_amount' => $rjA,
        ];

        return view('superadmin.pettycash.advances_dashboard', compact(
            'admin',
            'advanceslist',
            'advancesStats',
            'perPage',
            'TblZonesModel',
            'Tblcompany'
        ));
    }

    public function getAdvancesCreate(Request $request)
    {
        $admin = auth()->user();
        $id        = $request->id;
        $zones     = DB::table('tblzones')->get();
        $companies = DB::table('company_tbl')->get();
        $vendors   = DB::table('vendor_tbl')->get();

        $advance     = null;
        $prevAdvance = null;
        $nextAdvance = null;
        $monthBalance = null; // balance summary for same branch/company/month

        $prefillExpenseReportId = (!$id && $request->filled('expense_report_id'))
            ? (int) $request->expense_report_id
            : null;

        if ($id) {
            $advance = Advance::with(['vendor', 'zone', 'company', 'branch', 'report'])->find($id);
            if ($advance) {
                $advance->zone_name           = $advance->zone->name ?? '';
                $advance->branch_name         = $advance->branch->name ?? '';
                $advance->company_name        = $advance->company->company_name ?? '';
                $advance->vendor_display_name = $advance->vendor->display_name ?? '';
                $advance->recorded_by_name    = $advance->created_by
                    ? (DB::table('users')->where('id', $advance->created_by)->value('user_fullname') ?? '')
                    : '';

                $branchLabelIds = $this->advanceBranchIdListFromRow($advance);
                if ($branchLabelIds !== []) {
                    $orderedNames = DB::table('tbl_locations')->whereIn('id', $branchLabelIds)->orderBy('id')->pluck('name', 'id');
                    $parts          = [];
                    foreach ($branchLabelIds as $bid) {
                        if (isset($orderedNames[$bid])) {
                            $parts[] = $orderedNames[$bid];
                        }
                    }
                    if ($parts !== []) {
                        $advance->branch_name = implode(', ', $parts);
                    }
                }

                $prevAdvance = DB::table('advances')->where('id', '<', $id)->orderBy('id', 'desc')->first();
                $nextAdvance = DB::table('advances')->where('id', '>', $id)->orderBy('id', 'asc')->first();

                // Compute this month's balance summary (excluding current record)
                if ($advance->advance_date) {
                    $month = Carbon::parse($advance->advance_date)->format('Y-m');
                    $peers = DB::table('advances')
                        ->where('id', '!=', $id)
                        ->whereNotIn('status', ['rejected', 'draft']);
                    if ($this->advanceBranchIdListFromRow($advance) !== []) {
                        $this->applyAdvancePeerBranchScope($peers, $advance);
                    }
                    $peers = $peers->when($advance->company_id, fn($q) => $q->where('company_id', $advance->company_id))
                        ->whereRaw("DATE_FORMAT(advance_date, '%Y-%m') = ?", [$month])
                        ->select('advance_amount', 'used_amount', 'balance_amount', 'advance_date', 'status', 'reference_no')
                        ->get();

                    $prevMonth = Carbon::parse($advance->advance_date)->subMonth()->format('Y-m');
                    $prevBalQ  = DB::table('advances')
                        ->whereNotIn('status', ['rejected', 'draft']);
                    if ($this->advanceBranchIdListFromRow($advance) !== []) {
                        $this->applyAdvancePeerBranchScope($prevBalQ, $advance);
                    }
                    $prevBalance = (float) $prevBalQ->when($advance->company_id, fn($q) => $q->where('company_id', $advance->company_id))
                        ->whereRaw("DATE_FORMAT(advance_date, '%Y-%m') = ?", [$prevMonth])
                        ->sum('balance_amount');

                    $monthBalance = [
                        'total_advance' => (float) $peers->sum('advance_amount'),
                        'total_used'    => (float) $peers->sum('used_amount'),
                        'balance'       => (float) $peers->sum('balance_amount'),
                        'prev_balance'  => (float) $prevBalance,
                        'prev_month'    => $prevMonth,
                    ];
                }
            }
        }

        return view('superadmin.pettycash.advances_create', compact(
            'admin',
            'zones',
            'companies',
            'vendors',
            'advance',
            'prevAdvance',
            'nextAdvance',
            'monthBalance',
            'prefillExpenseReportId'
        ));
    }

    public function saveAdvance(Request $request)
    {
        $advanceDate = null;
        if ($request->advance_date) {
            try {
                $advanceDate = Carbon::createFromFormat('d/m/Y', $request->advance_date)->format('Y-m-d');
            } catch (\Exception $e) {
                $advanceDate = $request->advance_date;
            }
        }

        $request->validate([
            'advance_amount' => 'required|numeric|min:0.01',
            'advance_date'   => 'required',
        ]);

        [$primaryBranchId, $branchIdsCsv] = $this->normalizePettyCashBranchPayload($request->input('branch_id'));
        if ($request->input('save_action') !== 'draft' && $primaryBranchId === null) {
            throw ValidationException::withMessages([
                'branch_id' => ['Please select at least one branch.'],
            ]);
        }

        $id            = $request->id;
        $advanceAmount = (float) $request->advance_amount;

        $data = [
            'currency'       => $request->currency ?? 'INR',
            'advance_amount' => $advanceAmount,
            'advance_date'   => $advanceDate,
            'reference_no'   => $request->reference_no,
            'paid_through'   => $request->paid_through,
            'vendor_id'      => $request->vendor_id ?: null,
            'zone_id'        => $request->zone_id ?: null,
            'branch_id'      => $primaryBranchId,
            'branch_ids'     => $branchIdsCsv,
            'company_id'     => $request->company_id ?: null,
            'notes'          => $request->notes,
            'status'         => $request->save_action === 'draft' ? 'draft' : 'pending',
            'updated_by'     => auth()->id(),
            'updated_at'     => now(),
        ];

        if ($id) {
            // On edit: re-derive balance_amount = advance_amount - used_amount (preserve used)
            $existing    = DB::table('advances')->where('id', $id)->first();
            $usedAmount  = (float) ($existing->used_amount ?? 0);
            $data['balance_amount'] = max(0, $advanceAmount - $usedAmount);

            DB::table('advances')->where('id', $id)->update($data);
            $advanceId = $id;
            $message   = 'Advance Updated';
            $amtStr    = number_format($advanceAmount, 2, '.', '');
            PettyCashHistory::recordForAdvance((int) $advanceId, 'advance_updated', 'Advance of ₹' . $amtStr . ' was updated.');
        } else {
            // New advance: full amount is the opening balance
            $data['used_amount']    = 0;
            $data['balance_amount'] = $advanceAmount;
            $data['created_by']     = auth()->id();
            $data['created_at']     = now();
            if ($request->filled('expense_report_id')) {
                $data['report_id'] = (int) $request->expense_report_id;
            }
            $advanceId = DB::table('advances')->insertGetId($data);
            $message   = 'Advance Saved Successfully!';
            $amtStr    = number_format($advanceAmount, 2, '.', '');
            $isDraft   = ($data['status'] ?? '') === 'draft';

            if ($isDraft) {
                PettyCashHistory::recordForAdvance((int) $advanceId, 'advance_draft_saved', 'Advance of ₹' . $amtStr . ' was saved as draft.');
            } else {
                PettyCashHistory::recordForAdvance((int) $advanceId, 'advance_recorded', 'An advance of ₹' . $amtStr . ' was recorded.');
            }

            if (!empty($data['report_id'])) {
                $er = DB::table('expense_reports')->where('id', (int) $data['report_id'])->first();
                $title = $er ? (string) ($er->report_name ?: $er->report_id ?: '') : '';
                PettyCashHistory::recordForReport(
                    (int) $data['report_id'],
                    'advance_applied',
                    'Advance payment of ₹' . $amtStr . ' has been applied.'
                );
                PettyCashHistory::recordForAdvance(
                    (int) $advanceId,
                    'advance_linked_report',
                    'An advance of ₹' . $amtStr . ' has been applied to the report' . ($title !== '' ? " titled '" . $title . "'" : '') . '.'
                );
            }
        }

        $redirectList = route('superadmin.getadvances');
        if ($request->input('save_action') !== 'new') {
            $redirectList .= '?open_advance=' . (int) $advanceId;
        }

        return response()->json([
            'success'    => true,
            'message'    => $message,
            'advance_id' => $advanceId,
            'redirect'   => $request->input('save_action') === 'new'
                ? route('superadmin.getadvancescreate')
                : $redirectList,
        ]);
    }

    public function getAdvancesAjax(Request $request)
    {
        $perPage = (int) ($request->per_page ?? 15);
        $tab     = $request->tab ?? 'all'; // 'pending' or 'all'

        $query = DB::table('advances')
            ->leftJoin('vendor_tbl',      'vendor_tbl.id',      '=', 'advances.vendor_id')
            ->leftJoin('tblzones',        'tblzones.id',        '=', 'advances.zone_id')
            ->leftJoin('company_tbl',     'company_tbl.id',     '=', 'advances.company_id')
            ->leftJoin('tbl_locations',   'tbl_locations.id',   '=', 'advances.branch_id')
            ->leftJoin('expense_reports', 'expense_reports.id', '=', 'advances.report_id')
            ->select(
                'advances.*',
                'vendor_tbl.display_name as vendor_name',
                'tblzones.name as zone_name',
                'company_tbl.company_name',
                'tbl_locations.name as branch_name',
                'expense_reports.report_id as report_code',
                'expense_reports.report_name'
            );

        // Shared filters
        if ($request->filled('date_from') && $request->filled('date_to')) {
            try {
                $query->whereBetween('advances.advance_date', [
                    Carbon::createFromFormat('d/m/Y', $request->date_from)->startOfDay(),
                    Carbon::createFromFormat('d/m/Y', $request->date_to)->endOfDay(),
                ]);
            } catch (\Exception $e) {
            }
        }
        if ($request->filled('zone_id')) {
            $ids = array_values(array_filter(array_map('intval', explode(',', $request->zone_id))));
            if ($ids) $query->whereIn('advances.zone_id', $ids);
        }
        if ($request->filled('branch_id')) {
            $this->applyAdvanceBranchFilter($query, $request->branch_id);
        }
        if ($request->filled('company_id')) {
            $ids = array_values(array_filter(array_map('intval', explode(',', $request->company_id))));
            if ($ids) $query->whereIn('advances.company_id', $ids);
        }
        if ($request->filled('universal_search')) {
            $s = $request->universal_search;
            $query->where(function ($q) use ($s) {
                $q->where('advances.reference_no',      'LIKE', "%{$s}%")
                    ->orWhere('vendor_tbl.display_name',  'LIKE', "%{$s}%")
                    ->orWhere('advances.paid_through',    'LIKE', "%{$s}%")
                    ->orWhere('expense_reports.report_name', 'LIKE', "%{$s}%");
            });
        }






        // Pending tab: advances awaiting use on a report (not yet linked to an expense report)
        if ($tab === 'pending') {
            $query->where(DB::raw('LOWER(advances.status)'), 'pending')
                ->where(function ($q) {
                    $q->whereNull('advances.report_id')->orWhere('advances.report_id', 0);
                });
        } elseif ($tab === 'draft') {
            $query->where(DB::raw('LOWER(advances.status)'), 'draft');
        } elseif ($request->filled('status_name')) {
            $statuses = array_values(array_filter(array_map('trim', explode(',', strtolower($request->status_name)))));
            if ($statuses) $query->whereIn(DB::raw('LOWER(advances.status)'), $statuses);
        }

        // Stats always computed over unfiltered-by-tab base (only shared filters apply)
        $statsQuery = DB::table('advances')
            ->leftJoin('vendor_tbl',      'vendor_tbl.id',      '=', 'advances.vendor_id')
            ->leftJoin('tblzones',        'tblzones.id',        '=', 'advances.zone_id')
            ->leftJoin('company_tbl',     'company_tbl.id',     '=', 'advances.company_id')
            ->leftJoin('tbl_locations',   'tbl_locations.id',   '=', 'advances.branch_id')
            ->leftJoin('expense_reports', 'expense_reports.id', '=', 'advances.report_id')
            ->select('advances.status', 'advances.advance_amount', 'advances.report_id');

        if ($request->filled('date_from') && $request->filled('date_to')) {
            try {
                $statsQuery->whereBetween('advances.advance_date', [
                    Carbon::createFromFormat('d/m/Y', $request->date_from)->startOfDay(),
                    Carbon::createFromFormat('d/m/Y', $request->date_to)->endOfDay(),
                ]);
            } catch (\Exception $e) {
            }
        }
        if ($request->filled('zone_id')) {
            $ids = array_values(array_filter(array_map('intval', explode(',', $request->zone_id))));
            if ($ids) $statsQuery->whereIn('advances.zone_id', $ids);
        }
        if ($request->filled('branch_id')) {
            $this->applyAdvanceBranchFilter($statsQuery, $request->branch_id);
        }
        if ($request->filled('company_id')) {
            $ids = array_values(array_filter(array_map('intval', explode(',', $request->company_id))));
            if ($ids) $statsQuery->whereIn('advances.company_id', $ids);
        }

        $allRows = $statsQuery->get();
        $bySt    = static function ($rows, string $status) {
            $f = $rows->filter(static fn($r) => strtolower((string) ($r->status ?? '')) === $status);

            return [(int) $f->count(), (float) $f->sum('advance_amount')];
        };
        [$dC, $dA] = $bySt($allRows, 'draft');
        $pendingUnreported = $allRows->filter(static function ($r) {
            if (strtolower((string) ($r->status ?? '')) !== 'pending') {
                return false;
            }
            $rid = (int) ($r->report_id ?? 0);

            return $rid === 0;
        });
        $pC = (int) $pendingUnreported->count();
        $pA = (float) $pendingUnreported->sum('advance_amount');
        [$apC, $apA] = $bySt($allRows, 'applied');
        [$clC, $clA] = $bySt($allRows, 'closed');
        [$rjC, $rjA] = $bySt($allRows, 'rejected');
        $stats = [
            'total'           => $allRows->count(),
            'total_amount'    => (float) $allRows->sum('advance_amount'),
            'draft'           => $dC,
            'draft_amount'    => $dA,
            'pending'         => $pC,
            'pending_amount'  => $pA,
            'applied'         => $apC,
            'applied_amount'  => $apA,
            'closed'          => $clC,
            'closed_amount'   => $clA,
            'rejected'        => $rjC,
            'rejected_amount' => $rjA,
        ];

        $list = $query->orderBy('advances.id', 'desc')->paginate($perPage);
        $this->hydrateAdvancesBranchNames($list);
        $pagination = $list->appends($request->all())->links('pagination::bootstrap-4')->toHtml();

        $html = view('superadmin.pettycash.advances_rows', [
            'list' => $list,
            'tab'  => $tab,
        ])->render();

        return response()->json([
            'html'       => $html,
            'pagination' => $pagination,
            'stats'      => $stats,
        ]);
    }

    public function advanceApprover(Request $request)
    {
        $id    = $request->advance_id;
        $value = $request->value;

        if (!in_array($value, ['pending', 'applied', 'closed', 'rejected'])) {
            return response()->json(['success' => false, 'message' => 'Invalid status.'], 422);
        }

        $advance = DB::table('advances')->where('id', $id)->first();
        if (!$advance) {
            return response()->json(['success' => false, 'message' => 'Advance not found.'], 404);
        }

        DB::table('advances')
            ->where('id', $id)
            ->update(['status' => $value, 'updated_by' => auth()->id(), 'updated_at' => now()]);

        $amt = number_format((float) ($advance->advance_amount ?? 0), 2, '.', '');
        $msg = match ($value) {
            'pending'  => 'Advance of ₹' . $amt . ' was submitted for approval.',
            'applied'  => 'Advance of ₹' . $amt . ' was approved and marked as paid.',
            'closed'   => 'Advance of ₹' . $amt . ' was settled and closed.',
            'rejected' => 'Advance of ₹' . $amt . ' was rejected.',
            default    => 'Advance status updated.',
        };
        PettyCashHistory::recordForAdvance((int) $id, 'advance_status_' . $value, $msg);

        return response()->json(['success' => true, 'message' => 'Advance status updated to ' . ucfirst($value) . '.']);
    }

    /**
     * Returns the cumulative advance balance for a given branch/company in a specific month.
     * Used by the advances_create page to show opening balance when recording a new advance.
     */
    public function getAdvanceMonthBalance(Request $request)
    {
        $branchIdCsv = (string) ($request->branch_id ?? '');
        $companyId   = $request->company_id ?: null;
        $month       = $request->month;      // format: Y-m  e.g. 2026-03
        $excludeId   = $request->exclude_id ?: null;

        $branchFilterIds = array_values(array_filter(array_map('intval', explode(',', $branchIdCsv))));

        if ($branchFilterIds === [] && !$companyId) {
            return response()->json(['total_advance' => 0, 'total_used' => 0, 'balance' => 0, 'entries' => []]);
        }

        $query = DB::table('advances')
            ->whereNotIn('status', ['rejected', 'draft']);

        if ($branchFilterIds !== []) {
            $query->where(function ($q) use ($branchFilterIds) {
                foreach ($branchFilterIds as $bid) {
                    $q->orWhere(function ($qq) use ($bid) {
                        $qq->where('branch_id', $bid)
                            ->orWhereRaw('FIND_IN_SET(?, branch_ids)', [$bid]);
                    });
                }
            });
        }
        if ($companyId) {
            $query->where('company_id', $companyId);
        }
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        if ($month) {
            $query->whereRaw("DATE_FORMAT(advance_date, '%Y-%m') = ?", [$month]);
        }

        $rows = $query->select('id', 'advance_amount', 'used_amount', 'balance_amount', 'advance_date', 'status', 'reference_no')->get();

        $totalAdvance = $rows->sum('advance_amount');
        $totalUsed    = $rows->sum('used_amount');
        $balance      = $rows->sum('balance_amount');

        // Previous month's balance (last month relative to $month)
        $prevMonth       = null;
        $prevBalance     = 0;
        if ($month) {
            try {
                $prevMonthDate = Carbon::createFromFormat('Y-m', $month)->subMonth();
                $prevMonth     = $prevMonthDate->format('Y-m');
                $prevQ         = DB::table('advances')
                    ->whereNotIn('status', ['rejected', 'draft'])
                    ->when($companyId, fn($q) => $q->where('company_id', $companyId))
                    ->whereRaw("DATE_FORMAT(advance_date, '%Y-%m') = ?", [$prevMonth]);
                if ($branchFilterIds !== []) {
                    $prevQ->where(function ($q) use ($branchFilterIds) {
                        foreach ($branchFilterIds as $bid) {
                            $q->orWhere(function ($qq) use ($bid) {
                                $qq->where('branch_id', $bid)
                                    ->orWhereRaw('FIND_IN_SET(?, branch_ids)', [$bid]);
                            });
                        }
                    });
                }
                $prevBalance = (float) $prevQ->sum('balance_amount');
            } catch (\Exception $e) {
            }
        }

        return response()->json([
            'total_advance' => (float) $totalAdvance,
            'total_used'    => (float) $totalUsed,
            'balance'       => (float) $balance,
            'prev_balance'  => (float) $prevBalance,
            'prev_month'    => $prevMonth,
            'entries'       => $rows->map(fn($r) => [
                'id'             => $r->id,
                'advance_amount' => (float) $r->advance_amount,
                'used_amount'    => (float) $r->used_amount,
                'balance_amount' => (float) $r->balance_amount,
                'status'         => $r->status,
                'advance_date'   => $r->advance_date,
                'reference_no'   => $r->reference_no,
            ]),
        ]);
    }

    /**
     * Apply an advance against a set of petty cash expenses.
     * Calculates used_amount and balance_amount and stores them on the advance.
     */
    public function applyAdvanceToExpenses(Request $request)
    {
        $request->validate([
            'advance_id'      => 'required|exists:advances,id',
            'total_expenses'  => 'required|numeric|min:0',
        ]);

        $advanceId     = $request->advance_id;
        $totalExpenses = (float) $request->total_expenses;

        $advance = DB::table('advances')->where('id', $advanceId)->first();
        if (!$advance) {
            return response()->json(['success' => false, 'message' => 'Advance not found.'], 404);
        }

        $advanceAmount  = (float) $advance->advance_amount;
        $usedAmount     = min($advanceAmount, $totalExpenses);
        $balanceAmount  = max(0, $advanceAmount - $totalExpenses);
        $reimbursement  = $totalExpenses - $advanceAmount; // positive = owed to employee, negative = owed back

        DB::table('advances')->where('id', $advanceId)->update([
            'used_amount'    => $usedAmount,
            'balance_amount' => $balanceAmount,
            'report_id'      => $request->report_id ?: $advance->report_id,
            'status'         => 'applied',
            'updated_by'     => auth()->id(),
            'updated_at'     => now(),
        ]);

        return response()->json([
            'success'          => true,
            'message'          => 'Advance applied successfully.',
            'advance_amount'   => $advanceAmount,
            'total_expenses'   => $totalExpenses,
            'used_amount'      => $usedAmount,
            'balance_amount'   => $balanceAmount,
            'reimbursement'    => $reimbursement,
        ]);
    }

    /**
     * Return all expense reports for the "Apply to Report" dropdown on the advance detail panel.
     */
    public function getReportsForAdvance(Request $request)
    {
        $reports = DB::table('expense_reports as er')
            ->leftJoin(DB::raw('(SELECT report_id AS pc_rid, COALESCE(SUM(total_amount), 0) AS expenses_total FROM petty_cash GROUP BY report_id) AS pcsum'), 'pcsum.pc_rid', '=', 'er.id')
            ->orderBy('er.id', 'desc')
            ->select([
                'er.id',
                'er.report_id',
                'er.report_name',
                'er.start_date',
                'er.end_date',
                DB::raw('COALESCE(pcsum.expenses_total, 0) AS expenses_total'),
            ])
            ->get();

        return response()->json($reports);
    }

    /**
     * Link (or unlink) an advance to a specific expense report.
     * Used by the "Apply to Report" dropdown on the advance detail panel.
     */
    public function linkAdvanceReport(Request $request)
    {
        $advanceId = $request->advance_id;
        $reportId  = $request->report_id ?: null;

        $advance = DB::table('advances')->where('id', $advanceId)->first();
        if (!$advance) {
            return response()->json(['success' => false, 'message' => 'Advance not found.'], 404);
        }

        $prevReportId = $advance->report_id ? (int) $advance->report_id : null;

        DB::table('advances')->where('id', $advanceId)->update([
            'report_id'  => $reportId,
            'updated_by' => auth()->id(),
            'updated_at' => now(),
        ]);

        $amtStr = number_format((float) ($advance->advance_amount ?? 0), 2, '.', '');
        if ($reportId && (int) $reportId !== $prevReportId) {
            $report     = DB::table('expense_reports')->where('id', $reportId)->first();
            $title      = $report ? (string) ($report->report_name ?: $report->report_id ?: '') : '';
            PettyCashHistory::recordForReport((int) $reportId, 'advance_linked', 'Advance linked to this report.');
            PettyCashHistory::recordForAdvance(
                (int) $advanceId,
                'advance_linked_report',
                'An advance of ₹' . $amtStr . ' has been applied to the report' . ($title !== '' ? " titled '" . $title . "'" : '') . '.'
            );
        }
        if (!$reportId && $prevReportId) {
            PettyCashHistory::recordForReport($prevReportId, 'advance_unlinked', 'Advance removed from this report.');
            PettyCashHistory::recordForAdvance(
                (int) $advanceId,
                'advance_unlinked_report',
                'This advance of ₹' . $amtStr . ' was removed from the linked report.'
            );
        }

        $reportName = null;
        if ($reportId) {
            $report     = DB::table('expense_reports')->where('id', $reportId)->first();
            $reportName = $report ? ($report->report_name ?: $report->report_id) : null;
        }

        return response()->json([
            'success'     => true,
            'message'     => $reportId ? 'Advance linked to report successfully.' : 'Advance unlinked from report.',
            'report_name' => $reportName,
        ]);
    }

    /**
     * Recall a submitted (pending) advance back to Draft so the employee can edit and re-submit.
     */
    public function recallAdvance(Request $request)
    {
        $id      = $request->advance_id;
        $advance = DB::table('advances')->where('id', $id)->first();

        if (!$advance) {
            return response()->json(['success' => false, 'message' => 'Advance not found.'], 404);
        }

        if (strtolower($advance->status) !== 'pending') {
            return response()->json(['success' => false, 'message' => 'Only advances awaiting approval can be recalled.'], 422);
        }

        DB::table('advances')->where('id', $id)->update([
            'status'     => 'draft',
            'updated_by' => auth()->id(),
            'updated_at' => now(),
        ]);

        $amtStr = number_format((float) ($advance->advance_amount ?? 0), 2, '.', '');
        PettyCashHistory::recordForAdvance((int) $id, 'advance_recalled', 'Advance of ₹' . $amtStr . ' was recalled to draft for editing.');

        return response()->json(['success' => true, 'message' => 'Advance recalled. You can now edit and re-submit for approval.']);
    }

    /**
     * Zoho-style advance detail (list pane + JSON for side panel): amounts, metadata, linked report, history.
     */
    public function getAdvanceDetail(Request $request)
    {
        $id = (int) $request->query('advance_id');
        if (!$id) {
            return response()->json(['success' => false, 'message' => 'Invalid advance.'], 422);
        }

        $row = DB::table('advances as a')
            ->leftJoin('vendor_tbl', 'vendor_tbl.id', '=', 'a.vendor_id')
            ->leftJoin('tblzones', 'tblzones.id', '=', 'a.zone_id')
            ->leftJoin('company_tbl', 'company_tbl.id', '=', 'a.company_id')
            ->leftJoin('tbl_locations', 'tbl_locations.id', '=', 'a.branch_id')
            ->leftJoin('expense_reports as er', 'er.id', '=', 'a.report_id')
            ->leftJoin('users as u', 'u.id', '=', 'a.created_by')
            ->where('a.id', $id)
            ->select([
                'a.*',
                'vendor_tbl.display_name as vendor_name',
                'tblzones.name as zone_name',
                'company_tbl.company_name',
                'tbl_locations.name as branch_name',
                'er.id as linked_report_pk',
                'er.report_id as linked_report_code',
                'er.report_name as linked_report_name',
                DB::raw('COALESCE(u.user_fullname, u.username) as recorded_by_name'),
            ])
            ->first();

        if (!$row) {
            return response()->json(['success' => false, 'message' => 'Advance not found.'], 404);
        }

        $rbn = (string) ($row->recorded_by_name ?? '');
        $currency = ($row->currency && $row->currency !== 'INR') ? $row->currency . '.' : 'Rs.';
        $dateFmt  = $row->advance_date ? Carbon::parse($row->advance_date)->format('d/m/Y') : '—';

        $linkedReport = null;
        if (!empty($row->linked_report_pk)) {
            $linkedReport = [
                'id'          => (int) $row->linked_report_pk,
                'report_id'   => $row->linked_report_code,
                'report_name' => $row->linked_report_name,
            ];
        }

        $historyRows = PettyCashHistory::query()
            ->where('historyable_type', Advance::class)
            ->where('historyable_id', $id)
            ->orderBy('created_at', 'desc')
            ->get();

        $history = [];
        foreach ($historyRows as $h) {
            $an = '';

            if (!empty($h->created_by)) {
                $an = DB::table('users')
                    ->where('id', $h->created_by)
                    ->value('user_fullname')
                    ?? DB::table('users')
                    ->where('id', $h->created_by)
                    ->value('username')
                    ?? '';
            }
            $history[] = [
                'at'             => $h->created_at,
                'message'        => (string) $h->message,
                'action'         => (string) $h->action,
                'actor_name'     => $an,
                'actor_initials' => $this->actorInitials($an !== '' ? $an : null),
            ];
        }

        $st = strtolower((string) ($row->status ?? ''));
        $showApplyBanner = empty($row->report_id) && in_array($st, ['pending', 'draft'], true);

        $branchIdsForLabel = $this->advanceBranchIdListFromRow($row);
        $branchNameDisplay = $row->branch_name ?? '';
        if ($branchIdsForLabel !== []) {
            $nameMap = DB::table('tbl_locations')->whereIn('id', $branchIdsForLabel)->pluck('name', 'id');
            $parts   = [];
            foreach ($branchIdsForLabel as $bid) {
                if (isset($nameMap[$bid])) {
                    $parts[] = $nameMap[$bid];
                }
            }
            if ($parts !== []) {
                $branchNameDisplay = implode(', ', $parts);
            }
        }

        return response()->json([
            'success'              => true,
            'advance'              => [
                'id'               => (int) $row->id,
                'advance_date'     => $row->advance_date,
                'advance_date_fmt' => $dateFmt,
                'advance_amount'   => (float) ($row->advance_amount ?? 0),
                'balance_amount'   => (float) ($row->balance_amount ?? 0),
                'used_amount'      => (float) ($row->used_amount ?? 0),
                'currency'         => (string) ($row->currency ?? 'INR'),
                'currency_prefix'  => $currency,
                'reference_no'     => $row->reference_no,
                'notes'            => $row->notes,
                'paid_through'     => $row->paid_through,
                'status'           => $row->status,
                'vendor_name'      => $row->vendor_name,
                'zone_name'        => $row->zone_name,
                'company_name'     => $row->company_name,
                'branch_name'      => $branchNameDisplay,
            ],
            'linked_report'        => $linkedReport,
            'recorded_by_name'     => $rbn,
            'recorded_by_initials' => $this->actorInitials($rbn !== '' ? $rbn : null),
            'edit_url'             => route('superadmin.getadvancescreate') . '?id=' . $id,
            'show_apply_banner'    => $showApplyBanner,
            'history'              => $history,
        ]);
    }

    /**
     * Petty cash expense detail for dashboard slide-over (items, receipt, report link, history).
     */
    public function getPettyCashDetail(Request $request)
    {
        $id = (int) $request->query('petty_cash_id');
        if (!$id) {
            $id = (int) $request->query('id');
        }
        if (!$id) {
            return response()->json(['success' => false, 'message' => 'Invalid expense.'], 422);
        }

        $row = DB::table('petty_cash as pc')
            ->leftJoin('vendor_tbl', 'vendor_tbl.id', '=', 'pc.vendor_id')
            ->leftJoin('tblzones', 'tblzones.id', '=', 'pc.zone_id')
            ->leftJoin('company_tbl', 'company_tbl.id', '=', 'pc.company_id')
            ->leftJoin('tbl_locations', 'tbl_locations.id', '=', 'pc.branch_id')
            ->leftJoin('expense_categories', 'expense_categories.id', '=', 'pc.expense_category_id')
            ->leftJoin('expense_reports as er', 'er.id', '=', 'pc.report_id')
            ->leftJoin('users as uc', 'uc.id', '=', 'pc.created_by')
            ->leftJoin('users as uu', 'uu.id', '=', 'pc.updated_by')
            ->where('pc.id', $id)
            ->select([
                'pc.*',
                'vendor_tbl.display_name as vendor_name',
                'tblzones.name as zone_name',
                'company_tbl.company_name',
                'tbl_locations.name as branch_name',
                'expense_categories.name as category_name',
                'er.id as linked_report_pk',
                'er.report_id as linked_report_code',
                'er.report_name as linked_report_name',
                DB::raw('COALESCE(uc.user_fullname, uc.username) as created_by_name'),
                DB::raw('COALESCE(uu.user_fullname, uu.username) as updated_by_name'),
            ])
            ->first();

        if (!$row) {
            return response()->json(['success' => false, 'message' => 'Expense not found.'], 404);
        }

        $currency = ((string) ($row->currency ?? 'INR') !== 'INR')
            ? (string) $row->currency . '.'
            : '₹';

        $dateFmt = $row->expense_date
            ? Carbon::parse($row->expense_date)->format('d/m/Y')
            : '—';

        $branchIdsForLabel = $this->advanceBranchIdListFromRow($row);
        $branchNameDisplay = (string) ($row->branch_name ?? '');
        if ($branchIdsForLabel !== []) {
            $nameMap = DB::table('tbl_locations')->whereIn('id', $branchIdsForLabel)->pluck('name', 'id');
            $parts   = [];
            foreach ($branchIdsForLabel as $bid) {
                if (isset($nameMap[$bid])) {
                    $parts[] = $nameMap[$bid];
                }
            }
            if ($parts !== []) {
                $branchNameDisplay = implode(', ', $parts);
            }
        }

        $linkedReport = null;
        if (!empty($row->linked_report_pk)) {
            $erPk = (int) $row->linked_report_pk;
            $linkedReport = [
                'id'          => $erPk,
                'report_id'   => $row->linked_report_code,
                'report_name' => $row->linked_report_name,
                'url'         => route('superadmin.getpettycashreports') . '?open_er_id=' . $erPk,
            ];
        }

        $items = DB::table('petty_cash_items as pci')
            ->leftJoin('expense_categories', 'expense_categories.id', '=', 'pci.expense_category_id')
            ->where('pci.petty_cash_id', $id)
            ->orderBy('pci.id')
            ->select([
                'pci.description',
                'pci.amount',
                'expense_categories.name as category_name',
            ])
            ->get();

        $itemsArr = [];
        foreach ($items as $it) {
            $itemsArr[] = [
                'category_name' => (string) ($it->category_name ?? ''),
                'description'   => (string) ($it->description ?? ''),
                'amount'        => (float) ($it->amount ?? 0),
            ];
        }

        $storedPaths = $this->decodePettyCashAttachmentPathsJson($row->attachment_paths ?? null);
        $rp = trim((string) ($row->receipt_path ?? ''));
        if ($rp === '' && $storedPaths !== []) {
            $rp = $storedPaths[0];
        }
        $receiptUrl = $rp !== '' ? asset($rp) : null;
        $attachmentUrls = [];
        foreach ($storedPaths as $p) {
            if (trim($p) !== '') {
                $attachmentUrls[] = asset($p);
            }
        }

        $st = strtolower((string) ($row->status ?? ''));
        $readonly = in_array($st, ['approved', 'reimbursed'], true);

        $rbn = (string) ($row->created_by_name ?? '');
        $ubn = (string) ($row->updated_by_name ?? '');

        $historyEvents = [];

        $reportId = (int) ($row->report_id ?? 0);
        if ($reportId > 0) {
            $historyRows = DB::table('petty_cash_histories as h')
                ->leftJoin('users as u', 'u.id', '=', 'h.created_by')
                ->where('h.historyable_type', ExpenseReport::class)
                ->where('h.historyable_id', $reportId)
                ->orderBy('h.created_at', 'desc')
                ->select([
                    'h.created_at',
                    'h.message',
                    'h.action',
                    DB::raw('COALESCE(u.user_fullname, u.username) as actor_name'),
                ])
                ->get();

            foreach ($historyRows as $h) {
                $an = trim((string) ($h->actor_name ?? ''));
                $historyEvents[] = [
                    'at'             => $h->created_at,
                    'message'        => (string) $h->message,
                    'action'         => (string) $h->action,
                    'actor_name'     => $an !== '' ? $an : 'System',
                    'actor_initials' => $this->actorInitials($an !== '' ? $an : null),
                ];
            }
        }

        $totalStr = number_format((float) ($row->total_amount ?? 0), 2, '.', '');
        $historyEvents[] = [
            'at'             => $row->created_at,
            'message'        => 'Expense created for ₹' . $totalStr . '.',
            'action'         => 'expense_created',
            'actor_name'     => $rbn !== '' ? $rbn : 'System',
            'actor_initials' => $this->actorInitials($rbn !== '' ? $rbn : null),
        ];

        $createdTs = $row->created_at ? strtotime((string) $row->created_at) : 0;
        $updatedTs = $row->updated_at ? strtotime((string) $row->updated_at) : 0;
        if ($updatedTs > $createdTs + 2) {
            $historyEvents[] = [
                'at'             => $row->updated_at,
                'message'        => 'Expense updated.',
                'action'         => 'expense_updated',
                'actor_name'     => $ubn !== '' ? $ubn : ($rbn !== '' ? $rbn : 'System'),
                'actor_initials' => $this->actorInitials($ubn !== '' ? $ubn : ($rbn !== '' ? $rbn : null)),
            ];
        }

        if ($st === 'approved') {
            $historyEvents[] = [
                'at'             => $row->updated_at ?? $row->created_at,
                'message'        => 'Expense approved.',
                'action'         => 'expense_approved',
                'actor_name'     => 'System',
                'actor_initials' => $this->actorInitials('System'),
            ];
        } elseif ($st === 'rejected') {
            $reason = trim((string) ($row->reject_reason ?? ''));
            $historyEvents[] = [
                'at'             => $row->updated_at ?? $row->created_at,
                'message'        => $reason !== '' ? ('Expense rejected: ' . $reason) : 'Expense rejected.',
                'action'         => 'expense_rejected',
                'actor_name'     => 'System',
                'actor_initials' => $this->actorInitials('System'),
            ];
        } elseif ($st === 'reimbursed') {
            $historyEvents[] = [
                'at'             => $row->updated_at ?? $row->created_at,
                'message'        => 'Expense marked as reimbursed.',
                'action'         => 'expense_reimbursed',
                'actor_name'     => 'System',
                'actor_initials' => $this->actorInitials('System'),
            ];
        }

        usort($historyEvents, static function (array $a, array $b): int {
            $ta = isset($a['at']) ? strtotime((string) $a['at']) : 0;
            $tb = isset($b['at']) ? strtotime((string) $b['at']) : 0;

            return $tb <=> $ta;
        });

        $title = strtolower((string) ($row->expense_type ?? 'single')) === 'itemized'
            ? 'Itemized'
            : (string) ($row->category_name ?? 'Expense');

        $firstDesc = '';
        foreach ($itemsArr as $ia) {
            if (trim($ia['description']) !== '') {
                $firstDesc = $ia['description'];
                break;
            }
        }
        $descriptionLine = $firstDesc !== '' ? $firstDesc : (string) ($row->notes ?? '—');

        return response()->json([
            'success'              => true,
            'expense'              => [
                'id'                   => (int) $row->id,
                'title'                => $title,
                'expense_date_fmt'     => $dateFmt,
                'total_amount'         => (float) ($row->total_amount ?? 0),
                'currency'             => (string) ($row->currency ?? 'INR'),
                'currency_prefix'      => $currency,
                'status'               => (string) ($row->status ?? ''),
                'claim_reimbursement'  => (int) ($row->claim_reimbursement ?? 0) === 1,
                'reference_no'         => (string) ($row->reference_no ?? ''),
                'notes'                => (string) ($row->notes ?? ''),
                'category_name'      => (string) ($row->category_name ?? ''),
                'vendor_name'          => (string) ($row->vendor_name ?? ''),
                'zone_name'            => (string) ($row->zone_name ?? ''),
                'company_name'         => (string) ($row->company_name ?? ''),
                'branch_name'          => $branchNameDisplay,
                'expense_type'         => (string) ($row->expense_type ?? 'single'),
                'receipt_url'          => $receiptUrl,
                'attachment_urls'      => $attachmentUrls,
                'description_display'  => $descriptionLine,
                'readonly'             => $readonly,
            ],
            'items'                => $itemsArr,
            'linked_report'        => $linkedReport,
            'recorded_by_name'     => $rbn,
            'recorded_by_initials' => $this->actorInitials($rbn !== '' ? $rbn : null),
            'edit_url'             => route('superadmin.getpettycashcreate') . '?id=' . $id,
            'history'              => $historyEvents,
            'comments'             => [],
        ]);
    }

    // =========================================================
    // EXPENSE REPORT DETAIL (for side panel in Reports page)
    // =========================================================

    public function submitExpenseReportForApproval(Request $request)
    {
        $erId = (int) $request->er_id;
        if (!$erId) {
            return response()->json(['success' => false, 'message' => 'Invalid report ID.'], 422);
        }

        $report = DB::table('expense_reports')->where('id', $erId)->first();
        if (!$report) {
            return response()->json(['success' => false, 'message' => 'Report not found.'], 404);
        }

        if (DB::table('petty_cash')->where('report_id', $erId)->count() === 0) {
            return response()->json(['success' => false, 'message' => 'Report has no expenses.'], 422);
        }

        $stored = $report->status ?? null;
        if (in_array($stored, ['pending_approval', 'approved', 'reimbursed'], true)) {
            return response()->json(['success' => false, 'message' => 'Report cannot be submitted in its current state.'], 422);
        }

        DB::table('expense_reports')->where('id', $erId)->update([
            'status' => 'pending_approval',
            'submitted_at'           => now(),
            'approved_at'            => null,
            'approver_name'          => null,
            'reimbursed_at'          => null,
            'updated_at'             => now(),
        ]);

        PettyCashHistory::record($erId, 'submitted', 'Submitted for approval.');

        return response()->json(['success' => true, 'message' => 'Report submitted for approval.']);
    }

    public function approveExpenseReport(Request $request)
    {
        $erId = (int) $request->er_id;
        if (!$erId) {
            return response()->json(['success' => false, 'message' => 'Invalid report ID.'], 422);
        }

        if (!DB::table('expense_reports')->where('id', $erId)->exists()) {
            return response()->json(['success' => false, 'message' => 'Report not found.'], 404);
        }

        if ($this->getExpenseReportEffectiveWorkflow($erId) !== 'pending_approval') {
            return response()->json(['success' => false, 'message' => 'Only reports awaiting approval can be approved.'], 422);
        }

        $user         = auth()->user();
        $approverName = $user && isset($user->name) ? (string) $user->name : (($user && isset($user->username)) ? (string) $user->username : 'Approver');

        DB::table('expense_reports')->where('id', $erId)->update([
            'status' => 'approved',
            'approved_at'            => now(),
            'approver_name'          => $approverName,
            'updated_at'             => now(),
        ]);

        PettyCashHistory::record($erId, 'approved', 'Report approved.');

        return response()->json(['success' => true, 'message' => 'Report approved.']);
    }

    public function rejectExpenseReport(Request $request)
    {
        $erId = (int) $request->er_id;
        if (!$erId) {
            return response()->json(['success' => false, 'message' => 'Invalid report ID.'], 422);
        }

        if (!DB::table('expense_reports')->where('id', $erId)->exists()) {
            return response()->json(['success' => false, 'message' => 'Report not found.'], 404);
        }

        if ($this->getExpenseReportEffectiveWorkflow($erId) !== 'pending_approval') {
            return response()->json(['success' => false, 'message' => 'Only reports awaiting approval can be rejected.'], 422);
        }

        DB::table('expense_reports')->where('id', $erId)->update([
            'status' => 'rejected',
            'updated_at'             => now(),
        ]);

        PettyCashHistory::record($erId, 'rejected', 'Report rejected.');

        return response()->json(['success' => true, 'message' => 'Report rejected.']);
    }

    public function markExpenseReportReimbursed(Request $request)
    {
        $erId = (int) $request->er_id;
        if (!$erId) {
            return response()->json(['success' => false, 'message' => 'Invalid report ID.'], 422);
        }

        if (!DB::table('expense_reports')->where('id', $erId)->exists()) {
            return response()->json(['success' => false, 'message' => 'Report not found.'], 404);
        }

        if ($this->getExpenseReportEffectiveWorkflow($erId) !== 'approved') {
            return response()->json(['success' => false, 'message' => 'Only approved reports can be marked reimbursed.'], 422);
        }

        DB::table('expense_reports')->where('id', $erId)->update([
            'status' => 'reimbursed',
            'reimbursed_at'          => now(),
            'updated_at'             => now(),
        ]);

        PettyCashHistory::record($erId, 'reimbursed', 'Report marked as reimbursed.');

        return response()->json(['success' => true, 'message' => 'Report marked as reimbursed.']);
    }

    public function getAdvancesForExpenseReport(Request $request)
    {
        $erId = (int) $request->er_id;
        if (!$erId) {
            return response()->json(['success' => false, 'message' => 'Invalid report ID.'], 422);
        }

        $advances = DB::table('advances as a')
            ->leftJoin('users as u', 'u.id', '=', 'a.created_by')
            ->where(function ($q) use ($erId) {
                $q->whereNull('a.report_id')->orWhere('a.report_id', $erId);
            })
            ->whereNotIn('a.status', ['rejected', 'draft'])
            ->orderBy('a.advance_date', 'desc')
            ->select([
                'a.id',
                'a.advance_date',
                'a.advance_amount',
                'a.reference_no',
                'a.notes',
                'a.status',
                'a.report_id',
                DB::raw('COALESCE(u.user_fullname, u.username) as recorded_by_name'),
            ])
            ->get();

        return response()->json(['success' => true, 'advances' => $advances]);
    }

    public function applyAdvancesToExpenseReport(Request $request)
    {
        $request->validate([
            'er_id'        => 'required|integer|exists:expense_reports,id',
            'advance_ids'  => 'required|array',
            'advance_ids.*' => 'integer|exists:advances,id',
        ]);

        $erId       = (int) $request->er_id;
        $advanceIds = array_values(array_unique(array_map('intval', $request->advance_ids)));

        foreach ($advanceIds as $aid) {
            $adv = DB::table('advances')->where('id', $aid)->first();
            if (!$adv) {
                continue;
            }
            if ($adv->report_id !== null && (int) $adv->report_id !== $erId) {
                return response()->json([
                    'success' => false,
                    'message' => 'One or more advances are already linked to another report.',
                ], 422);
            }
            $wasLinked = (int) ($adv->report_id ?? 0) === $erId;
            DB::table('advances')->where('id', $aid)->update([
                'report_id'  => $erId,
                'updated_by' => auth()->id(),
                'updated_at' => now(),
            ]);
            if (!$wasLinked) {
                $amtStr = number_format((float) $adv->advance_amount, 2, '.', '');
                $erRow  = DB::table('expense_reports')->where('id', $erId)->first();
                $title  = $erRow ? (string) ($erRow->report_name ?: $erRow->report_id ?: '') : '';
                PettyCashHistory::recordForReport(
                    $erId,
                    'advance_applied',
                    'Advance payment of ₹' . $amtStr . ' has been applied.'
                );
                PettyCashHistory::recordForAdvance(
                    $aid,
                    'advance_linked_report',
                    'An advance of ₹' . $amtStr . ' has been applied to the report' . ($title !== '' ? " titled '" . $title . "'" : '') . '.'
                );
            }
        }

        return response()->json(['success' => true, 'message' => 'Advances applied to this report.']);
    }

    public function getExpenseReportDetail(Request $request)
    {
        $erId = (int) $request->er_id;
        if (!$erId) {
            return response()->json(['success' => false, 'message' => 'Invalid report ID.'], 422);
        }

        $report = DB::table('expense_reports')->where('id', $erId)->first();
        if (!$report) {
            return response()->json(['success' => false, 'message' => 'Report not found.'], 404);
        }

        $submitterName = '';
        if (!empty($report->created_by)) {
            $submitterName = (string) (DB::table('users')->where('id', $report->created_by)->value('user_fullname')
                ?? DB::table('users')->where('id', $report->created_by)->value('name') ?? '');
        }

        $pettyCashEntries = DB::table('petty_cash as pc')
            ->leftJoin('vendor_tbl', 'vendor_tbl.id', '=', 'pc.vendor_id')
            ->leftJoin('tblzones', 'tblzones.id', '=', 'pc.zone_id')
            ->leftJoin('company_tbl', 'company_tbl.id', '=', 'pc.company_id')
            ->leftJoin('tbl_locations', 'tbl_locations.id', '=', 'pc.branch_id')
            ->leftJoin('expense_categories', 'expense_categories.id', '=', 'pc.expense_category_id')
            ->select(
                'pc.id',
                'pc.expense_date',
                'pc.total_amount',
                'pc.status',
                'pc.notes',
                'pc.reference_no',
                'pc.currency',
                'pc.claim_reimbursement',
                'pc.receipt_path',
                'vendor_tbl.display_name as vendor_name',
                'tblzones.name as zone_name',
                'company_tbl.company_name',
                'tbl_locations.name as branch_name',
                'expense_categories.name as category_name'
            )
            ->where('pc.report_id', $erId)
            ->orderBy('pc.expense_date', 'asc')
            ->get();

        $entries = [];
        foreach ($pettyCashEntries as $pc) {
            $items = DB::table('petty_cash_items as pci')
                ->leftJoin('expense_categories', 'expense_categories.id', '=', 'pci.expense_category_id')
                ->select('pci.id', 'pci.description', 'pci.amount', 'expense_categories.name as category_name')
                ->where('pci.petty_cash_id', $pc->id)
                ->get()
                ->toArray();

            $pcArr = (array) $pc;
            $pcArr['items'] = $items;
            $entries[]      = $pcArr;
        }

        $totalAmount = collect($entries)->sum('total_amount');
        $entryCount  = count($entries);

        $reimbursableAmount = collect($entries)
            ->filter(static fn($e) => (int) ($e['claim_reimbursement'] ?? 0) === 1)
            ->sum('total_amount');
        $nonReimbursableAmount = max(0.0, (float) $totalAmount - (float) $reimbursableAmount);

        $advanceApplied = (float) DB::table('advances')
            ->where('report_id', $erId)
            ->whereNotIn('status', ['rejected', 'draft'])
            ->sum('advance_amount');

        $workflowStatus = $this->getExpenseReportEffectiveWorkflow($erId);
        $toReimburse    = $workflowStatus === 'reimbursed'
            ? 0.0
            : max(0.0, (float) $reimbursableAmount - $advanceApplied);

        $keyCounts = [];
        foreach ($entries as $e) {
            $ref = strtolower(trim((string) ($e['reference_no'] ?? '')));
            $key = $ref . '|' . ($e['expense_date'] ?? '') . '|' . (string) ($e['total_amount'] ?? '');
            if ($key === '||' || $key === '') {
                continue;
            }
            $keyCounts[$key] = ($keyCounts[$key] ?? 0) + 1;
        }
        $dupExpenseCount = 0;
        foreach ($keyCounts as $c) {
            if ($c > 1) {
                $dupExpenseCount += $c;
            }
        }
        $warnings = [];
        if ($dupExpenseCount > 0) {
            $warnings[] = [
                'type'    => 'duplicate',
                'message' => 'Duplicate – ' . $dupExpenseCount . ' expenses',
                'detail'  => 'Review line items with the same invoice #, date and amount.',
            ];
        }

        $policyName = '';
        foreach ($entries as $e) {
            if (!empty($e['company_name'])) {
                $policyName = (string) $e['company_name'];
                break;
            }
        }

        $linkedAdvanceRows = DB::table('advances as a')
            ->leftJoin('users as u', 'u.id', '=', 'a.created_by')
            ->where('a.report_id', $erId)
            ->whereNotIn('a.status', ['rejected', 'draft'])
            ->orderBy('a.advance_date', 'desc')
            ->select([
                'a.id',
                'a.advance_date',
                'a.advance_amount',
                'a.reference_no',
                'a.notes',
                'a.status',
                DB::raw('COALESCE(u.user_fullname, u.username) as recorded_by_name'),
            ])
            ->get();

        $linkedAdvances = [];
        foreach ($linkedAdvanceRows as $ar) {
            $rbn = (string) ($ar->recorded_by_name ?? '');
            $linkedAdvances[] = [
                'id'                    => (int) $ar->id,
                'advance_date'          => $ar->advance_date,
                'advance_amount'        => (float) $ar->advance_amount,
                'reference_no'          => $ar->reference_no,
                'notes'                 => $ar->notes,
                'status'                => $ar->status,
                'recorded_by_name'      => $rbn,
                'recorded_by_initials'  => $this->actorInitials($rbn !== '' ? $rbn : null),
            ];
        }

        $historyRows = PettyCashHistory::query()
            ->where('historyable_type', ExpenseReport::class)
            ->where('historyable_id', $erId)
            ->orderBy('created_at', 'desc')
            ->get();

        $history = [];
        foreach ($historyRows as $h) {
            $an = (string) ($h->user_name ?? '');
            $history[] = [
                'at'                   => $h->created_at,
                'message'              => (string) $h->message,
                'action'               => (string) $h->action,
                'actor_name'           => $an,
                'actor_initials'       => $this->actorInitials($an !== '' ? $an : null),
            ];
        }

        return response()->json([
            'success'                 => true,
            'report'                  => $report,
            'entries'                 => $entries,
            'total_amount'            => $totalAmount,
            'entry_count'             => $entryCount,
            'reimbursable_amount'     => (float) $reimbursableAmount,
            'non_reimbursable_amount' => (float) $nonReimbursableAmount,
            'workflow_status'         => $workflowStatus,
            'submitter_name'          => $submitterName,
            'advance_applied_total'   => $advanceApplied,
            'to_reimburse_amount'     => $toReimburse,
            'warnings'                => $warnings,
            'history'                 => $history,
            'linked_advances'         => $linkedAdvances,
            'policy_name'             => $policyName,
            'business_purpose_label'  => $report->business_purpose ? (string) $report->business_purpose : '—',
            'trip_label'              => $report->trip_id ? (string) $report->trip_id : '—',
        ]);
    }
}
