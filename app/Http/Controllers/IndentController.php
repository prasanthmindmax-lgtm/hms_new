<?php

namespace App\Http\Controllers;

use App\Models\ConsumableStore;
use App\Models\Department;
use App\Models\Indent;
use App\Models\IndentHistory;
use App\Models\IndentLine;
use App\Models\Tblcompany;
use App\Models\TblLocationModel;
use App\Models\TblZonesModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;

class IndentController extends Controller
{
    private const TICKET_OPEN_ACCESS = [1, 2, 3, 6];

    private const TICKET_OWN_ROWS = [4, 5];

    protected function indentAuthRow(): object
    {
        $id = (int) auth()->id();
        if ($id <= 0) {
            abort(403);
        }
        $row = DB::table('users')->where('id', $id)->first();
        if ($row) {
            return $row;
        }
        $u = auth()->user();
        if ($u) {
            return is_object($u) ? $u : (object) (array) $u;
        }
        abort(403);
    }

    public function index(Request $request)
    {
        $admin = $this->indentAuthRow();

        return view('superadmin.indents.index', [
            'admin'       => $admin,
            'zones'       => $this->indentZonesForUser($admin),
            'locations'   => $this->indentLocationsForUser($admin),
            'departments' => $this->departmentsForCurrentUser()->where('is_active', 1)->orderBy('name')->get(),
            'statuses'    => Indent::STATUSES,
            'companies'   => Tblcompany::query()->orderBy('company_name')->get(['id', 'company_name']),
        ]);
    }

    public function create(Request $request)
    {
        $admin = $this->indentAuthRow();

        return view('superadmin.indents.create', [
            'admin'       => $admin,
            'zones'       => $this->indentZonesForUser($admin),
            'departments' => $this->departmentsForCurrentUser()->where('is_active', 1)->orderBy('name')->get(),
            'companies'   => Tblcompany::query()->orderBy('company_name')->get(['id', 'company_name']),
        ]);
    }

    public function data(Request $request): JsonResponse
    {
        $admin = $this->indentAuthRow();

        $perPage = min(100, max(5, (int) $request->input('per_page', 15)));
        $page    = max(1, (int) $request->input('page', 1));

        $base = Indent::query()->with([
            'fromDepartment:id,name',
            'toDepartment:id,name',
            'branch:id,name,zone_id',
            'company:id,company_name',
            'creator:id,user_fullname',
            'approver:id,user_fullname',
            'lines.consumableStore:id,item_name,quantity',
        ]);

        $this->applyIndentListScope($base, $admin);
        $this->applyIndentFilters($base, $request, true);

        // Status summary: same scope & filters as the grid, but do not apply the status filter itself.
        $statsBase = Indent::query();
        $this->applyIndentListScope($statsBase, $admin);
        $this->applyIndentFilters($statsBase, $request, false);
        $byStatus = [];
        foreach (Indent::STATUSES as $st) {
            $byStatus[$st] = (clone $statsBase)->where('status', $st)->count();
        }
        $statsTotal = (clone $statsBase)->count();

        $paginator = (clone $base)
            ->orderByDesc('id')
            ->paginate($perPage, ['*'], 'page', $page);

        $rows = $paginator->getCollection()->map(function (Indent $indent) use ($admin) {
            $lines = $indent->lines->map(function (IndentLine $line) {
                $avail = (float) ($line->consumableStore->quantity ?? 0);

                return [
                    'id'                   => $line->id,
                    'item_name'            => $line->item_name,
                    'item_category'        => $line->item_category,
                    'quantity_requested'   => (float) $line->quantity_requested,
                    'quantity_issued'      => (float) $line->quantity_issued,
                    'remaining'            => max(0, (float) $line->quantity_requested - (float) $line->quantity_issued),
                    'consumable_store_id'  => $line->consumable_store_id,
                    'available_in_store'   => $avail,
                ];
            })->values()->all();

            return [
                'id'                 => $indent->id,
                'indent_no'          => $indent->indent_no,
                'status'             => $indent->status,
                'purpose'            => $indent->purpose,
                'remarks'            => $indent->remarks,
                'required_date'      => $indent->required_date?->format('Y-m-d'),
                'from_department_id'   => $indent->from_department_id,
                'to_department_id'     => $indent->to_department_id,
                'from_department_name' => $indent->fromDepartment->name ?? '',
                'to_department_name'   => $indent->toDepartment->name ?? '',
                'branch_id'            => $indent->branch_id,
                'branch_name'          => $indent->branch->name ?? '',
                'company_name'         => $indent->company->company_name ?? '',
                'created_by_name'      => $indent->creator->user_fullname ?? '',
                'created_at'           => $indent->created_at?->format('d/m/Y H:i'),
                'approved_by_name'     => $indent->approver->user_fullname ?? '',
                'lines'                => $lines,
                'can_issue'          => $this->indentMayFulfil($admin) && in_array($indent->status, ['pending'], true),
                'can_dispatch'       => $this->indentMayFulfil($admin) && in_array($indent->status, ['approved', 'partially_issued'], true),
                'can_reject'         => $this->indentMayFulfil($admin) && in_array($indent->status, ['pending'], true),
                'is_creator'         => (int) $indent->created_by === (int) auth()->id(),
            ];
        });
        $paginator->setCollection($rows);

        return response()->json([
            'success'    => true,
            'indents'    => $paginator->items(),
            'stats'      => [
                'total'     => $statsTotal,
                'by_status' => $byStatus,
            ],
            'pagination' => [
                'current_page' => $paginator->currentPage(),
                'last_page'    => $paginator->lastPage(),
                'per_page'     => $paginator->perPage(),
                'total'        => $paginator->total(),
                'from'         => $paginator->firstItem(),
                'to'           => $paginator->lastItem(),
            ],
        ]);
    }

