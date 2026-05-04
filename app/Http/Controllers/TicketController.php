<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\EntityComment;
use App\Models\Ticket;
use App\Models\TblLocationModel;
use App\Models\TblZonesModel;
use App\Models\IssueCategory;
use App\Models\TicketCategory;
use App\Models\usermanagementdetails;
use App\Exports\TicketExport;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\File;
use Maatwebsite\Excel\Facades\Excel;

class TicketController extends Controller
{
    private const OPEN = [1, 2, 6];

    private const OWN_ROWS = [3, 4, 5];

    protected function ticketDepartmentRestrictionIds(?int $userId = null): array
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
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values()
            ->all();
    }

    protected function ticketUserHasDepartmentRestriction(): bool
    {
        return $this->ticketDepartmentRestrictionIds() !== [];
    }

    protected function departmentsForCurrentUser()
    {
        $q = Department::query()->orderBy('id');
        $ids = $this->ticketDepartmentRestrictionIds();
        if ($ids !== []) {
            $q->whereIn('id', $ids);
        }

        return $q;
    }

    protected function assertDepartmentsAllowedForUser(int $fromDepartmentId, int $toDepartmentId): ?JsonResponse
    {
        $allowed = $this->ticketDepartmentRestrictionIds();
        if ($allowed === []) {
            return null;
        }

        $okFrom = in_array($fromDepartmentId, $allowed, true);
        $okTo = in_array($toDepartmentId, $allowed, true);
        if (!$okFrom || !$okTo) {
            return response()->json([
                'success' => false,
                'message' => 'You can only raise or edit tickets for departments you are assigned to.',
            ], 422);
        }

        return null;
    }

    public function getDepartments(Request $request)
    {
        $admin   = auth()->user();
        $perPage = $request->get('per_page', 10);

        $deptQuery = $this->departmentsForCurrentUser();
        if (Schema::hasTable('department_user')) {
            $deptQuery->with(['assignedUsers:id,user_fullname']);
        }

        $departments = $deptQuery
            ->paginate($perPage)
            ->appends(['per_page' => $perPage]);

        $departmentUsersList = $this->ticketUserHasDepartmentRestriction()
            ? collect()
            : usermanagementdetails::query()
                ->orderBy('user_fullname')
                ->get(['id', 'user_fullname']);

        return view('superadmin.tickets.departments', [
            'admin'       => $admin,
            'departments' => $departments,
            'perPage'     => $perPage,
            'departmentUsersList' => $departmentUsersList,
            'canAssignDepartmentUsers' => ! $this->ticketUserHasDepartmentRestriction(),
        ]);
    }

    public function departmentAssignedUsers(Request $request)
    {
        if ($this->ticketUserHasDepartmentRestriction()) {
            return response()->json(['success' => false, 'message' => 'Forbidden.'], 403);
        }

        $request->validate([
            'department_id' => 'required|integer|exists:departments,id',
        ]);

        $departmentId = (int) $request->department_id;
        $userIds = Schema::hasTable('department_user')
            ? DB::table('department_user')
                ->where('department_id', $departmentId)
                ->pluck('user_id')
                ->map(fn ($id) => (int) $id)
                ->values()
                ->all()
            : [];

        return response()->json([
            'success' => true,
            'user_ids' => $userIds,
        ]);
    }

    public function syncDepartmentUsers(Request $request)
    {
        if ($this->ticketUserHasDepartmentRestriction()) {
            return response()->json(['success' => false, 'message' => 'You cannot manage department user assignments.'], 403);
        }

        $validated = $request->validate([
            'department_id' => 'required|integer|exists:departments,id',
            'user_ids' => ['nullable', 'array'],
            'user_ids.*' => 'integer|exists:users,id',
        ]);

        if (! Schema::hasTable('department_user')) {
            return response()->json([
                'success' => false,
                'message' => 'Assignments table is missing. Run database migrations.',
            ], 500);
        }

        $departmentId = (int) $validated['department_id'];
        $userIds = array_values(array_unique(array_map('intval', $validated['user_ids'] ?? [])));

        $now = now();
        DB::transaction(function () use ($departmentId, $userIds, $now) {
            DB::table('department_user')->where('department_id', $departmentId)->delete();
            foreach ($userIds as $uid) {
                if ($uid <= 0) {
                    continue;
                }
                DB::table('department_user')->insert([
                    'department_id' => $departmentId,
                    'user_id' => $uid,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'Users assigned to this department successfully.',
        ]);
    }

    public function storeDepartments(Request $request)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'is_active' => ['required', Rule::in([0, 1])],
        ];
        if (! $this->ticketUserHasDepartmentRestriction()) {
            $rules['user_ids'] = ['nullable', 'array'];
            $rules['user_ids.*'] = ['integer', 'exists:users,id'];
        }
        $request->validate($rules);

        if ($this->ticketUserHasDepartmentRestriction() && ! $request->filled('id')) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot create new departments.',
            ], 403);
        }

        $id = $request->id;

        if ($this->ticketUserHasDepartmentRestriction() && $id !== null && $id !== '') {
            $allowed = $this->ticketDepartmentRestrictionIds();
            if (! in_array((int) $id, $allowed, true)) {
                return response()->json([
                    'success' => false,
                    'message' => 'You cannot edit this department.',
                ], 403);
            }
        }

        $data = [
            'name'       => $request->name,
            'description' => $request->description,
            'is_active'  => $request->is_active,
            'created_by' => auth()->id(),
        ];

        if (!empty($id)) {
            Department::where('id', $id)->update($data);
            $departments = Department::find($id);
            $message = 'Department updated successfully!';
        } else {
            $departments = Department::create($data);
            $message = 'Department created successfully!';
        }

        if (! $this->ticketUserHasDepartmentRestriction()
            && Schema::hasTable('department_user')
            && $departments) {
            $deptId = (int) $departments->id;
            $userIds = array_values(array_unique(array_map(
                'intval',
                $request->input('user_ids', [])
            )));
            $now = now();
            DB::transaction(function () use ($deptId, $userIds, $now) {
                DB::table('department_user')->where('department_id', $deptId)->delete();
                foreach ($userIds as $uid) {
                    if ($uid <= 0) {
                        continue;
                    }
                    DB::table('department_user')->insert([
                        'department_id' => $deptId,
                        'user_id' => $uid,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);
                }
            });
        }

        return response()->json([
            'success'  => true,
            'message'  => $message,
            'departments' => $departments,
        ]);
    }

    public function getTicketCategories(Request $request)
    {
        $admin = auth()->user();
        $perPage = $request->get('per_page', 10);

        $departments = Department::query()->where('is_active', 1)->orderBy('id')->get();

        $rows = TicketCategory::query()
            ->with(['createdBy:id,user_fullname'])
            ->where('is_active', 1)
            ->orderBy('id', 'desc')
            ->paginate($perPage)
            ->appends(['per_page' => $perPage]);

        return view('superadmin.tickets.ticket_categories', [
            'admin' => $admin,
            'ticketCategories' => $rows,
            'perPage' => $perPage,
            'departments' => $departments,
        ]);
    }

    public function storeTicketCategories(Request $request)
    {
        $request->validate([
            'id' => 'nullable|integer|exists:ticket_categories,id',
            'department_id' => 'required|integer|exists:departments,id',
            'name' => 'required|string|max:255',
            'is_active' => ['required', Rule::in([0, 1])],
        ]);

        $id = $request->input('id');

        if (! empty($id)) {
            $row = TicketCategory::findOrFail($id);
            $row->department_id = $request->department_id;
            $row->name = $request->name;
            $row->is_active = $request->is_active;
            $row->created_by = auth()->id();
            $row->save();
            $message = 'Ticket Category updated successfully!';
        } else {
            $row = TicketCategory::create([
                'department_id' => $request->department_id,
                'name' => $request->name,
                'is_active' => $request->is_active,
                'created_by' => auth()->id(),
            ]);
            $message = 'Ticket Category created successfully!';
        }

        return response()->json([
            'success' => true,
            'message' => $message,
            'ticketCategory' => $row,
        ]);
    }

    /**
     * Ticket categories for an issue: global (no department) plus categories for this department.
     */
    public function getTicketCategoriesByDepartment($department_id)
    {
        $departmentId = (int) $department_id;

        $categories = TicketCategory::query()
            ->where('is_active', true)
            ->where(function (Builder $q) use ($departmentId) {
                $q->whereNull('department_id')
                    ->orWhere('department_id', $departmentId);
            })
            ->orderBy('name')
            ->get(['id', 'name', 'department_id']);

        return response()->json($categories);
    }

    public function getIssueCategories(Request $request)
    {
        $admin = auth()->user();
        $perPage = $request->get('per_page', 10);
        $departments = $this->departmentsForCurrentUser()->orderBy('id', 'asc')->get();
        $query = IssueCategory::query()
            ->with(['department', 'ticketCategory', 'createdBy:id,user_fullname'])
            ->orderBy('id', 'desc');

        $allowedDept = $this->ticketDepartmentRestrictionIds();
        if ($allowedDept !== []) {
            $query->whereIn('department_id', $allowedDept);
        }

        $issueCategories = $query->paginate($perPage)
            ->appends(['per_page' => $perPage]);

        $ticketCategoryParents = TicketCategory::query()
            ->orderBy('name')
            ->get(['id', 'name', 'department_id']);

        return view('superadmin.tickets.issue_categories', [
            'admin' => $admin,
            'departments' => $departments,
            'ticketCategoryParents' => $ticketCategoryParents,
            'issueCategories' => $issueCategories,
            'perPage' => $perPage,
        ]);
    }

    protected function hasApplicableTicketCategoriesForDepartment(int $departmentId): bool
    {
        return TicketCategory::query()
            ->where(function (Builder $q) use ($departmentId) {
                $q->whereNull('department_id')
                    ->orWhere('department_id', $departmentId);
            })
            ->exists();
    }

    public function storeIssueCategories(Request $request)
    {
        $slaRaw = trim((string) $request->input('sla_time', ''));
        if ($slaRaw !== '' && preg_match('/^(\d{1,2}):(\d{2})(?::(\d{2}))?$/', $slaRaw, $m)) {
            $h = min(23, max(0, (int) $m[1]));
            $i = min(59, max(0, (int) $m[2]));
            $request->merge(['sla_time' => sprintf('%02d:%02d', $h, $i)]);
        }

        $request->validate([
            'department_id' => 'required|integer|exists:departments,id',
            'name' => 'required|string|max:255',
            'sla_time' => ['required', 'regex:/^(?:[01]\d|2[0-3]):[0-5]\d$/'],
            'is_active' => ['required', Rule::in([0, 1])],
        ]);

        $deptId = (int) $request->department_id;
        $allowedDept = $this->ticketDepartmentRestrictionIds();
        if ($allowedDept !== [] && ! in_array($deptId, $allowedDept, true)) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot manage categories for this department.',
            ], 403);
        }

        $requiresTicket = $this->hasApplicableTicketCategoriesForDepartment($deptId);
        $request->validate([
            'ticket_category_id' => $requiresTicket
                ? 'required|integer|exists:ticket_categories,id'
                : 'nullable|integer|exists:ticket_categories,id',
        ]);

        $ticketCategoryId = null;
        if ($requiresTicket) {
            $tcRow = TicketCategory::query()->find($request->ticket_category_id);
            if (! $tcRow) {
                return response()->json(['success' => false, 'message' => 'Invalid ticket category.'], 422);
            }
            if ($tcRow->department_id !== null && (int) $tcRow->department_id !== $deptId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Selected ticket category does not belong to the selected department.',
                ], 422);
            }
            $ticketCategoryId = (int) $request->ticket_category_id;
        }

        $id = $request->id;

        $data = [
            'ticket_category_id' => $ticketCategoryId,
            'department_id' => $request->department_id,
            'name' => $request->name,
            'sla_time' => $request->sla_time,
            'description' => $request->description,
            'is_active' => $request->is_active,
            'created_by' => auth()->id(),
        ];

        if (! empty($id)) {
            $existing = IssueCategory::find($id);
            if ($existing && $allowedDept !== [] && ! in_array((int) $existing->department_id, $allowedDept, true)) {
                return response()->json([
                    'success' => false,
                    'message' => 'You cannot edit this issue category.',
                ], 403);
            }
            IssueCategory::where('id', $id)->update($data);
            $issueCategories = IssueCategory::find($id);
            $message = 'Issue Category updated successfully!';
        } else {
            $issueCategories = IssueCategory::create($data);
            $message = 'Issue Category created successfully!';
        }

        return response()->json([
            'success' => true,
            'message' => $message,
            'issueCategories' => $issueCategories,
        ]);
    }

    protected function canEditTicket(Ticket $ticket): bool
    {
        $access = $this->ticketAccessFromAuth();

        if (!$access || isset($this->accessControl($ticket)['error'])) {
            return false;
        }

        if ($ticket->status !== 'open') {
            return false;
        }

        $userId = (int) auth()->id();

        if ($this->ticketAccessIsOpen($access)) {
            return true;
        }

        return (int) $ticket->created_by === $userId;
    }

    protected function namesForUsers(iterable $userIds): array
    {
        $ids = collect($userIds)->filter()->unique()->values();
        if ($ids->isEmpty()) {
            return [];
        }

        return usermanagementdetails::query()
            ->whereIn('id', $ids)
            ->pluck('user_fullname', 'id')
            ->all();
    }

    protected function accessControl(?Ticket $ticket = null, ?int $locationId = null)
    {
        $a = $this->ticketAccessFromAuth();

        if (!$a) {
            return ['error' => response()->json(['success' => false, 'message' => 'Unauthenticated.'], 401)];
        }

        if ($locationId && $err = $this->ticketAccessLocationError($a, $locationId)) {
            return ['error' => $err];
        }

        if ($ticket && !$this->ticketAccessAllows($a, $ticket)) {
            return ['error' => false];
        }

        return ['access' => $a];
    }

    protected function getIds(Request $request, string $key, $model = null, $extraWhere = [])
    {
        $raw = $request->input($key);
        if (!$raw) return [];

        $ids = collect(is_array($raw) ? $raw : explode(',', $raw))
            ->map(fn($v) => trim($v))
            ->filter(fn($v) => $v !== '')
            ->unique()
            ->values();

        if ($ids->isEmpty()) return [];

        if (!$model) return $ids->all();

        $query = $model::query()->whereIn('id', $ids);

        foreach ($extraWhere as $k => $v) {
            $query->where($k, $v);
        }

        return $query->pluck('id')->map(fn($id) => (int) $id)->all();
    }

    protected function applyFilters(Builder $query, Request $request, int $userId)
    {
        // scope (mine / department)
        if ($request->get('scope') === 'mine') {
            $query->where('created_by', $userId);
        } elseif ($request->get('scope') === 'department') {
            $deptIds = DB::table('department_user')
                ->where('user_id', $userId)
                ->pluck('department_id');

            $query->whereIn('to_department_id', $deptIds);
        }

        // access
        if ($a = $this->ticketAccessFromAuth()) {
            $this->ticketAccessApplyScope($a, $query);
        } else {
            $query->whereRaw('0=1');
        }

        // date filters
        if ($d = $request->input('date_from')) {
            $query->whereDate('created_at', '>=', $d);
        }
        if ($d = $request->input('date_to')) {
            $query->whereDate('created_at', '<=', $d);
        }

        // department
        $dept = $this->getIds($request, 'to_department_id', Department::class, ['is_active' => 1]);
        $restrictedDeptIds = $this->ticketDepartmentRestrictionIds($userId);
        if ($restrictedDeptIds !== [] && $dept !== []) {
            $dept = array_values(array_intersect($dept, $restrictedDeptIds));
        }
        if ($dept) {
            $query->whereIn('to_department_id', $dept);
        }

        // status
        $status = $this->getIds($request, 'status');
        if ($status) $query->whereIn('status', $status);

        // zone
        $zones = $this->getIds($request, 'zone_id', TblZonesModel::class);
        if ($zones) {
            $query->whereHas('location', fn($q) => $q->whereIn('zone_id', $zones));
        }

        // branch
        $branches = $this->getIds($request, 'branch_id', TblLocationModel::class);
        if ($branches) {
            $query->whereIn('location_id', $branches);
        }

        // search
        $qText = trim($request->input('universal_search') ?: $request->input('raised_by', ''));
        if ($qText) {
            $needle = "%{$qText}%";
            $query->where(function ($q) use ($needle) {
                $q->where('ticket_no', 'like', $needle)
                    ->orWhere('subject', 'like', $needle)
                    ->orWhere('description', 'like', $needle)
                    ->orWhereHas('creator', fn($cq) => $cq->where('user_fullname', 'like', $needle))
                    ->orWhereHas('location', fn($lq) => $lq->where('name', 'like', $needle))
                    ->orWhereHas('fromDepartment', fn($dq) => $dq->where('name', 'like', $needle))
                    ->orWhereHas('toDepartment', fn($dq) => $dq->where('name', 'like', $needle))
                    ->orWhereHas('category', fn($cq) => $cq->where('name', 'like', $needle))
                    ->orWhereHas('category.ticketCategory', fn($pq) => $pq->where('name', 'like', $needle))
                    ->orWhereHas('location.zone', fn($zq) => $zq->where('name', 'like', $needle));
            });
        }

        return $query;
    }

    protected function queryGrid(Request $request): Builder
    {
        $q = Ticket::query()->with([
            'location:id,name',
            'fromDepartment:id,name',
            'toDepartment:id,name',
            'category:id,name,sla_time,ticket_category_id',
            'category.ticketCategory:id,name',
            'creator:id,user_fullname',
            'statusUpdater:id,user_fullname',
        ]);

        return $this->applyFilters($q, $request, auth()->id());
    }

    private function getZonesAndLocations($admin)
    {
        $locations = null;
        $zones = null;

        if ($admin->access_limits == 1) {

            $zones = TblZonesModel::select('name', 'id')->get();
            $locations = TblLocationModel::select('name', 'id', 'zone_id')->get();
        } else if ($admin->access_limits == 2) {

            $zoneIds = [];

            if (!empty($admin->multi_location)) {

                $multiLocations = explode(',', $admin->multi_location);

                $locationsFromMulti = TblLocationModel::whereIn('id', $multiLocations)
                    ->pluck('zone_id')
                    ->unique()
                    ->toArray();

                $zoneIds = array_unique(array_merge([$admin->zone_id], $locationsFromMulti));

                $locations = TblLocationModel::select('name', 'id', 'zone_id')
                    ->where('zone_id', $admin->zone_id)
                    ->get();

                $specificLocations = TblLocationModel::select('name', 'id', 'zone_id')
                    ->whereIn('id', $multiLocations)
                    ->get();

                $locations = $locations->merge($specificLocations)->unique('id');
            } else {
                $locations = TblLocationModel::select('name', 'id', 'zone_id')
                    ->where('zone_id', $admin->zone_id)
                    ->get();

                $zoneIds = [$admin->zone_id];
            }

            $zones = TblZonesModel::select('name', 'id')
                ->whereIn('id', $zoneIds)
                ->get();
        } else {
            $branchIds = [];
            $branchIds[] = $admin->branch_id;

            if (!empty($admin->multi_location)) {

                $multiLocations = explode(',', $admin->multi_location);

                $branchIds = array_merge($branchIds, $multiLocations);

                $locations = TblLocationModel::select('name', 'id', 'zone_id')
                    ->whereIn('id', $branchIds)
                    ->get();
            } else {
                $locations = TblLocationModel::select('name', 'id', 'zone_id')
                    ->where('id', $admin->branch_id)
                    ->get();
            }

            $zoneIds = $locations->pluck('zone_id')->unique()->toArray();
            $zones = TblZonesModel::select('name', 'id')
                ->whereIn('id', $zoneIds)
                ->get();
        }

        return compact('zones', 'locations');
    }

    private function applyAccessFilter($query, $admin)
    {
        if ($admin->access_limits == 1 || $admin->access_limits == 4) {
            return $query;
        } elseif ($admin->access_limits == 2) {
            $branchIds = [];

            if (!empty($admin->zone_id)) {
                $zoneBranchIds = DB::table('tbl_locations')
                    ->where('zone_id', $admin->zone_id)
                    ->pluck('id')
                    ->toArray();

                $branchIds = array_merge($branchIds, $zoneBranchIds);
            }

            if (!empty($admin->multi_location)) {
                $multiLocationIds = array_map(
                    'intval',
                    explode(',', $admin->multi_location)
                );

                $branchIds = array_merge($branchIds, $multiLocationIds);
            }

            $branchIds = array_unique($branchIds);

            if (!empty($branchIds)) {
                $query->whereIn('location_id', $branchIds);
            }
        } elseif ($admin->access_limits == 3 || $admin->access_limits == 5) {
            $branchIds = [];

            if (!empty($admin->multi_location)) {
                $multiLocationIds = array_map(
                    'intval',
                    explode(',', $admin->multi_location)
                );

                $branchIds = array_merge($branchIds, $multiLocationIds);
            }

            $branchIds = array_unique($branchIds);

            if (!empty($branchIds)) {
                $query->whereIn('location_id', $branchIds);
            }
            $query->where('created_by', $admin->id);
        }

        return $query;
    }

    public function index(Request $request)
    {
        $admin = auth()->user();

        $data = $this->getZonesAndLocations($admin);

        return view('superadmin.tickets.index', [
            'admin' => $admin,
            'zones' => $data['zones'],
            'locations' => $data['locations'],
            'departments' => $this->departmentsForCurrentUser()->where('is_active', 1)->orderBy('name')->get(),
            'statuses' => Ticket::STATUSES,
            'priorities' => Ticket::PRIORITIES,
        ]);
    }

    public function categoriesByDepartment(Request $request)
    {
        $request->validate([
            'department_id' => 'required|integer',
            'ticket_category_id' => 'required|integer|exists:ticket_categories,id',
        ]);

        $departmentId = (int) $request->department_id;
        $ticketCategoryId = (int) $request->ticket_category_id;
        $allowedDept = $this->ticketDepartmentRestrictionIds();
        if ($allowedDept !== [] && ! in_array($departmentId, $allowedDept, true)) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot load categories for this department.',
            ], 403);
        }

        $rows = IssueCategory::query()
            ->where('department_id', $departmentId)
            ->where('ticket_category_id', $ticketCategoryId)
            ->where('is_active', 1)
            ->orderBy('name')
            ->get(['id', 'name']);

        return response()->json(['success' => true, 'categories' => $rows]);
    }

    public function listTicketCategoryParents(Request $request)
    {
        $rows = TicketCategory::query()->orderBy('name')->get(['id', 'name']);

        return response()->json(['success' => true, 'ticket_categories' => $rows]);
    }

    public function data(Request $request)
    {
        $admin = auth()->user();
        $userId = (int) auth()->id();

        $statsQuery = Ticket::query();

        $this->applyAccessFilter($statsQuery, $admin);

        $this->applyFilters($statsQuery, $request, $userId);

        $countsByStatus = $statsQuery
            ->selectRaw('status, COUNT(*) as c')
            ->groupBy('status')
            ->pluck('c', 'status');

        $byStatus = [];
        foreach (Ticket::STATUSES as $st) {
            $byStatus[$st] = (int) ($countsByStatus[$st] ?? 0);
        }

        $statsTotal = array_sum($byStatus);

        $q = $this->queryGrid($request);

        $this->applyAccessFilter($q, $admin);

        $page = max(1, (int) $request->input('page', 1));
        $perPage = (int) $request->input('per_page', 15);
        $perPage = min(100, max(5, $perPage));

        $listBase = clone $q;

        $paginator = (clone $listBase)
            ->orderByDesc('id')
            ->paginate($perPage, ['*'], 'page', $page);

        if ($paginator->total() > 0 && $paginator->isEmpty()) {
            $paginator = (clone $listBase)->orderByDesc('id')->paginate(
                $perPage,
                ['*'],
                'page',
                $paginator->lastPage()
            );
        }

        $mapped = $paginator->getCollection()->map(function (Ticket $t) use ($userId) {

            $access = $this->ticketAccessFromAuth();
            $slaSummary = $t->slaVersusActualSummary();

            return [
                'id' => $t->id,
                'ticket_no' => $t->ticket_no,
                'location_id' => $t->location_id,
                'from_department_id' => $t->from_department_id,
                'to_department_id' => $t->to_department_id,
                'issue_category_id' => $t->issue_category_id,
                'ticket_category_id' => $t->category?->ticket_category_id,
                'ticket_category_name' => $t->category?->ticketCategory?->name ?? '',
                'location_name' => $t->location->name ?? '',
                'from_department_name' => $t->fromDepartment->name ?? '',
                'to_department_name' => $t->toDepartment->name ?? '',
                'category_name' => $t->category->name ?? '',
                'priority' => $t->priority,
                'subject' => $t->subject,
                'description' => $t->description,
                'status' => $t->status,
                'attachments' => $t->attachments ?? [],
                'created_by_name' => $t->creator->user_fullname ?? '',
                'created_at' => $this->fmtAt($t->created_at),
                'status_updated_by_name' => $t->statusUpdater->user_fullname ?? '',
                'status_updated_at' => $this->fmtAt($t->status_updated_at),
                'time_to_close' => $t->timeToCloseDisplay(),
                'sla_vs_actual' => $slaSummary['text'],
                'sla_vs_actual_kind' => $slaSummary['kind'],
                'closed_status_note' => $t->closedStatusNote(),
                'is_creator' => (int) $t->created_by === $userId,
                'can_update_status' => $access && $this->ticketAccessIsOpen($access),
                'can_edit' => $this->canEditTicket($t),
            ];
        });

        $paginator->setCollection($mapped);

        return response()->json([
            'success' => true,
            'tickets' => $paginator->items(),
            'stats' => [
                'total' => $statsTotal,
                'by_status' => $byStatus,
            ],
            'pagination' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'from' => $paginator->firstItem(),
                'to' => $paginator->lastItem(),
            ],
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'location_id' => 'required|integer|exists:tbl_locations,id',
            'from_department_id' => 'required|integer|exists:departments,id',
            'to_department_id' => 'required|integer|exists:departments,id',
            'issue_category_id' => 'required|integer|exists:issue_categories,id',
            'priority' => 'required|string|in:' . implode(',', Ticket::PRIORITIES),
            'description' => 'required|string|max:10000',
            'attachments' => 'required|array|min:1',
            'attachments.*' => 'file|mimes:jpeg,png,jpg,gif,webp,pdf,doc,docx,xls,xlsx|max:10240',
        ], [
            'attachments.required' => 'Please upload at least one attachment.',
            'attachments.min' => 'Please upload at least one attachment.',
        ]);

        $category = IssueCategory::query()
            ->where('id', $validated['issue_category_id'])
            ->where('department_id', $validated['to_department_id'])
            ->where('is_active', 1)
            ->first();

        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Selected issue category must belong to the destination department and be active.',
            ], 422);
        }

        $fromDept = Department::where('id', $validated['from_department_id'])->where('is_active', 1)->first();
        $toDept = Department::where('id', $validated['to_department_id'])->where('is_active', 1)->first();
        if (!$fromDept || !$toDept) {
            return response()->json(['success' => false, 'message' => 'Invalid or inactive department.'], 422);
        }

        $locDenied = $this->accessControl(null, (int) $validated['location_id'])['error'] ?? null;

        if ($locDenied) {
            return $locDenied;
        }

        if ($denied = $this->assertDepartmentsAllowedForUser((int) $validated['from_department_id'], (int) $validated['to_department_id'])) {
            return $denied;
        }

        $paths = $this->uploadAttachments($request->file('attachments'));
        $lastTicket = Ticket::orderBy('id', 'desc')->value('ticket_no');

        if ($lastTicket && preg_match('/TKT-(\d+)/', $lastTicket, $matches)) {
            $nextNumber = (int) $matches[1] + 1;
        } else {
            $nextNumber = 1;
        }

        $ticketNo = 'TKT-' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);
        $ticket = Ticket::create([
            'ticket_no' => $ticketNo,
            'location_id' => $validated['location_id'],
            'from_department_id' => $validated['from_department_id'],
            'to_department_id' => $validated['to_department_id'],
            'issue_category_id' => $validated['issue_category_id'],
            'priority' => $validated['priority'],
            'description' => $validated['description'],
            'attachments' => $paths,
            'status' => 'open',
            'created_by' => auth()->id(),
        ]);

        $uid = (int) auth()->id();
        $creatorName = (string) (usermanagementdetails::query()->where('id', $uid)->value('user_fullname') ?? '—');
        $ticket->solution = [
            [
                'from_status' => null,
                'to_status' => 'open',
                'user_id' => $uid,
                'user_name' => $creatorName,
                'updated_at' => now()->toIso8601String(),
                'note' => null,
            ],
        ];
        $ticket->save();

        return response()->json([
            'success' => true,
            'message' => 'Ticket raised successfully.',
            'ticket' => ['id' => $ticket->id, 'ticket_no' => $ticket->ticket_no],
        ]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'id' => 'required|integer|exists:tickets,id',
            'location_id' => 'required|integer|exists:tbl_locations,id',
            'from_department_id' => 'required|integer|exists:departments,id',
            'to_department_id' => 'required|integer|exists:departments,id',
            'issue_category_id' => 'required|integer|exists:issue_categories,id',
            'priority' => 'required|string|in:' . implode(',', Ticket::PRIORITIES),
            'description' => 'required|string|max:10000',
            'attachments.*' => 'nullable|file|mimes:jpeg,png,jpg,gif,webp,pdf,doc,docx,xls,xlsx|max:10240',
        ]);

        $ticket = Ticket::findOrFail($validated['id']);

        if (!$this->canEditTicket($ticket)) {
            return response()->json([
                'success' => false,
                'message' => 'This ticket cannot be edited once it is no longer open (e.g. after it moves to In progress).',
            ], 403);
        }

        $locDenied = $this->accessControl(null, (int) $validated['location_id'])['error'] ?? null;

        if ($locDenied) {
            return $locDenied;
        }

        if ($denied = $this->assertDepartmentsAllowedForUser((int) $validated['from_department_id'], (int) $validated['to_department_id'])) {
            return $denied;
        }

        $category = IssueCategory::query()
            ->where('id', $validated['issue_category_id'])
            ->where('department_id', $validated['to_department_id'])
            ->where('is_active', 1)
            ->first();

        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Selected issue category must belong to the destination department and be active.',
            ], 422);
        }

        $fromDept = Department::where('id', $validated['from_department_id'])->where('is_active', 1)->first();
        $toDept = Department::where('id', $validated['to_department_id'])->where('is_active', 1)->first();
        if (!$fromDept || !$toDept) {
            return response()->json(['success' => false, 'message' => 'Invalid or inactive department.'], 422);
        }

        $existing = $ticket->attachments ?? [];
        if (!is_array($existing)) {
            $existing = [];
        }
        $existing = array_values(array_filter($existing, static fn($p) => is_string($p) && $p !== ''));

        $newPaths = $this->uploadAttachments($request->file('attachments'));

        if ($request->has('keep_attachments_json')) {
            $decoded = json_decode((string) $request->input('keep_attachments_json'), true);
            if (!is_array($decoded)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid attachment keep list.',
                ], 422);
            }
            $kept = [];
            foreach ($decoded as $p) {
                if (!is_string($p) || $p === '' || str_contains($p, '..')) {
                    continue;
                }
                if (in_array($p, $existing, true)) {
                    $kept[] = $p;
                }
            }
            $kept = array_values(array_unique($kept));
            $merged = array_merge($kept, $newPaths);
        } else {
            $merged = array_merge($existing, $newPaths);
        }

        $ticket->location_id = $validated['location_id'];
        $ticket->from_department_id = $validated['from_department_id'];
        $ticket->to_department_id = $validated['to_department_id'];
        $ticket->issue_category_id = $validated['issue_category_id'];
        $ticket->priority = $validated['priority'];
        $ticket->description = $validated['description'];
        $ticket->attachments = array_values($merged);
        $ticket->save();

        return response()->json([
            'success' => true,
            'message' => 'Ticket updated successfully.',
        ]);
    }

    public function updateStatus(Request $request)
    {
        $request->validate([
            'id' => 'required|integer|exists:tickets,id',
            'status' => 'required|string|in:' . implode(',', Ticket::STATUSES),
            'note' => 'nullable|string|max:2000',
        ]);

        if ($request->input('status') === 'closed') {
            $noteTrim = trim((string) ($request->input('note') ?? ''));
            if ($noteTrim === '') {
                throw ValidationException::withMessages([
                    'note' => ['Solution is required when closing the ticket.'],
                ]);
            }
        }

        $ticket = Ticket::findOrFail($request->id);

        $access = $this->ticketAccessFromAuth();

        if (!$access || !$this->ticketAccessIsOpen($access)) {
            return response()->json([
                'success' => false,
                'message' => 'You are not allowed to update status for this ticket.'
            ], 403);
        }

        if (! $this->ticketAccessAllows($access, $ticket)) {
            return response()->json([
                'success' => false,
                'message' => 'You are not allowed to update status for this ticket.',
            ], 403);
        }

        $from = $ticket->status;
        if ($from === $request->status) {
            return response()->json([
                'success' => false,
                'message' => 'This status is already selected. Choose another status to record a change.',
            ], 422);
        }

        $note = $request->input('note');
        $note = is_string($note) ? trim($note) : null;
        if ($note === '') {
            $note = null;
        }

        $uid = (int) auth()->id();
        $updaterName = (string) (usermanagementdetails::query()->where('id', $uid)->value('user_fullname') ?? '—');
        $entries = $ticket->solution ?? [];
        if (!is_array($entries)) {
            $entries = [];
        }
        $entries[] = [
            'from_status' => $from,
            'to_status' => $request->status,
            'user_id' => $uid,
            'user_name' => $updaterName,
            'updated_at' => now()->toIso8601String(),
            'note' => $note,
        ];

        $ticket->status = $request->status;
        $ticket->status_updated_by = $uid;
        $ticket->status_updated_at = now();
        $ticket->solution = $entries;
        $ticket->save();

        $ticket->load(['statusUpdater:id,user_fullname', 'category:id,name,sla_time,ticket_category_id', 'category.ticketCategory:id,name']);
        $slaSummary = $ticket->slaVersusActualSummary();

        return response()->json([
            'success' => true,
            'message' => 'Status updated Successfully!',
            'ticket' => [
                'status' => $ticket->status,
                'status_updated_by_name' => $ticket->statusUpdater->user_fullname ?? '',
                'status_updated_at' => $this->fmtAt($ticket->status_updated_at),
                'time_to_close' => $ticket->timeToCloseDisplay(),
                'sla_vs_actual' => $slaSummary['text'],
                'sla_vs_actual_kind' => $slaSummary['kind'],
                'closed_status_note' => $ticket->closedStatusNote(),
            ],
        ]);
    }

    public function export(Request $request)
    {
        $tickets = $this->queryGrid($request)->orderByDesc('id')->get();
        $filename = 'tickets_' . now()->format('Y_m_d_His') . '.xlsx';

        return Excel::download(new TicketExport($tickets), $filename);
    }

    protected function filePath($name)
    {
        $path = public_path('ticket_attachments/' . basename($name));
        return file_exists($path) ? $path : null;
    }

    public function viewAttachment(Request $request)
    {
        $name = $request->query('f');
        $path = $this->filePath($name);

        if (!$path) abort(404);

        $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));

        if (in_array($ext, ['doc', 'docx', 'xls', 'xlsx'])) {
            return response()->download($path, basename($name));
        }

        return response()->file($path);
    }

    public function timeline(Ticket $ticket)
    {
        if (isset($this->accessControl($ticket)['error'])) {
            abort(403, 'You cannot view this ticket timeline.');
        }


        $solution = $ticket->solution ?? [];
        if (!is_array($solution)) {
            $solution = [];
        }

        $userIds = collect($solution)->pluck('user_id')
            ->filter()
            ->unique();
        $names = $this->namesForUsers($userIds);

        $items = [];

        if ($solution === []) {
            $ticket->loadMissing('creator:id,user_fullname');
            $items[] = [
                'type' => 'status',
                'synthetic' => true,
                'created_at' => $this->fmtAt($ticket->created_at),
                'created_at_iso' => $ticket->created_at?->toIso8601String(),
                'user_name' => $ticket->creator->user_fullname ?? '—',
                'from_status' => null,
                'to_status' => 'open',
                'note' => null,
                'summary' => 'Ticket raised',
            ];
        } else {
            foreach ($solution as $idx => $e) {
                if (!is_array($e)) {
                    continue;
                }
                $iso = (string) ($e['updated_at'] ?? '');
                $at = $iso !== '' ? \Illuminate\Support\Carbon::parse($iso) : null;
                $uid = isset($e['user_id']) ? (int) $e['user_id'] : 0;
                $userName = (string) ($e['user_name'] ?? ($uid > 0 ? ($names[$uid] ?? '—') : '—'));
                $items[] = [
                    'type' => 'status',
                    'id' => $idx,
                    'created_at' => $at ? $this->fmtAt($at) : '',
                    'created_at_iso' => $iso,
                    'user_name' => $userName,
                    'from_status' => $e['from_status'] ?? null,
                    'to_status' => $e['to_status'] ?? 'open',
                    'note' => $e['note'] ?? null,
                ];
            }
        }

        $commentRows = EntityComment::query()
            ->where('commentable_type', Ticket::class)
            ->where('commentable_id', $ticket->id)
            ->orderBy('id')
            ->get(['id', 'body', 'user_id', 'created_at']);

        $commentUserIds = $commentRows->pluck('user_id')->filter()->unique();
        $commentNames = $this->namesForUsers($commentUserIds);

        foreach ($commentRows as $row) {
            $at = $row->created_at;
            $uid = (int) ($row->user_id ?? 0);
            $userName = $uid > 0 ? (string) ($commentNames[$uid] ?? '—') : '—';
            $iso = $at ? $at->toIso8601String() : '';
            $items[] = [
                'type' => 'comment',
                'id' => (int) $row->id,
                'created_at' => $at ? $this->fmtAt($at) : '',
                'created_at_iso' => $iso,
                'user_name' => $userName,
                'body' => (string) $row->body,
            ];
        }

        usort($items, function (array $a, array $b) {
            return strcmp((string) ($a['created_at_iso'] ?? ''), (string) ($b['created_at_iso'] ?? ''));
        });

        return response()->json([
            'success' => true,
            'items' => array_values($items),
        ]);
    }

    public function storeComment(Request $request, Ticket $ticket): JsonResponse
    {
        $validated = $request->validate([
            'body' => ['required', 'string', 'max:5000'],
        ]);

        $access = $this->ticketAccessFromAuth();
        if (! $access) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated.'], 401);
        }

        if (! $this->ticketAccessAllows($access, $ticket)) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot add comments on this ticket.',
            ], 403);
        }

        EntityComment::query()->create([
            'commentable_type' => Ticket::class,
            'commentable_id' => $ticket->id,
            'body' => $validated['body'],
            'user_id' => auth()->id(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Comment added.',
        ]);
    }

    protected function ticketAccessFromAuth(): ?object
    {
        $id = auth()->id();
        if (!$id) {
            return null;
        }
        $row = DB::table('users')->where('id', $id)->first();

        return $row ? $row : null;
    }

    protected function ticketAccessLevel(object $user): int
    {
        return (int) ($user->access_limits ?? 0);
    }

    protected function ticketAccessIsOpen(object $user): bool
    {
        return in_array($this->ticketAccessLevel($user), self::OPEN, true);
    }

    protected function ticketAccessAllowedLocationIds(object $user): ?array
    {
        if ($this->ticketAccessIsOpen($user)) {
            return null;
        }

        $lv = $this->ticketAccessLevel($user);
        if ($lv === 2) {

            $zoneIds = array_values(array_filter([(int) ($user->zone_id ?? 0)]));

            $multi = [];
            if (!empty($user->multi_location)) {
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

        if (in_array($lv, self::OWN_ROWS, true)) {

            $ids = array_values(array_filter([(int) ($user->branch_id ?? 0)]));

            if (!empty($user->multi_location)) {
                $multi = array_map('intval', explode(',', $user->multi_location));
                $ids = array_merge($ids, $multi);
            }

            return array_values(array_unique($ids));
        }

        return [];
    }

    protected function ticketAccessApplyScope(object $user, Builder $q): void
    {
        $ids = $this->ticketAccessAllowedLocationIds($user);
        if ($ids !== null) {
            if ($ids === []) {
                $q->whereRaw('0 = 1');

                return;
            }
            $q->whereIn('location_id', $ids);
            if (in_array($this->ticketAccessLevel($user), self::OWN_ROWS, true)) {
                $q->where('created_by', (int) $user->id);
            }
        }

        // Always apply department_user limits when the user is assigned to departments
        // (including users with "open" location access — they must not see other departments' tickets).
        $deptIds = $this->ticketDepartmentRestrictionIds((int) $user->id);
        if ($deptIds !== []) {
            $q->where(function (Builder $sub) use ($deptIds) {
                $sub->whereIn('to_department_id', $deptIds)
                    ->orWhereIn('from_department_id', $deptIds);
            });
        }
    }

    protected function ticketAccessAllows(object $user, Ticket $ticket): bool
    {
        $ids = $this->ticketAccessAllowedLocationIds($user);
        if ($ids === null) {
            $locationOk = true;
        } elseif ($ids === [] || ! in_array((int) $ticket->location_id, $ids, true)) {
            $locationOk = false;
        } else {
            $locationOk = true;
        }

        if (! $locationOk) {
            return false;
        }

        if (in_array($this->ticketAccessLevel($user), self::OWN_ROWS, true)) {
            if ((int) $ticket->created_by !== (int) $user->id) {
                return false;
            }
        }

        $deptIds = $this->ticketDepartmentRestrictionIds((int) $user->id);
        if ($deptIds === []) {
            return true;
        }

        $to = (int) $ticket->to_department_id;
        $from = (int) $ticket->from_department_id;

        return in_array($to, $deptIds, true)
            || in_array($from, $deptIds, true);
    }

    protected function ticketAccessLocationError(object $user, int $locationId): ?JsonResponse
    {
        $ids = $this->ticketAccessAllowedLocationIds($user);
        if ($ids === null) {
            return null;
        }
        if ($ids === [] || ! in_array($locationId, $ids, true)) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot use this branch/location for tickets.',
            ], 422);
        }

        return null;
    }

    private function fmtAt($value): string
    {
        if ($value === null) {
            return '';
        }

        return \Illuminate\Support\Carbon::parse($value)->format('d M Y, g:i A');
    }

    protected function uploadAttachments($files): array
    {
        $paths = [];
        if (!$files) return $paths;

        $uploadPath = public_path('ticket_attachments');

        if (!File::isDirectory($uploadPath)) {
            File::makeDirectory($uploadPath, 0755, true);
        }

        foreach ($files as $file) {
            if (!$file) continue;

            $name = time() . '_' . preg_replace('/\s+/', '_', $file->getClientOriginalName());
            $file->move($uploadPath, $name);
            $paths[] = 'ticket_attachments/' . $name;
        }

        return $paths;
    }
}
