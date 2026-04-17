<?php

namespace App\Http\Controllers;

use App\Exports\AssetExport;
use App\Exports\AssetTemplateExport;
use App\Imports\AssetImport;
use App\Models\Asset;
use App\Models\AssetCategory;
use App\Models\ConsumableStore;
use App\Models\Department;
use App\Models\Tblcompany;
use App\Models\TblLocationModel;
use App\Models\TblZonesModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;

class AssetController extends Controller
{
    private const CONSUMABLE_ACCESS = [1, 2, 3, 6];

    private const CONSUMABLE_OWN_ROWS = [4, 5];

    public function getAssetCategories(Request $request)
    {
        $admin   = auth()->user();
        $perPage = (int) $request->get('per_page', 10);

        $assetCategories = AssetCategory::with(['createdBy', 'updatedBy'])
            ->orderBy('id', 'asc')
            ->paginate($perPage)
            ->appends(['per_page' => $perPage]);

        return view('superadmin.assets.categories', [
            'admin'           => $admin,
            'assetCategories' => $assetCategories,
            'perPage'         => $perPage,
        ]);
    }

    public function storeAssetCategories(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active'   => 'required|in:0,1',
        ]);

        $id = $request->input('id');

        $data = [
            'name'        => $request->input('name'),
            'description' => $request->input('description'),
            'is_active'   => (int) $request->input('is_active'),
            'updated_by'  => auth()->id(),
        ];

        if ($id !== null && $id !== '') {
            $category = AssetCategory::findOrFail($id);
            $category->update($data);
            $message = 'Asset category updated successfully!';
        } else {
            $data['created_by'] = auth()->id();
            $category = AssetCategory::create($data);
            $message = 'Asset category created successfully!';
        }

        return response()->json([
            'success'  => true,
            'message'  => $message,
            'category' => $category,
        ]);
    }

    public function getAssets(Request $request)
    {
        $this->mergeDefaultAssetCategoryFilter($request);

        $admin = auth()->user();

        $categories = AssetCategory::where('is_active', 1)->orderBy('name')->get();
        $departments = Department::where('is_active', 1)->orderBy('name')->get();

        $perPage = (int) $request->get('per_page', 10);
        $perPage = min(100, max(5, $perPage));

        $base = $this->assetGridBaseQuery();
        $this->applyCommonFilters($base, $request);
        $stats = $this->computeStats(clone $base);

        $listQuery = clone $base;
        $this->applyStatFilter($listQuery, $request);

        $assets = $listQuery
            ->orderByDesc('id')
            ->paginate($perPage)
            ->appends($request->except('page'));

        $columnContext = $this->assetDashboardColumnContext($request);

        $systemCategoryIds = $this->systemHardwareCategoryIds();

        return view('superadmin.assets.asset_dashboard', [
            'admin'               => $admin,
            'categories'          => $categories,
            'departments'         => $departments,
            'assets'                => $assets,
            'perPage'               => $perPage,
            'stats'                 => $stats,
            'statuses'              => Asset::STATUSES,
            'columnDefinitions'     => $columnContext['definitions'],
            'assetDashboardTemplate' => $columnContext['template'],
            'systemCategoryIds'     => $systemCategoryIds,
            'systemCategoryIdsCsv'  => $systemCategoryIds === [] ? '' : implode(',', $systemCategoryIds),
        ]);
    }

    public function createAsset(Request $request)
    {
        $admin = auth()->user();
        $categories = AssetCategory::where('is_active', 1)->orderBy('name')->get();
        $departments = Department::where('is_active', 1)->orderBy('name')->get();
        $locData = $this->locationDropdownData();

        $consumable = null;
        if ($request->filled('consumable_store_id')) {
            $cid = (int) $request->query('consumable_store_id');
            if (! $this->consumableStoreLineAccessibleByUser($cid, $admin)) {
                abort(403, 'You do not have access to this Consumable Store line.');
            }
            $consumable = ConsumableStore::with(['Grn', 'Department'])->find($cid);
            if (! $consumable || (float) $consumable->quantity <= 0) {
                abort(404, 'Consumable Store line not found or has no quantity.');
            }
        }

        $newAsset = new Asset(['status' => Asset::STATUS_AVAILABLE]);
        if ($consumable) {
            if ($consumable->department_id) {
                $newAsset->department_id = (int) $consumable->department_id;
            }
            $grn = $consumable->Grn;
            if ($grn) {
                $newAsset->company_id = $grn->company_id;
                $newAsset->zone_id = $grn->zone_id;
                $newAsset->branch_id = $grn->branch_id;
            }
        }

        return view('superadmin.assets.asset_create', [
            'admin'                 => $admin,
            'asset'                 => $newAsset,
            'categories'            => $categories,
            'departments'           => $departments,
            'companies'             => $locData['companies'],
            'zones'                 => $locData['zones'],
            'branches'              => $locData['branches'],
            'statuses'              => Asset::STATUSES,
            'consumable'            => $consumable,
            'employeeDataUrl'       => route('superadmin.employee-data'),
        ]);
    }

    public function editAsset(Asset $asset)
    {
        $admin = auth()->user();
        $categories = AssetCategory::where('is_active', 1)->orderBy('name')->get();
        $departments = Department::where('is_active', 1)->orderBy('name')->get();
        $locData = $this->locationDropdownData();

        $asset->load([
            'category',
            'department',
            'primaryCompany',
            'primaryZone',
            'primaryBranch',
            'assignedUser',
        ]);

        return view('superadmin.assets.asset_create', [
            'admin'                 => $admin,
            'asset'                 => $asset,
            'categories'            => $categories,
            'departments'           => $departments,
            'companies'             => $locData['companies'],
            'zones'                 => $locData['zones'],
            'branches'              => $locData['branches'],
            'statuses'              => Asset::STATUSES,
            'consumable'            => null,
            'employeeDataUrl'       => route('superadmin.employee-data'),
        ]);
    }

    public function getConsumableStoreDashboard(Request $request)
    {
        $admin = auth()->user();
        $perPage = (int) $request->get('per_page', 10);
        $moduleLabel = 'Consumable Store';

        $query = ConsumableStore::with(['Grn', 'Department', 'Zone', 'Branch', 'Company'])->orderBy('id', 'desc');
        $this->applyConsumableStoreListAccess($query, $admin);

        if ($request->filled('universal_search')) {
            $s = $request->universal_search;
            $query->where(function ($q) use ($s) {
                $q->where('grn_number', 'like', '%' . $s . '%')
                    ->orWhere('item_name', 'like', '%' . $s . '%')
                    ->orWhereHas('Department', function ($dq) use ($s) {
                        $dq->where('name', 'like', '%' . $s . '%');
                    });
            });
        }

        $stats = [
            'total'     => (clone $query)->count(),
            'total_qty' => (float) (clone $query)->sum('quantity'),
        ];

        $consumableStoreList = $query->paginate($perPage)->appends($request->all());

        if ($request->ajax()) {
            $html = view('vendor.partials.table.consumable_store_rows', compact('consumableStoreList', 'perPage', 'moduleLabel'))->render();

            return response()->json(['html' => $html, 'stats' => $stats]);
        }

        return view('vendor.consumable_store_dashboard', [
            'admin'               => $admin,
            'consumableStoreList' => $consumableStoreList,
            'perPage'             => $perPage,
            'stats'               => $stats,
            'pageTitle'           => $moduleLabel,
            'moduleLabel'         => $moduleLabel,
        ]);
    }

    public function assetsData(Request $request)
    {
        $this->mergeDefaultAssetCategoryFilter($request);

        $perPage = (int) $request->get('per_page', 10);
        $perPage = min(100, max(5, $perPage));

        $base = $this->assetGridBaseQuery();
        $this->applyCommonFilters($base, $request);
        $stats = $this->computeStats(clone $base);

        $listQuery = clone $base;
        $this->applyStatFilter($listQuery, $request);

        $assets = $listQuery
            ->orderByDesc('id')
            ->paginate($perPage, ['*'], 'page', max(1, (int) $request->get('page', 1)));

        $columnContext = $this->assetDashboardColumnContext($request);

        $html = view('superadmin.assets.asset_dashboard', [
            'assets'                 => $assets,
            'assetDashboardFragment' => 'rows',
            'columnDefinitions'      => $columnContext['definitions'],
            'assetDashboardTemplate' => $columnContext['template'],
        ])->render();
        $paginationHtml = view('superadmin.assets.asset_dashboard', [
            'assets'                 => $assets,
            'assetDashboardFragment' => 'pagination',
            'columnDefinitions'      => $columnContext['definitions'],
        ])->render();
        $theadHtml = view('superadmin.assets.asset_dashboard', [
            'assets'                 => $assets,
            'assetDashboardFragment' => 'thead',
            'columnDefinitions'      => $columnContext['definitions'],
        ])->render();

        return response()->json([
            'success'           => true,
            'html'              => $html,
            'thead_html'        => $theadHtml,
            'pagination_html'   => $paginationHtml,
            'stats'             => $stats,
            'asset_grid_template' => $columnContext['template'],
            'pagination'        => [
                'total' => $assets->total(),
                'from'  => $assets->firstItem(),
                'to'    => $assets->lastItem(),
            ],
        ]);
    }

    public function storeAsset(Request $request)
    {
        $this->mergeNullableAssetFields($request);
        $this->applyAssetAssigneeToken($request);

        if ($request->filled('consumable_store_id')) {
            $request->validate([
                'convert_qty' => 'required|numeric|min:0.01',
            ]);
            if (! $this->consumableStoreLineAccessibleByUser((int) $request->input('consumable_store_id'), auth()->user())) {
                throw ValidationException::withMessages([
                    'consumable_store_id' => 'You do not have access to this Consumable Store line.',
                ]);
            }
            if (! $request->filled('assigned_user_id') && ! $request->filled('assigned_hrm_employment_id')) {
                throw ValidationException::withMessages([
                    'asset_assignee' => 'Please select an assignee from the HRM list.',
                ]);
            }
        }

        $validated = $this->validateAssetPayload($request);
        unset($validated['convert_qty'], $validated['asset_assignee_label']);
        $validated['type_attributes'] = $this->sanitizeTypeAttributes($request->input('type_attributes'));
        $this->assertBranchBelongsToZone($request, 'zone_id', 'branch_id');

        $validated['created_by'] = auth()->id();
        $validated['updated_by'] = auth()->id();

        $validated['consumable_store_id'] = null;

        $assigneeLabel = trim((string) $request->input('asset_assignee_label', ''));
        if (! empty($validated['assigned_hrm_employment_id'])) {
            $validated['responsible_person'] = $assigneeLabel !== '' ? mb_substr($assigneeLabel, 0, 255) : null;
        } elseif (! empty($validated['assigned_user_id'])) {
            $u = DB::table('users')->where('id', $validated['assigned_user_id'])->first();
            $validated['responsible_person'] = $u ? (string) ($u->user_fullname ?? $u->username ?? '') : null;
        } else {
            $validated['responsible_person'] = null;
        }

        DB::transaction(function () use ($request, &$validated) {
            if ($request->filled('consumable_store_id')) {
                $line = ConsumableStore::lockForUpdate()->findOrFail((int) $request->input('consumable_store_id'));
                $qty = (float) $request->input('convert_qty');
                $avail = (float) $line->quantity;
                if ($qty > $avail + 0.00001) {
                    throw ValidationException::withMessages([
                        'convert_qty' => 'Quantity exceeds available amount in Consumable Store.',
                    ]);
                }
                $line->quantity = $avail - $qty;
                if ($line->quantity <= 0.00001) {
                    $line->delete();
                } else {
                    $line->save();
                }
                $validated['consumable_store_id'] = (int) $request->input('consumable_store_id');
                if (empty($validated['status']) || $validated['status'] === Asset::STATUS_AVAILABLE) {
                    $validated['status'] = Asset::STATUS_ASSIGNED;
                }
            }
            Asset::create($validated);
        });

        return redirect()
            ->route('superadmin.assets.dashboard')
            ->with('success', 'Asset created successfully.');
    }

    public function updateAsset(Request $request)
    {
        $id = (int) $request->input('id');
        $asset = Asset::findOrFail($id);

        $this->mergeNullableAssetFields($request);
        $this->applyAssetAssigneeToken($request);
        $validated = $this->validateAssetPayload($request, $asset->id);
        unset($validated['convert_qty'], $validated['consumable_store_id'], $validated['asset_assignee_label']);
        $validated['type_attributes'] = $this->sanitizeTypeAttributes($request->input('type_attributes'));

        $this->assertBranchBelongsToZone($request, 'zone_id', 'branch_id');

        $validated['updated_by'] = auth()->id();

        $assigneeLabel = trim((string) $request->input('asset_assignee_label', ''));
        if (! empty($validated['assigned_hrm_employment_id'])) {
            $validated['responsible_person'] = $assigneeLabel !== '' ? mb_substr($assigneeLabel, 0, 255) : null;
        } elseif (! empty($validated['assigned_user_id'])) {
            $u = DB::table('users')->where('id', $validated['assigned_user_id'])->first();
            $validated['responsible_person'] = $u ? (string) ($u->user_fullname ?? $u->username ?? '') : null;
        } else {
            $validated['responsible_person'] = null;
        }

        $asset->update($validated);

        return redirect()
            ->route('superadmin.assets.dashboard')
            ->with('success', 'Asset updated successfully.');
    }

    public function downloadAssetImportTemplate(Request $request)
    {
        $request->validate([
            'category_id' => 'required|integer|exists:asset_categories,id',
        ]);

        $id = (int) $request->query('category_id');
        if (! AssetCategory::query()->where('id', $id)->where('is_active', 1)->exists()) {
            abort(404, 'Category not found or inactive.');
        }

        $cat = AssetCategory::query()->findOrFail($id);
        $slug = Str::slug((string) $cat->name) ?: 'category';

        return Excel::download(new AssetTemplateExport($id), "assets_import_{$slug}_{$id}.xlsx");
    }

    public function importAssetsExcel(Request $request)
    {
        $validated = $request->validate([
            'file'                 => 'required|file|mimes:xlsx,xls,csv',
            'import_category_id'   => 'nullable|integer|exists:asset_categories,id',
        ]);
        $default = isset($validated['import_category_id']) ? (int) $validated['import_category_id'] : null;

        $import = new AssetImport($default);
        Excel::import($import, $request->file('file'));

        $n  = $import->imported;
        $sk = $import->skipped;

        if ($n === 0 && $sk === 0) {
            return $this->assetImportResponse(
                $request,
                false,
                'No data rows were found in the file (or the sheet is empty).',
                $n,
                $sk
            );
        }

        if ($n === 0 && $sk > 0) {
            return $this->assetImportResponse(
                $request,
                false,
                "No assets were imported ({$sk} row(s) skipped). Check category_id / category, duplicate asset codes, or required fields.",
                $n,
                $sk
            );
        }

        $msg = "Imported {$n} asset(s).";
        if ($sk > 0) {
            $msg .= " {$sk} row(s) skipped.";
        }

        return $this->assetImportResponse($request, true, $msg, $n, $sk);
    }

    protected function assetImportResponse(Request $request, bool $ok, string $message, int $imported, int $skipped)
    {
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success'  => $ok,
                'message'  => $message,
                'imported' => $imported,
                'skipped'  => $skipped,
            ], $ok ? 200 : 422);
        }

        if ($ok) {
            return redirect()
                ->route('superadmin.assets.dashboard')
                ->with('success', $message);
        }

        return redirect()
            ->route('superadmin.assets.dashboard')
            ->with('warning', $message);
    }

    public function exportAssets(Request $request)
    {
        $this->mergeDefaultAssetCategoryFilter($request);

        $base = $this->assetGridBaseQuery();
        $this->applyCommonFilters($base, $request);
        $this->applyStatFilter($base, $request);

        $rows = $base->orderByDesc('id')->get();

        $columnContext = $this->assetDashboardColumnContext($request);
        $definitions    = $columnContext['definitions'];
        $template       = $columnContext['template'];

        $filename = 'hms_assets_' . $template . '_' . date('Y-m-d_His') . '.xlsx';

        return Excel::download(new AssetExport($rows, $definitions), $filename);
    }

    protected function locationDropdownData(): array
    {
        $companies = Tblcompany::query()->orderBy('company_name')->get(['id', 'company_name']);
        $zones = TblZonesModel::query()->orderBy('name')->get(['id', 'name']);
        $branches = TblLocationModel::query()->orderBy('name')->get(['id', 'name', 'zone_id']);

        return compact('companies', 'zones', 'branches');
    }

    protected function assetGridBaseQuery(): Builder
    {
        return Asset::query()->with([
            'category',
            'department',
            'primaryCompany',
            'primaryZone',
            'primaryBranch',
        ]);
    }

    protected function assertBranchBelongsToZone(Request $request, string $zoneKey, string $branchKey): void
    {
        $zoneId = $request->input($zoneKey);
        $branchId = $request->input($branchKey);
        if (! $branchId) {
            return;
        }
        if (! $zoneId) {
            return;
        }
        $ok = TblLocationModel::query()
            ->where('id', $branchId)
            ->where('zone_id', $zoneId)
            ->exists();
        if (! $ok) {
            throw ValidationException::withMessages([
                $branchKey => 'The selected branch must belong to the selected zone.',
            ]);
        }
    }

    protected function mergeNullableAssetFields(Request $request): void
    {
        foreach (['purchase_date', 'warranty_expiry'] as $k) {
            if ($request->input($k) === '' || $request->input($k) === null) {
                $request->merge([$k => null]);
            }
        }
        foreach (
            [
                'category_id',
                'company_id',
                'zone_id',
                'branch_id',
                'department_id',
            ] as $k
        ) {
            if ($request->input($k) === '' || $request->input($k) === null) {
                $request->merge([$k => null]);
            }
        }
        if ($request->input('asset_code') === '') {
            $request->merge(['asset_code' => null]);
        }
        foreach (['assigned_user_id', 'assigned_hrm_employment_id', 'asset_assignee_label'] as $k) {
            if ($request->input($k) === '' || $request->input($k) === null) {
                $request->merge([$k => null]);
            }
        }
    }

    protected function applyAssetAssigneeToken(Request $request): void
    {
        $tok = trim((string) $request->input('asset_assignee', ''));
        if ($tok === '') {
            $request->merge([
                'assigned_user_id'             => null,
                'assigned_hrm_employment_id'   => null,
            ]);

            return;
        }
        if (preg_match('/^u:(\d+)$/', $tok, $m)) {
            $request->merge([
                'assigned_user_id'             => (int) $m[1],
                'assigned_hrm_employment_id'   => null,
            ]);

            return;
        }
        if (preg_match('/^h:(.+)$/s', $tok, $m)) {
            $decoded = rawurldecode($m[1]);
            $decoded = mb_substr($decoded, 0, 64);
            $request->merge([
                'assigned_user_id'             => null,
                'assigned_hrm_employment_id'   => $decoded !== '' ? $decoded : null,
            ]);

            return;
        }
        $request->merge([
            'assigned_user_id'             => null,
            'assigned_hrm_employment_id'   => null,
        ]);
    }

    protected function validateAssetPayload(Request $request, ?int $ignoreAssetId = null): array
    {
        $uniqueCode = Rule::unique('assets', 'asset_code');
        if ($ignoreAssetId) {
            $uniqueCode = $uniqueCode->ignore($ignoreAssetId);
        }

        return $request->validate([
            'asset_code'               => ['nullable', 'string', 'max:100', $uniqueCode],
            'category_id'              => 'nullable|integer|exists:asset_categories,id',
            'model'                    => 'nullable|string|max:255',
            'serial_number'            => 'nullable|string|max:255',
            'purchase_date'            => 'nullable|date',
            'warranty_expiry'          => 'nullable|date',
            'status'                   => ['required', 'string', Rule::in(Asset::STATUSES)],
            'department_id'            => 'nullable|integer|exists:departments,id',
            'responsible_person'       => 'nullable|string|max:255',
            'assigned_user_id'         => 'nullable|integer|exists:users,id',
            'assigned_hrm_employment_id' => 'nullable|string|max:64',
            'asset_assignee_label'     => 'nullable|string|max:255',
            'consumable_store_id'      => 'nullable|integer|exists:consumable_stores,id',
            'convert_qty'              => 'nullable|numeric|min:0.01',
            'remarks'                  => 'nullable|string|max:5000',
            'company_id'               => 'nullable|integer|exists:company_tbl,id',
            'zone_id'                  => 'nullable|integer|exists:tblzones,id',
            'branch_id'                => 'nullable|integer|exists:tbl_locations,id',
            'type_attributes'          => 'nullable|array',
            'type_attributes.ui_template' => ['nullable', 'string', Rule::in([
                'general', 'system', 'cpu', 'monitor', 'printer', 'cctv', 'switch', 'router', 'nvr', 'dvr',
            ])],
            'type_attributes.system_model' => 'nullable|string|max:255',
            'type_attributes.monitor_model' => 'nullable|string|max:255',
            'type_attributes.os_installed' => 'nullable|string|max:255',
            'type_attributes.processor' => 'nullable|string|max:255',
            'type_attributes.ssd_hdd' => 'nullable|string|max:255',
            'type_attributes.ram' => 'nullable|string|max:255',
            'type_attributes.brand' => 'nullable|string|max:255',
            'type_attributes.camera_name' => 'nullable|string|max:255',
            'type_attributes.ip_address' => 'nullable|string|max:255',
            'type_attributes.dvr_name'   => 'nullable|string|max:255',
            'type_attributes.dvr_channel' => 'nullable|string|max:255',
            'type_attributes.device_username' => 'nullable|string|max:255',
            'type_attributes.device_password' => 'nullable|string|max:255',
        ]);
    }

    protected function sanitizeTypeAttributes($raw): ?array
    {
        if (! is_array($raw)) {
            return null;
        }
        $keys = [
            'ui_template',
            'system_model',
            'monitor_model',
            'os_installed',
            'processor',
            'ssd_hdd',
            'ram',
            'brand',
            'camera_name',
            'ip_address',
            'dvr_name',
            'dvr_channel',
            'device_username',
            'device_password',
        ];
        $out = [];
        foreach ($keys as $k) {
            if (! array_key_exists($k, $raw)) {
                continue;
            }
            $v = $raw[$k];
            if ($v === null || $v === '') {
                continue;
            }
            if (! is_string($v)) {
                continue;
            }
            $out[$k] = mb_substr($v, 0, 2000);
        }

        if (($out['ui_template'] ?? '') === 'general') {
            $out['ui_template'] = 'system';
        }

        return $out === [] ? null : $out;
    }

    protected function mergeDefaultAssetCategoryFilter(Request $request): void
    {
        if (array_key_exists('category_id', $request->all())) {
            return;
        }

        $csv = $this->defaultFirstTabCategoryIdsCsv();
        if ($csv === '') {
            return;
        }

        $request->merge(['category_id' => $csv]);
    }

    protected function defaultFirstTabCategoryIdsCsv(): string
    {
        $systemCsv = $this->systemHardwareCategoryIdsCsv();
        if ($systemCsv !== '') {
            return $systemCsv;
        }

        $firstId = AssetCategory::query()
            ->where('is_active', 1)
            ->orderBy('name')
            ->value('id');

        return $firstId ? (string) (int) $firstId : '';
    }

    protected function applyCommonFilters(Builder $q, Request $request): void
    {
        if ($request->filled('category_id')) {
            $ids = array_values(array_filter(array_map('intval', explode(',', (string) $request->category_id))));
            if ($ids !== []) {
                $q->whereIn('category_id', $ids);
            }
        }

        if ($request->filled('department_id')) {
            $ids = array_values(array_filter(array_map('intval', explode(',', (string) $request->department_id))));
            $ids = array_values(array_filter($ids, static fn ($v) => $v > 0));
            if ($ids !== []) {
                $q->whereIn('department_id', $ids);
            }
        }

        if ($request->filled('status_name')) {
            $statuses = array_values(array_filter(array_map('trim', explode(',', (string) $request->status_name))));
            $statuses = array_values(array_intersect($statuses, Asset::STATUSES));
            if ($statuses !== []) {
                $q->whereIn('status', $statuses);
            }
        }

        if ($request->filled('date_from')) {
            $q->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $q->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->filled('universal_search')) {
            $term = trim((string) $request->universal_search);
            if ($term !== '') {
                $s = '%' . str_replace(['%', '_'], ['\\%', '\\_'], $term) . '%';
                $q->where(function (Builder $w) use ($s) {
                    $w->where('asset_code', 'like', $s)
                        ->orWhere('model', 'like', $s)
                        ->orWhere('responsible_person', 'like', $s)
                        ->orWhere('remarks', 'like', $s)
                        ->orWhere('serial_number', 'like', $s)
                        ->orWhere('type_attributes->system_model', 'like', $s)
                        ->orWhere('type_attributes->monitor_model', 'like', $s)
                        ->orWhere('type_attributes->os_installed', 'like', $s)
                        ->orWhere('type_attributes->processor', 'like', $s)
                        ->orWhere('type_attributes->ssd_hdd', 'like', $s)
                        ->orWhere('type_attributes->ram', 'like', $s)
                        ->orWhere('type_attributes->brand', 'like', $s)
                        ->orWhere('type_attributes->ip_address', 'like', $s)
                        ->orWhereHas('category', function (Builder $c) use ($s) {
                            $c->where('name', 'like', $s);
                        })
                        ->orWhereHas('department', function (Builder $c) use ($s) {
                            $c->where('name', 'like', $s);
                        })
                        ->orWhereHas('primaryCompany', function (Builder $c) use ($s) {
                            $c->where('company_name', 'like', $s);
                        })
                        ->orWhereHas('primaryZone', function (Builder $c) use ($s) {
                            $c->where('name', 'like', $s);
                        })
                        ->orWhereHas('primaryBranch', function (Builder $c) use ($s) {
                            $c->where('name', 'like', $s);
                        });
                });
            }
        }
    }

    protected function applyStatFilter(Builder $q, Request $request): void
    {
        $sf = (string) $request->input('stat_filter', '');
        if ($sf !== '' && in_array($sf, Asset::STATUSES, true)) {
            $q->where('status', $sf);
        }
    }

    protected function computeStats(Builder $base): array
    {
        $total = (clone $base)->count();
        $by    = [];
        foreach (Asset::STATUSES as $st) {
            $by[$st] = (clone $base)->where('status', $st)->count();
        }

        return [
            'total'     => $total,
            'by_status' => $by,
        ];
    }

    protected function assetDashboardColumnContext(Request $request): array
    {
        $categoryCsv = (string) $request->input('category_id', '');
        $template    = $this->inferAssetDashboardTemplate($categoryCsv);

        return [
            'template'    => $template,
            'definitions' => $this->assetDashboardColumnDefinitions($template, $categoryCsv),
        ];
    }

    protected function inferAssetDashboardTemplate(string $categoryIdCsv): string
    {
        $ids = array_values(array_filter(array_map('intval', array_map('trim', explode(',', $categoryIdCsv)))));
        if ($ids === []) {
            return 'all';
        }

        $cats = AssetCategory::query()->whereIn('id', $ids)->get(['id', 'name']);
        if ($cats->count() !== count($ids)) {
            return 'all';
        }

        if ($cats->every(fn ($c) => $this->categoryNameMapsToSystemTemplate((string) $c->name))) {
            return 'system';
        }

        if (count($ids) === 1) {
            return $this->templateLabelToDashboardTemplate((string) ($cats->first()->name ?? ''));
        }

        return 'all';
    }

    protected function systemHardwareCategoryIds(): array
    {
        $ids = AssetCategory::query()
            ->where('is_active', 1)
            ->orderBy('name')
            ->get(['id', 'name'])
            ->filter(fn ($c) => $this->categoryNameMapsToSystemTemplate((string) $c->name))
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->values()
            ->all();
        sort($ids);

        return $ids;
    }

    protected function systemHardwareCategoryIdsCsv(): string
    {
        $ids = $this->systemHardwareCategoryIds();

        return $ids === [] ? '' : implode(',', $ids);
    }

    protected function categoryNameMapsToSystemTemplate(string $name): bool
    {
        $t = strtolower($name);

        return str_contains($t, 'monitor')
            || str_contains($t, 'cpu')
            || str_contains($t, 'desktop')
            || str_contains($t, 'laptop');
    }

    /**
     * Mirrors asset form category → panel template (system, CCTV, …).
     */
    protected function templateLabelToDashboardTemplate(string $name): string
    {
        $t = strtolower($name);
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
        if ($this->categoryNameMapsToSystemTemplate($name)) {
            return 'system';
        }

        return 'all';
    }

    protected function assetDashboardColumnDefinitions(string $template, string $categoryIdCsv = ''): array
    {
        $h = static fn(string $key, string $label, string $thClass = ''): array => $thClass === ''
            ? ['key' => $key, 'label' => $label]
            : ['key' => $key, 'label' => $label, 'th_class' => $thClass];

        $end = [
            $h('warranty', 'WARRANTY'),
            $h('remarks', 'REMARKS'),
            $h('action', 'ACTION', 'text-center'),
        ];

        $all = array_merge([
            $h('sno', 'S.NO'),
            $h('category', 'CATEGORY'),
            $h('company', 'COMPANY'),
            $h('zone', 'ZONE'),
            $h('branch', 'BRANCH'),
            $h('department', 'DEPARTMENT'),
            $h('system_model', 'SYSTEM MODEL'),
            $h('monitor_model', 'MONITOR MODEL'),
            $h('responsible', 'RESPONSIBLE PERSON'),
            $h('os_installed', 'OS INSTALLED'),
            $h('processor', 'PROCESSOR'),
            $h('ssd_hdd', 'SSD/HDD'),
            $h('ram', 'RAM'),
        ], $end);

        $system = array_merge([
            $h('sno', 'S.NO'),
            $h('company', 'COMPANY'),
            $h('zone', 'ZONE'),
            $h('branch', 'BRANCH'),
            $h('department', 'DEPARTMENT'),
            $h('system_model', 'SYSTEM MODEL'),
            $h('monitor_model', 'MONITOR MODEL'),
            $h('serial', 'SERIAL NO.'),
            $h('os_installed', 'OS INSTALLED'),
            $h('processor', 'PROCESSOR'),
            $h('ssd_hdd', 'SSD/HDD'),
            $h('ram', 'RAM'),
            $h('responsible', 'RESPONSIBLE'),
        ], $end);

        $printer = array_merge([
            $h('sno', 'S.NO'),
            $h('company', 'COMPANY'),
            $h('zone', 'ZONE'),
            $h('branch', 'BRANCH'),
            $h('department', 'DEPARTMENT'),
            $h('model', 'MODEL'),
            $h('serial', 'SERIAL NO.'),
            $h('responsible', 'RESPONSIBLE'),
        ], $end);

        $cctv = array_merge([
            $h('sno', 'S.NO'),
            $h('company', 'COMPANY'),
            $h('zone', 'ZONE'),
            $h('branch', 'BRANCH'),
            $h('department', 'DEPARTMENT'),
            $h('camera_name', 'CAMERA NAME'),
            $h('brand', 'BRAND'),
            $h('model', 'MODEL'),
            $h('ip_address', 'IP ADDRESS'),
        ], $end);

        $network = array_merge([
            $h('sno', 'S.NO'),
            $h('company', 'COMPANY'),
            $h('zone', 'ZONE'),
            $h('branch', 'BRANCH'),
            $h('department', 'DEPARTMENT'),
            $h('brand', 'BRAND'),
            $h('model', 'MODEL'),
            $h('ip_address', 'IP ADDRESS'),
            $h('device_username', 'USERNAME'),
        ], $end);

        $nvr = array_merge([
            $h('sno', 'S.NO'),
            $h('company', 'COMPANY'),
            $h('zone', 'ZONE'),
            $h('branch', 'BRANCH'),
            $h('department', 'DEPARTMENT'),
            $h('dvr_name', 'NVR NAME'),
            $h('brand', 'BRAND'),
            $h('model', 'NVR MODEL'),
            $h('ip_address', 'IP ADDRESS'),
            $h('dvr_channel', 'CHANNEL'),
            $h('responsible', 'RESPONSIBLE'),
        ], $end);

        $dvr = array_merge([
            $h('sno', 'S.NO'),
            $h('company', 'COMPANY'),
            $h('zone', 'ZONE'),
            $h('branch', 'BRANCH'),
            $h('department', 'DEPARTMENT'),
            $h('dvr_name', 'DVR NAME'),
            $h('brand', 'BRAND'),
            $h('model', 'DVR MODEL'),
            $h('ip_address', 'IP ADDRESS'),
            $h('dvr_channel', 'CHANNEL'),
            $h('responsible', 'RESPONSIBLE'),
        ], $end);

        $columns = match ($template) {
            'all' => $all,
            'system' => $system,
            'printer' => $printer,
            'cctv' => $cctv,
            'switch', 'router' => $network,
            'nvr' => $nvr,
            'dvr' => $dvr,
            default => $system,
        };

        return $this->ensureCategoryColumnForBroadCategoryView($columns, $categoryIdCsv, $h);
    }

    protected function ensureCategoryColumnForBroadCategoryView(array $columns, string $categoryIdCsv, callable $h): array
    {
        $ids = array_values(array_filter(array_map('intval', array_map('trim', explode(',', $categoryIdCsv)))));
        if (count($ids) === 1) {
            return $columns;
        }

        foreach ($columns as $c) {
            if (($c['key'] ?? '') === 'category') {
                return $columns;
            }
        }

        $catCol = $h('category', 'CATEGORY');
        $out    = [];
        $inserted = false;
        foreach ($columns as $c) {
            $out[] = $c;
            if (! $inserted && ($c['key'] ?? '') === 'sno') {
                $out[] = $catCol;
                $inserted = true;
            }
        }

        if (! $inserted) {
            array_unshift($out, $catCol);
        }

        return $out;
    }

    protected function consumableStoreDepartmentRestrictionIds(?int $userId = null): array
    {
        $userId = $userId ?? (int) auth()->id();
        if ($userId <= 0) {
            return [];
        }
        if (! Schema::hasTable('department_user')) {
            return [];
        }

        return DB::table('department_user')
            ->where('user_id', $userId)
            ->pluck('department_id')
            ->map(fn($id) => (int) $id)
            ->unique()
            ->values()
            ->all();
    }

    protected function consumableStoreAccessLevel(object $user): int
    {
        return (int) ($user->access_limits ?? 0);
    }

    protected function consumableStoreAllowedBranchIds(object $user): ?array
    {
        if (in_array($this->consumableStoreAccessLevel($user), self::CONSUMABLE_ACCESS, true)) {
            return null;
        }

        $lv = $this->consumableStoreAccessLevel($user);
        if ($lv === 2) {
            $zoneIds = array_values(array_filter([(int) ($user->zone_id ?? 0)]));

            $multi = [];
            if (! empty($user->multi_location)) {
                $multi = array_values(array_unique(array_map('intval', explode(',', $user->multi_location))));
            }

            if ($multi !== []) {
                $extra = TblLocationModel::query()
                    ->whereIn('id', $multi)
                    ->pluck('zone_id')
                    ->filter()
                    ->map(fn($z) => (int) $z)
                    ->unique()
                    ->values()
                    ->all();

                $zoneIds = array_values(array_unique(array_merge($zoneIds, $extra)));
            }
            $zoneIds = array_values(array_filter($zoneIds));

            $byZone = $zoneIds === []
                ? collect()
                : TblLocationModel::query()->whereIn('zone_id', $zoneIds)->pluck('id');

            $byMulti = $multi === []
                ? collect()
                : TblLocationModel::query()->whereIn('id', $multi)->pluck('id');

            return $byZone
                ->merge($byMulti)
                ->unique()
                ->map(fn($id) => (int) $id)
                ->values()
                ->all();
        }

        if (in_array($lv, self::CONSUMABLE_OWN_ROWS, true)) {
            $ids = array_values(array_filter([(int) ($user->branch_id ?? 0)]));
            if (! empty($user->multi_location)) {
                $multi = array_map('intval', explode(',', $user->multi_location));
                $ids = array_merge($ids, $multi);
            }

            return array_values(array_unique($ids));
        }

        return [];
    }

    protected function applyConsumableStoreListAccess(Builder $query, object $admin): void
    {
        $branchIds = $this->consumableStoreAllowedBranchIds($admin);
        if ($branchIds !== null) {
            if ($branchIds === []) {
                $query->whereRaw('0 = 1');

                return;
            }
            $query->whereHas('Grn', function (Builder $gq) use ($branchIds) {
                $gq->whereIn('branch_id', $branchIds);
            });
        }

        if (in_array($this->consumableStoreAccessLevel($admin), self::CONSUMABLE_OWN_ROWS, true)) {
            $query->whereHas('Grn', function (Builder $gq) use ($admin) {
                $gq->where('user_id', (int) $admin->id);
            });
        }

        $deptIds = $this->consumableStoreDepartmentRestrictionIds((int) $admin->id);

        if ($deptIds !== []) {
            $query->whereIn('consumable_stores.department_id', $deptIds);
        }
    }

    protected function consumableStoreLineAccessibleByUser(int $consumableStoreId, object $admin): bool
    {
        $q = ConsumableStore::query()->where('id', $consumableStoreId);
        $this->applyConsumableStoreListAccess($q, $admin);

        return $q->exists();
    }
}