    public function stockOptions(Request $request): JsonResponse
    {
        $request->validate([
            'branch_id' => 'required|integer|exists:tbl_locations,id',
            'q'         => 'nullable|string|max:200',
        ]);

        $admin = $this->indentAuthRow();
        if (! $this->indentBranchAllowed($admin, (int) $request->branch_id)) {
            return response()->json(['success' => false, 'message' => 'You cannot load stock for this branch.'], 403);
        }

        $q = ConsumableStore::query()
            ->with(['Grn:id,branch_id,grn_number', 'Department:id,name'])
            ->whereHas('Grn', function (Builder $gq) use ($request) {
                $gq->where('branch_id', (int) $request->branch_id);
            })
            ->where('quantity', '>', 0);

        if ($request->filled('q')) {
            $needle = '%' . str_replace(['%', '_'], ['\\%', '\\_'], $request->q) . '%';
            $q->where('item_name', 'like', $needle);
        }

        $items = $q->orderBy('item_name')->limit(80)->get()->map(function (ConsumableStore $row) {
            return [
                'id'               => $row->id,
                'item_name'        => $row->item_name,
                'available_qty'    => (float) $row->quantity,
                'grn_number'       => $row->grn_number,
                'department_name'  => $row->Department->name ?? '',
                'item_category'    => null,
            ];
        });

        return response()->json(['success' => true, 'items' => $items]);
    }

    public function store(Request $request): JsonResponse
    {
        $admin = $this->indentAuthRow();

        $validated = $request->validate([
            'company_id'     => 'nullable|integer|exists:company_tbl,id',
            'zone_id'        => 'nullable|integer|exists:tblzones,id',
            'branch_id'      => 'required|integer|exists:tbl_locations,id',
            'from_department_id' => 'required|integer|exists:departments,id',
            'to_department_id'   => 'required|integer|exists:departments,id',
            'remarks'        => 'nullable|string|max:10000',
            'lines'          => 'required|array|min:1',
            'lines.*.consumable_store_id' => 'required|integer|exists:consumable_stores,id',
            'lines.*.quantity_requested'  => 'required|numeric|min:0.01',
            'lines.*.item_category'       => 'nullable|string|max:191',
        ]);

        $deptIds = $this->indentDepartmentRestrictionIds((int) auth()->id());
        if ($deptIds !== [] && ! in_array((int) $validated['from_department_id'], $deptIds, true)) {
            throw ValidationException::withMessages([
                'from_department_id' => ['Your “from” department must be one you are assigned to.'],
            ]);
        }

        if (! $this->indentBranchAllowed($admin, (int) $validated['branch_id'])) {
            throw ValidationException::withMessages([
                'branch_id' => ['You cannot use this branch for indents.'],
            ]);
        }

        $branch = TblLocationModel::query()->findOrFail((int) $validated['branch_id']);
        $zoneId = (int) ($validated['zone_id'] ?: $branch->zone_id);

        foreach ($validated['lines'] as $idx => $line) {
            $this->assertConsumableLineAvailableForBranch(
                (int) $line['consumable_store_id'],
                (int) $validated['branch_id'],
                (float) $line['quantity_requested'],
                "lines.$idx.quantity_requested"
            );
        }

        $indent = DB::transaction(function () use ($validated, $branch, $zoneId) {
            $indent = Indent::create([
                'indent_no'      => $this->nextIndentNo(),
                'company_id'     => $validated['company_id'] ?? null,
                'zone_id'        => $zoneId ?: null,
                'branch_id'      => (int) $validated['branch_id'],
                'from_department_id' => (int) $validated['from_department_id'],
                'to_department_id'   => (int) $validated['to_department_id'],
                'purpose'        => '',
                'required_date'  => null,
                'remarks'        => $validated['remarks'] ?? null,
                'status'         => 'pending',
                'created_by'     => (int) auth()->id(),
            ]);

            foreach ($validated['lines'] as $line) {
                $cs = ConsumableStore::query()->findOrFail((int) $line['consumable_store_id']);
                IndentLine::create([
                    'indent_id'             => $indent->id,
                    'consumable_store_id'   => $cs->id,
                    'item_name'             => $cs->item_name,
                    'item_category'         => $line['item_category'] ?? null,
                    'quantity_requested'    => $line['quantity_requested'],
                    'quantity_issued'       => 0,
                ]);
            }

            $this->logIndentHistory($indent, 'created', [
                'indent_no' => $indent->indent_no,
                'lines'     => count($validated['lines']),
            ]);

            return $indent;
        });

        return response()->json([
            'success' => true,
            'message' => 'Indent created successfully.',
            'indent'  => ['id' => $indent->id, 'indent_no' => $indent->indent_no],
        ]);
    }

    public function updateStatus(Request $request, Indent $indent): JsonResponse
    {
        $admin = $this->indentAuthRow();
        if (! $this->indentMayFulfil($admin)) {
            return response()->json(['success' => false, 'message' => 'You are not allowed to update indent status.'], 403);
        }
        if (! $this->indentUserCanView($indent, $admin)) {
            return response()->json(['success' => false, 'message' => 'Forbidden.'], 403);
        }

        $validated = $request->validate([
            'status'           => 'required|string|in:approved,rejected',
            'rejection_reason' => 'required_if:status,rejected|nullable|string|max:5000',
        ]);

        if ($indent->status !== 'pending') {
            return response()->json(['success' => false, 'message' => 'Only pending indents can be issued or rejected.'], 422);
        }

        if ($validated['status'] === 'approved') {
            $indent->status           = 'approved';
            $indent->approved_by      = (int) auth()->id();
            $indent->approved_at      = now();
            $indent->rejected_by      = null;
            $indent->rejected_at      = null;
            $indent->rejection_reason = null;
            $indent->last_status_by   = (int) auth()->id();
            $indent->save();
            $this->logIndentHistory($indent, 'approved', [
                'by' => (int) auth()->id(),
            ]);
        } else {
            $indent->status           = 'rejected';
            $indent->rejected_by      = (int) auth()->id();
            $indent->rejected_at      = now();
            $indent->rejection_reason = $validated['rejection_reason'] ?? '';
            $indent->approved_by      = null;
            $indent->approved_at      = null;
            $indent->last_status_by   = (int) auth()->id();
            $indent->save();
            $this->logIndentHistory($indent, 'rejected', [
                'by'     => (int) auth()->id(),
                'reason' => $indent->rejection_reason,
            ]);
        }

        return response()->json([
            'success' => true,
            'message'   => $validated['status'] === 'approved' ? 'Indent approved.' : 'Indent rejected.',
            'status'    => $indent->status,
        ]);
    }

    public function issue(Request $request, Indent $indent): JsonResponse
    {
        $admin = $this->indentAuthRow();
        if (! $this->indentMayFulfil($admin)) {
            return response()->json(['success' => false, 'message' => 'You are not allowed to issue stock against indents.'], 403);
        }
        if (! $this->indentUserCanView($indent, $admin)) {
            return response()->json(['success' => false, 'message' => 'Forbidden.'], 403);
        }

        if (! in_array($indent->status, ['approved', 'partially_issued'], true)) {
            return response()->json(['success' => false, 'message' => 'Indent must be approved before issuing items.'], 422);
        }

        $validated = $request->validate([
            'lines'               => 'required|array|min:1',
            'lines.*.id'          => 'required|integer|exists:indent_lines,id',
            'lines.*.issue_qty'   => 'required|numeric|min:0.01',
        ]);

        DB::transaction(function () use ($indent, $validated) {
            foreach ($validated['lines'] as $row) {
                $lineId   = (int) $row['id'];
                $issueQty = (float) $row['issue_qty'];

                $line = IndentLine::query()
                    ->where('indent_id', $indent->id)
                    ->whereKey($lineId)
                    ->lockForUpdate()
                    ->first();
                if (! $line) {
                    throw ValidationException::withMessages(['lines' => ['Invalid indent line.']]);
                }

                $remaining = max(0, (float) $line->quantity_requested - (float) $line->quantity_issued);
                if ($issueQty > $remaining + 0.00001) {
                    throw ValidationException::withMessages([
                        'lines' => ['Issue quantity cannot exceed remaining for line #' . $line->id . '.'],
                    ]);
                }

                $cs = ConsumableStore::query()->where('id', $line->consumable_store_id)->lockForUpdate()->first();
                if (! $cs) {
                    throw ValidationException::withMessages(['lines' => ['Consumable store row missing.']]);
                }
                if ((float) $cs->quantity + 0.00001 < $issueQty) {
                    throw ValidationException::withMessages([
                        'lines' => ['Insufficient stock for "' . $cs->item_name . '".'],
                    ]);
                }

                $cs->quantity = (float) $cs->quantity - $issueQty;
                $cs->save();

                $line->quantity_issued = (float) $line->quantity_issued + $issueQty;
                $line->save();

                $this->logIndentHistory($indent, 'issued', [
                    'indent_line_id'      => $line->id,
                    'consumable_store_id' => $cs->id,
                    'issue_qty'           => $issueQty,
                    'by'                  => (int) auth()->id(),
                ]);
            }

            $indent->refresh();
            $this->refreshIndentAggregateStatus($indent);
        });

        $indent->refresh();

        return response()->json([
            'success' => true,
            'message' => 'Stock issued against indent.',
            'status'  => $indent->status,
        ]);
    }

    public function history(Indent $indent): JsonResponse
    {
        $admin = $this->indentAuthRow();
        if (! $this->indentUserCanView($indent, $admin)) {
            return response()->json(['success' => false, 'message' => 'Forbidden.'], 403);
        }

        $items = $indent->histories()->with('user:id,user_fullname')->get()->map(function (IndentHistory $h) {
            return [
                'id'         => $h->id,
                'action'     => $h->action,
                'payload'    => $h->payload,
                'user_name'  => $h->user->user_fullname ?? '',
                'created_at' => $h->created_at?->format('d/m/Y H:i'),
            ];
        });

        return response()->json(['success' => true, 'items' => $items]);
    }

    public function show(Indent $indent): JsonResponse
    {
        $admin = $this->indentAuthRow();
        if (! $this->indentUserCanView($indent, $admin)) {
            return response()->json(['success' => false, 'message' => 'Forbidden.'], 403);
        }

        $indent->load([
            'fromDepartment:id,name',
            'toDepartment:id,name',
            'branch:id,name',
            'zone:id,name',
            'company:id,company_name',
            'creator:id,user_fullname',
            'approver:id,user_fullname',
            'rejector:id,user_fullname',
            'lastStatusBy:id,user_fullname',
            'lines.consumableStore',
        ]);

        return response()->json(['success' => true, 'indent' => $indent]);
    }

    protected function indentDepartmentRestrictionIds(?int $userId = null): array
    {
        $userId = $userId ?? (int) auth()->id();
        if ($userId <= 0 || ! Schema::hasTable('department_user')) {
            return [];
        }

        return DB::table('department_user')
            ->where('user_id', $userId)
            ->pluck('department_id')
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values()
            ->all();
    }

    protected function departmentsForCurrentUser(): Builder
    {
        $q = Department::query()->orderBy('id');
        $ids = $this->indentDepartmentRestrictionIds();
        if ($ids !== []) {
            $q->whereIn('id', $ids);
        }

        return $q;
    }

    protected function indentAccessLevel(object $user): int
    {
        return (int) ($user->access_limits ?? 0);
    }

    protected function indentIsOpenAccess(object $user): bool
    {
        return in_array($this->indentAccessLevel($user), self::TICKET_OPEN_ACCESS, true);
    }

    /**
     * @return list<int>|null
     */
    protected function indentAllowedBranchIds(object $user): ?array
    {
        if ($this->indentIsOpenAccess($user)) {
            return null;
        }

        $lv = $this->indentAccessLevel($user);
        if ($lv === 2) {
            $zoneIds = array_values(array_filter([(int) ($user->zone_id ?? 0)]));
            $multi    = [];
            if (! empty($user->multi_location)) {
                $multi = array_values(array_unique(array_map('intval', explode(',', $user->multi_location))));
            }
            if ($multi !== []) {
                $extra = TblLocationModel::query()
                    ->whereIn('id', $multi)
                    ->pluck('zone_id')
                    ->filter()
                    ->map(fn ($z) => (int) $z)
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
                ->map(fn ($id) => (int) $id)
                ->values()
                ->all();
        }

        if (in_array($lv, self::TICKET_OWN_ROWS, true)) {
            $ids = array_values(array_filter([(int) ($user->branch_id ?? 0)]));
            if (! empty($user->multi_location)) {
                $multi = array_map('intval', explode(',', $user->multi_location));
                $ids   = array_merge($ids, $multi);
            }

            return array_values(array_unique($ids));
        }

        return null;
    }

    protected function applyIndentListScope(Builder $query, object $admin): void
    {
        $branchIds = $this->indentAllowedBranchIds($admin);
        if ($branchIds !== null) {
            if ($branchIds === []) {
                $query->whereRaw('0 = 1');

                return;
            }
            $query->whereIn('branch_id', $branchIds);
        }

        if (in_array($this->indentAccessLevel($admin), self::TICKET_OWN_ROWS, true)) {
            $query->where('created_by', (int) $admin->id);
        }

        $deptIds = $this->indentDepartmentRestrictionIds((int) $admin->id);
        if ($deptIds !== []) {
            $query->where(function (Builder $q) use ($deptIds) {
                $q->whereIn('from_department_id', $deptIds)
                    ->orWhereIn('to_department_id', $deptIds);
            });
        }
    }

    protected function indentUserCanView(Indent $indent, object $admin): bool
    {
        $q = Indent::query()->whereKey($indent->getKey());
        $this->applyIndentListScope($q, $admin);

        return $q->exists();
    }

    protected function indentBranchAllowed(object $admin, int $branchId): bool
    {
        $allowed = $this->indentAllowedBranchIds($admin);
        if ($allowed === null) {
            return true;
        }
        if ($allowed === []) {
            return false;
        }

        return in_array($branchId, $allowed, true);
    }

    protected function indentMayFulfil(object $admin): bool
    {
        return in_array($this->indentAccessLevel($admin), Indent::FULFIL_ACCESS_LEVELS, true);
    }

    protected function applyIndentFilters(Builder $query, Request $request, bool $applyStatusFilter = true): void
    {
        if ($request->filled('department_id')) {
            $ids = array_values(array_filter(array_map('intval', explode(',', (string) $request->department_id))));
            if ($ids !== []) {
                $query->where(function (Builder $q) use ($ids) {
                    $q->whereIn('from_department_id', $ids)
                        ->orWhereIn('to_department_id', $ids);
                });
            }
        }

        if ($applyStatusFilter && $request->filled('status')) {
            $raw = explode(',', (string) $request->status);
            $st  = array_values(array_intersect($raw, Indent::STATUSES));
            if ($st !== []) {
                $query->whereIn('status', $st);
            }
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->filled('branch_id')) {
            $b = array_values(array_filter(array_map('intval', explode(',', (string) $request->branch_id))));
            if ($b !== []) {
                $query->whereIn('branch_id', $b);
            }
        }

        if ($request->filled('zone_id')) {
            $z = array_values(array_filter(array_map('intval', explode(',', (string) $request->zone_id))));
            if ($z !== []) {
                $query->whereIn('zone_id', $z);
            }
        }

        $qText = trim((string) $request->input('universal_search', ''));
        if ($qText !== '') {
            $needle = '%' . str_replace(['%', '_'], ['\\%', '\\_'], $qText) . '%';
            $query->where(function (Builder $w) use ($needle) {
                $w->where('indent_no', 'like', $needle)
                    ->orWhere('purpose', 'like', $needle)
                    ->orWhere('remarks', 'like', $needle)
                    ->orWhereHas('fromDepartment', fn (Builder $d) => $d->where('name', 'like', $needle))
                    ->orWhereHas('toDepartment', fn (Builder $d) => $d->where('name', 'like', $needle));
            });
        }
    }

    protected function indentZonesForUser(object $admin)
    {
        $data = $this->indentZonesAndLocations($admin);

        return $data['zones'];
    }

    protected function indentLocationsForUser(object $admin)
    {
        $data = $this->indentZonesAndLocations($admin);

        return $data['locations'];
    }

    protected function indentZonesAndLocations(object $admin): array
    {
        $locations = collect();
        $zones     = collect();

        if ($this->indentAccessLevel($admin) === 1) {
            $zones     = TblZonesModel::query()->select('name', 'id')->orderBy('name')->get();
            $locations = TblLocationModel::query()->select('name', 'id', 'zone_id')->orderBy('name')->get();
        } elseif ($this->indentAccessLevel($admin) === 2) {
            $zoneIds = [];
            if (! empty($admin->multi_location)) {
                $multiLocations = explode(',', $admin->multi_location);
                $locationsFromMulti = TblLocationModel::query()
                    ->whereIn('id', $multiLocations)
                    ->pluck('zone_id')
                    ->unique()
                    ->all();
                $zoneIds = array_unique(array_merge([(int) $admin->zone_id], array_map('intval', $locationsFromMulti)));
            } else {
                $zoneIds = [(int) $admin->zone_id];
            }
            $zoneIds = array_values(array_filter($zoneIds));

            $locations = TblLocationModel::query()
                ->select('name', 'id', 'zone_id')
                ->whereIn('zone_id', $zoneIds)
                ->orderBy('name')
                ->get();

            if (! empty($admin->multi_location)) {
                $multi = array_map('intval', explode(',', $admin->multi_location));
                $extra = TblLocationModel::query()
                    ->select('name', 'id', 'zone_id')
                    ->whereIn('id', $multi)
                    ->orderBy('name')
                    ->get();
                $locations = $locations->merge($extra)->unique('id')->values();
            }

            $zones = TblZonesModel::query()->select('name', 'id')->whereIn('id', $zoneIds)->orderBy('name')->get();
        } else {
            $branchIds = array_filter(array_merge(
                [(int) ($admin->branch_id ?? 0)],
                ! empty($admin->multi_location) ? array_map('intval', explode(',', $admin->multi_location)) : []
            ));
            $branchIds = array_values(array_unique(array_filter($branchIds)));

            if ($branchIds !== []) {
                $locations = TblLocationModel::query()
                    ->select('name', 'id', 'zone_id')
                    ->whereIn('id', $branchIds)
                    ->orderBy('name')
                    ->get();
            }
            $zoneIds = $locations->pluck('zone_id')->filter()->unique()->values()->all();
            if ($zoneIds !== []) {
                $zones = TblZonesModel::query()->select('name', 'id')->whereIn('id', $zoneIds)->orderBy('name')->get();
            }
        }

        return compact('zones', 'locations');
    }

    protected function assertConsumableLineAvailableForBranch(int $consumableStoreId, int $branchId, float $qty, string $errorKey): void
    {
        $cs = ConsumableStore::query()->with('Grn:id,branch_id')->find($consumableStoreId);
        if (! $cs || ! $cs->Grn || (int) $cs->Grn->branch_id !== $branchId) {
            throw ValidationException::withMessages([
                $errorKey => ['Selected item is not available at this branch.'],
            ]);
        }
        if ((float) $cs->quantity + 0.00001 < $qty) {
            throw ValidationException::withMessages([
                $errorKey => ['Insufficient quantity for "' . $cs->item_name . '".'],
            ]);
        }
    }

    protected function nextIndentNo(): string
    {
        $prefix = 'IND-' . '-';
        $seq    = (int) Indent::query()->where('indent_no', 'like', $prefix . '%')->count() + 1;

        return $prefix . str_pad((string) $seq, 4, '0', STR_PAD_LEFT);
    }

    protected function logIndentHistory(Indent $indent, string $action, array $payload = []): void
    {
        IndentHistory::create([
            'indent_id'  => $indent->id,
            'user_id'    => (int) auth()->id(),
            'action'     => $action,
            'payload'    => $payload ?: null,
            'created_at' => now(),
        ]);
    }

    protected function refreshIndentAggregateStatus(Indent $indent): void
    {
        $indent->load('lines');
        $anyIssued = false;
        $allFull   = true;
        foreach ($indent->lines as $line) {
            $req = (float) $line->quantity_requested;
            $iss = (float) $line->quantity_issued;
            if ($iss > 0.00001) {
                $anyIssued = true;
            }
            if ($iss + 0.00001 < $req) {
                $allFull = false;
            }
        }
        if ($allFull && $indent->lines->isNotEmpty()) {
            $indent->status = 'issued';
        } elseif ($anyIssued) {
            $indent->status = 'partially_issued';
        }
        $indent->last_status_by = (int) auth()->id();
        $indent->save();
    }
}
