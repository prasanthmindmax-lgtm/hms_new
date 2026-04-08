<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Ticket;
use App\Models\TblLocationModel;
use App\Models\TicketCategory;
use App\Models\usermanagementdetails;
use App\Exports\TicketExport;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\File;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class TicketController extends Controller
{
    public function getDepartments(Request $request)
    {
        $admin   = auth()->user();
        $perPage = $request->get('per_page', 10);

        $departments = Department::query()
            ->orderBy('id', 'asc')
            ->paginate($perPage)
            ->appends(['per_page' => $perPage]);

        return view('superadmin.tickets.departments', [
            'admin'       => $admin,
            'departments' => $departments,
            'perPage'     => $perPage,
        ]);
    }

    public function storeDepartments(Request $request)
    {
        $request->validate([
            'name'      => 'required|string|max:255',
            'is_active' => ['required', Rule::in([0, 1])],
        ]);

        $id = $request->id;

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

        return response()->json([
            'success'  => true,
            'message'  => $message,
            'departments' => $departments,
        ]);
    }

    public function getTicketCategories(Request $request)
    {
        $admin   = auth()->user();
        $perPage = $request->get('per_page', 10);
        $departments = Department::orderBy('id', 'asc')->get();

        $ticketCategories = TicketCategory::orderBy('id', 'asc')->paginate($perPage)
            ->appends(['per_page' => $perPage]);

        return view('superadmin.tickets.categories', [
            'admin'            => $admin,
            'departments'       => $departments,
            'ticketCategories' => $ticketCategories,
            'perPage'          => $perPage,
        ]);
    }

    public function storeTicketCategories(Request $request)
    {
        $slaRaw = trim((string) $request->input('sla_time', ''));
        if ($slaRaw !== '' && preg_match('/^(\d{1,2}):(\d{2})(?::(\d{2}))?$/', $slaRaw, $m)) {
            $h = min(23, max(0, (int) $m[1]));
            $i = min(59, max(0, (int) $m[2]));
            $request->merge(['sla_time' => sprintf('%02d:%02d', $h, $i)]);
        }

        $request->validate([
            'department_id' => 'required',
            'name'      => 'required|string|max:255',
            'sla_time'  => ['required', 'regex:/^(?:[01]\d|2[0-3]):[0-5]\d$/'],
            'is_active' => ['required', Rule::in([0, 1])],
        ]);

        $id = $request->id;

        $data = [
            'department_id' => $request->department_id,
            'name'       => $request->name,
            'sla_time'   => $request->sla_time,
            'description' => $request->description,
            'is_active'  => $request->is_active,
            'created_by' => auth()->id(),
        ];

        if (!empty($id)) {
            TicketCategory::where('id', $id)->update($data);
            $ticketCategories = TicketCategory::find($id);
            $message = 'Ticket Category updated successfully!';
        } else {
            $ticketCategories = TicketCategory::create($data);
            $message = 'Ticket Category created successfully!';
        }

        return response()->json([
            'success'  => true,
            'message'  => $message,
            'ticketCategories' => $ticketCategories,
        ]);
    }

    protected function currentUserRoleId(): ?int
    {
        $id = auth()->id();
        if (!$id) {
            return null;
        }

        return (int) DB::table('users')->where('id', $id)->value('access_limits');
    }

    protected function ticketFullAccessLimits(): array
    {
        return [1, 2, 3, 6];
    }

    protected function currentUserHasFullTicketAccess(): bool
    {
        $limit = $this->currentUserRoleId();

        return in_array($limit, $this->ticketFullAccessLimits(), true);
    }

    /** Non-elevated users only see tickets they raised. */
    protected function applyTicketOwnershipScopeUnlessElevated(Builder $q): void
    {
        if ($this->currentUserHasFullTicketAccess()) {
            return;
        }
        $uid = (int) auth()->id();
        if ($uid > 0) {
            $q->where('created_by', $uid);
        }
    }

    protected function canUpdateTicketStatus(Ticket $ticket): bool
    {
        return $this->currentUserHasFullTicketAccess();
    }

    /**
     * Creators may edit only while the ticket is still open
     */
    protected function canEditTicket(Ticket $ticket): bool
    {
        if ($ticket->status !== 'open') {
            return false;
        }
        $userId = (int) auth()->id();
        if ($userId <= 0) {
            return false;
        }

        return (int) $ticket->created_by === $userId;
    }

    protected function ticketUserNames(iterable $userIds): array
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

    protected function applyTicketScope(Builder $query, Request $request, int $userId): void
    {
        $scope = $request->get('scope', 'all');
        if ($scope === 'mine') {
            $query->where('created_by', $userId);
        } elseif ($scope === 'department') {
            $deptIds = DB::table('department_user')->where('user_id', $userId)->pluck('department_id');
            $query->whereIn('to_department_id', $deptIds);
        }
    }

    /**
     * Active department IDs from filter (comma-separated or repeated params).
     *
     * @return int[]
     */
    protected function requestedDepartmentIds(Request $request): array
    {
        $raw = $request->input('to_department_id');
        if ($raw === null || $raw === '') {
            return [];
        }
        $parts = is_array($raw) ? $raw : explode(',', (string) $raw);
        $ids = [];
        foreach ($parts as $p) {
            $p = trim((string) $p);
            if ($p === '') {
                continue;
            }
            $n = (int) $p;
            if ($n > 0) {
                $ids[] = $n;
            }
        }
        $ids = array_values(array_unique($ids));
        if ($ids === []) {
            return [];
        }

        return Department::query()
            ->where('is_active', 1)
            ->whereIn('id', $ids)
            ->pluck('id')
            ->map(fn($id) => (int) $id)
            ->all();
    }

    /**
     * Status slugs from filter (comma-separated or array), validated against Ticket::STATUSES.
     *
     * @return string[]
     */
    protected function requestedStatuses(Request $request): array
    {
        $raw = $request->input('status');
        if ($raw === null || $raw === '') {
            return [];
        }
        $parts = is_array($raw) ? $raw : explode(',', (string) $raw);
        $allowed = array_flip(Ticket::STATUSES);
        $out = [];
        foreach ($parts as $p) {
            $p = trim((string) $p);
            if ($p !== '' && isset($allowed[$p])) {
                $out[] = $p;
            }
        }

        return array_values(array_unique($out));
    }

    protected function applyTicketDepartmentFilter(Builder $query, Request $request): void
    {
        $ids = $this->requestedDepartmentIds($request);
        if ($ids !== []) {
            $query->whereIn('to_department_id', $ids);
        }
    }

    protected function applyTicketStatusMultiFilter(Builder $query, Request $request): void
    {
        $statuses = $this->requestedStatuses($request);
        if ($statuses !== []) {
            $query->whereIn('status', $statuses);
        }
    }

    protected function applyTicketListFilters(Builder $query, Request $request): void
    {
        $dateFrom = $request->input('date_from');
        if (is_string($dateFrom) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateFrom)) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }
        $dateTo = $request->input('date_to');
        if (is_string($dateTo) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateTo)) {
            $query->whereDate('created_at', '<=', $dateTo);
        }
        $this->applyTicketDepartmentFilter($query, $request);
        $qText = trim((string) $request->input('universal_search', ''));
        if ($qText === '' && $request->filled('raised_by')) {
            $qText = trim((string) $request->input('raised_by', ''));
        }
        if ($qText !== '') {
            $needle = '%' . addcslashes($qText, '%_\\') . '%';
            $query->where(function ($sub) use ($needle) {
                $sub->where('ticket_no', 'like', $needle)
                    ->orWhere('subject', 'like', $needle)
                    ->orWhere('description', 'like', $needle)
                    ->orWhereHas('creator', function ($cq) use ($needle) {
                        $cq->where('user_fullname', 'like', $needle);
                    })
                    ->orWhereHas('location', function ($lq) use ($needle) {
                        $lq->where('name', 'like', $needle);
                    })
                    ->orWhereHas('fromDepartment', function ($dq) use ($needle) {
                        $dq->where('name', 'like', $needle);
                    })
                    ->orWhereHas('toDepartment', function ($dq) use ($needle) {
                        $dq->where('name', 'like', $needle);
                    })
                    ->orWhereHas('category', function ($cq) use ($needle) {
                        $cq->where('name', 'like', $needle);
                    });
            });
        }
    }

    protected function ticketListQueryForGrid(Request $request): Builder
    {
        $q = Ticket::query()
            ->with([
                'location:id,name',
                'fromDepartment:id,name',
                'toDepartment:id,name',
                'category:id,name,sla_time',
                'creator:id,user_fullname',
                'statusUpdater:id,user_fullname',
            ]);

        $this->applyTicketOwnershipScopeUnlessElevated($q);

        $this->applyTicketListFilters($q, $request);
        $this->applyTicketStatusMultiFilter($q, $request);

        return $q;
    }

    public function index(Request $request)
    {
        $admin = auth()->user();
        $locations = TblLocationModel::orderBy('name')->get();
        $departments = Department::where('is_active', 1)->orderBy('name')->get();

        return view('superadmin.tickets.index', [
            'admin' => $admin,
            'locations' => $locations,
            'departments' => $departments,
            'statuses' => Ticket::STATUSES,
            'priorities' => Ticket::PRIORITIES,
        ]);
    }

    public function categoriesByDepartment(Request $request)
    {
        $request->validate([
            'department_id' => 'required|integer',
        ]);

        $rows = TicketCategory::query()
            ->where('department_id', $request->department_id)
            ->where('is_active', 1)
            ->orderBy('name')
            ->get(['id', 'name']);

        return response()->json(['success' => true, 'categories' => $rows]);
    }

    public function data(Request $request)
    {
        $userId = (int) auth()->id();

        $statsQuery = Ticket::query();
        $this->applyTicketScope($statsQuery, $request, $userId);
        $this->applyTicketOwnershipScopeUnlessElevated($statsQuery);
        $this->applyTicketListFilters($statsQuery, $request);
        $this->applyTicketStatusMultiFilter($statsQuery, $request);
        $countsByStatus = $statsQuery
            ->selectRaw('status, COUNT(*) as c')
            ->groupBy('status')
            ->pluck('c', 'status');

        $byStatus = [];
        foreach (Ticket::STATUSES as $st) {
            $byStatus[$st] = (int) ($countsByStatus[$st] ?? 0);
        }
        $statsTotal = array_sum($byStatus);

        $q = $this->ticketListQueryForGrid($request);

        $page = max(1, (int) $request->input('page', 1));
        $perPage = (int) $request->input('per_page', 15);
        $perPage = min(100, max(5, $perPage));

        $listBase = clone $q;
        $paginator = (clone $listBase)->orderByDesc('id')->paginate($perPage, ['*'], 'page', $page);
        if ($paginator->total() > 0 && $paginator->isEmpty()) {
            $paginator = (clone $listBase)->orderByDesc('id')->paginate(
                $perPage,
                ['*'],
                'page',
                $paginator->lastPage()
            );
        }

        $mapped = $paginator->getCollection()->map(function (Ticket $t) use ($userId) {
            $canStatus = $this->canUpdateTicketStatus($t);
            $slaSummary = $t->slaVersusActualSummary();

            return [
                'id' => $t->id,
                'ticket_no' => $t->ticket_no,
                'location_id' => $t->location_id,
                'from_department_id' => $t->from_department_id,
                'to_department_id' => $t->to_department_id,
                'ticket_category_id' => $t->ticket_category_id,
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
                'created_at' => $this->formatTicketDateTimeForDisplay($t->created_at),
                'status_updated_by_name' => $t->statusUpdater->user_fullname ?? '',
                'status_updated_at' => $this->formatTicketDateTimeForDisplay($t->status_updated_at),
                'time_to_close' => $t->timeToCloseDisplay(),
                'sla_vs_actual' => $slaSummary['text'],
                'sla_vs_actual_kind' => $slaSummary['kind'],
                'closed_status_note' => $t->closedStatusNote(),
                'is_creator' => (int) $t->created_by === $userId,
                'can_update_status' => $canStatus,
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

    public function export(Request $request)
    {
        $tickets = $this->ticketListQueryForGrid($request)->orderByDesc('id')->get();
        $filename = 'tickets_' . now()->format('Y_m_d_His') . '.xlsx';

        return Excel::download(new TicketExport($tickets), $filename);
    }

    /**
     * Resolve stored filename to a real path under public/ticket_attachments, or null if invalid.
     */
    protected function resolveTicketAttachmentPath(string $name): ?string
    {
        $name = basename($name);
        if ($name === '' || preg_match('/[\x00-\x1f\x7f\\\\\/]/', $name)) {
            return null;
        }

        $baseDir = realpath(public_path('ticket_attachments'));
        if ($baseDir === false) {
            return null;
        }

        $fullPath = public_path('ticket_attachments') . DIRECTORY_SEPARATOR . $name;
        $realPath = realpath($fullPath);
        if ($realPath === false || ! is_file($realPath)) {
            return null;
        }

        if (! str_starts_with($realPath, $baseDir . DIRECTORY_SEPARATOR)) {
            return null;
        }

        return $realPath;
    }

    protected function guessAttachmentMimeType(string $realPath): string
    {
        $ext = strtolower(pathinfo($realPath, PATHINFO_EXTENSION));
        $map = [
            'pdf' => 'application/pdf',
            'png' => 'image/png',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'gif' => 'image/gif',
            'webp' => 'image/webp',
            'doc' => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'xls' => 'application/vnd.ms-excel',
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ];
        if (isset($map[$ext])) {
            return $map[$ext];
        }
        $detected = @mime_content_type($realPath);

        return is_string($detected) && $detected !== '' ? $detected : 'application/octet-stream';
    }

    /**
     * Stream with Content-Disposition: inline + explicit Content-Type (new tab / in-browser where supported).
     */
    protected function streamTicketAttachment(string $name): BinaryFileResponse
    {
        $realPath = $this->resolveTicketAttachmentPath($name);
        if ($realPath === null) {
            abort(404);
        }

        $safeName = basename($realPath);
        $response = response()->file($realPath);
        $response->headers->set('Content-Type', $this->guessAttachmentMimeType($realPath));
        $response->setContentDisposition('inline', $safeName);

        return $response;
    }

    /**
     * Streams an attachment, returns Office viewer JSON (?office=1), or serves a signed URL (no session) for Office Online.
     */
    public function viewAttachment(Request $request): BinaryFileResponse|JsonResponse|RedirectResponse
    {
        if ($request->hasValidSignature()) {
            $request->validate([
                'f' => 'required|string|max:512',
            ]);

            return $this->streamTicketAttachment((string) $request->query('f'));
        }

        $redirect = $this->requireSuperadminRoleForTicketAttachment($request);
        if ($redirect !== null) {
            return $redirect;
        }

        if ($request->query('office') === '1') {
            return $this->officeAttachmentViewerPayload($request);
        }

        return $this->streamTicketAttachment((string) $request->query('f', ''));
    }

    protected function requireSuperadminRoleForTicketAttachment(Request $request): ?RedirectResponse
    {
        if (! auth()->check()) {
            return redirect('login');
        }

        if ((int) auth()->user()->role_id !== 1) {
            Auth::logout();
            $request->session()->invalidate();

            return redirect('login');
        }

        return null;
    }

    /**
     * JSON for Word/Excel: Office Online viewer URL when app URL is public HTTPS; otherwise use_direct.
     */
    protected function officeAttachmentViewerPayload(Request $request): JsonResponse
    {
        $request->validate([
            'f' => 'required|string|max:512',
        ]);

        $name = basename($request->query('f'));
        if ($this->resolveTicketAttachmentPath($name) === null) {
            abort(404);
        }

        $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
        if (! in_array($ext, ['doc', 'docx', 'xls', 'xlsx'], true)) {
            return response()->json([
                'viewer_url' => null,
                'use_direct' => true,
            ]);
        }

        if (! $this->appBaseUrlEligibleForOfficeOnlineViewer()) {
            return response()->json([
                'viewer_url' => null,
                'use_direct' => true,
            ]);
        }

        $signedUrl = URL::temporarySignedRoute(
            'superadmin.tickets.attachment',
            now()->addMinutes(20),
            ['f' => $name]
        );

        $viewerUrl = 'https://view.officeapps.live.com/op/view.aspx?src=' . rawurlencode($signedUrl);

        return response()->json([
            'viewer_url' => $viewerUrl,
            'use_direct' => false,
        ]);
    }

    /**
     * Office Online must fetch the file from the public internet over HTTPS (not localhost / LAN-only hosts).
     */
    protected function appBaseUrlEligibleForOfficeOnlineViewer(): bool
    {
        $url = (string) config('app.url');
        if (! str_starts_with($url, 'https://')) {
            return false;
        }
        $host = strtolower((string) (parse_url($url, PHP_URL_HOST) ?: ''));
        if ($host === '' || $host === 'localhost') {
            return false;
        }
        if (str_ends_with($host, '.local') || str_ends_with($host, '.test')) {
            return false;
        }
        if (str_starts_with($host, '127.') || str_starts_with($host, '192.168.') || str_starts_with($host, '10.')) {
            return false;
        }

        return true;
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'location_id' => 'required|integer|exists:tbl_locations,id',
            'from_department_id' => 'required|integer|exists:departments,id',
            'to_department_id' => 'required|integer|exists:departments,id',
            'ticket_category_id' => 'required|integer|exists:ticket_categories,id',
            'priority' => 'required|string|in:' . implode(',', Ticket::PRIORITIES),
            'subject' => 'required|string|max:500',
            'description' => 'required|string|max:10000',
            'attachments.*' => 'nullable|file|mimes:jpeg,png,jpg,gif,webp,pdf,doc,docx,xls,xlsx|max:10240',
        ]);

        $category = TicketCategory::query()
            ->where('id', $validated['ticket_category_id'])
            ->where('department_id', $validated['to_department_id'])
            ->where('is_active', 1)
            ->first();

        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Selected category must belong to the destination department and be active.',
            ], 422);
        }

        $fromDept = Department::where('id', $validated['from_department_id'])->where('is_active', 1)->first();
        $toDept = Department::where('id', $validated['to_department_id'])->where('is_active', 1)->first();
        if (!$fromDept || !$toDept) {
            return response()->json(['success' => false, 'message' => 'Invalid or inactive department.'], 422);
        }

        $paths = [];
        if ($request->hasFile('attachments')) {
            $uploadPath = public_path('ticket_attachments');
            if (!File::isDirectory($uploadPath)) {
                File::makeDirectory($uploadPath, 0755, true);
            }
            foreach ($request->file('attachments') as $file) {
                if (!$file) {
                    continue;
                }
                $originalName = $file->getClientOriginalName();
                $uniqueName = time() . '_' . preg_replace('/\s+/', '_', $originalName);
                $file->move($uploadPath, $uniqueName);
                $paths[] = 'ticket_attachments/' . $uniqueName;
            }
        }

        $ticket = Ticket::create([
            'ticket_no' => 'TKT-TMP-' . uniqid('', true),
            'location_id' => $validated['location_id'],
            'from_department_id' => $validated['from_department_id'],
            'to_department_id' => $validated['to_department_id'],
            'ticket_category_id' => $validated['ticket_category_id'],
            'priority' => $validated['priority'],
            'subject' => $validated['subject'],
            'description' => $validated['description'],
            'attachments' => $paths,
            'status' => 'open',
            'created_by' => auth()->id(),
        ]);

        $ticket->ticket_no = 'TKT-' . str_pad((string) $ticket->id, 5, '0', STR_PAD_LEFT);
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
            'ticket_category_id' => 'required|integer|exists:ticket_categories,id',
            'priority' => 'required|string|in:' . implode(',', Ticket::PRIORITIES),
            'subject' => 'required|string|max:500',
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

        $category = TicketCategory::query()
            ->where('id', $validated['ticket_category_id'])
            ->where('department_id', $validated['to_department_id'])
            ->where('is_active', 1)
            ->first();

        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Selected category must belong to the destination department and be active.',
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

        $newPaths = [];
        if ($request->hasFile('attachments')) {
            $uploadPath = public_path('ticket_attachments');
            if (!File::isDirectory($uploadPath)) {
                File::makeDirectory($uploadPath, 0755, true);
            }
            foreach ($request->file('attachments') as $file) {
                if (!$file) {
                    continue;
                }
                $originalName = $file->getClientOriginalName();
                $uniqueName = time() . '_' . preg_replace('/\s+/', '_', $originalName);
                $file->move($uploadPath, $uniqueName);
                $newPaths[] = 'ticket_attachments/' . $uniqueName;
            }
        }

        $ticket->location_id = $validated['location_id'];
        $ticket->from_department_id = $validated['from_department_id'];
        $ticket->to_department_id = $validated['to_department_id'];
        $ticket->ticket_category_id = $validated['ticket_category_id'];
        $ticket->priority = $validated['priority'];
        $ticket->subject = $validated['subject'];
        $ticket->description = $validated['description'];
        $ticket->attachments = array_values(array_merge($existing, $newPaths));
        $ticket->save();

        return response()->json([
            'success' => true,
            'message' => 'Ticket updated.',
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

        if (!$this->canUpdateTicketStatus($ticket)) {
            return response()->json([
                'success' => false,
                'message' => 'You are not allowed to update tickets for this department.',
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

        $ticket->load(['statusUpdater:id,user_fullname', 'category:id,name,sla_time']);
        $slaSummary = $ticket->slaVersusActualSummary();

        return response()->json([
            'success' => true,
            'message' => 'Status updated.',
            'ticket' => [
                'status' => $ticket->status,
                'status_updated_by_name' => $ticket->statusUpdater->user_fullname ?? '',
                'status_updated_at' => $this->formatTicketDateTimeForDisplay($ticket->status_updated_at),
                'time_to_close' => $ticket->timeToCloseDisplay(),
                'sla_vs_actual' => $slaSummary['text'],
                'sla_vs_actual_kind' => $slaSummary['kind'],
                'closed_status_note' => $ticket->closedStatusNote(),
            ],
        ]);
    }

    public function timeline(Ticket $ticket)
    {
        $solution = $ticket->solution ?? [];
        if (!is_array($solution)) {
            $solution = [];
        }

        $userIds = collect($solution)->pluck('user_id')
            ->filter()
            ->unique();
        $names = $this->ticketUserNames($userIds);

        $items = [];

        if ($solution === []) {
            $ticket->loadMissing('creator:id,user_fullname');
            $items[] = [
                'type' => 'status',
                'synthetic' => true,
                'created_at' => $this->formatTicketDateTimeForDisplay($ticket->created_at),
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
                    'created_at' => $at ? $this->formatTicketDateTimeForDisplay($at) : '',
                    'created_at_iso' => $iso,
                    'user_name' => $userName,
                    'from_status' => $e['from_status'] ?? null,
                    'to_status' => $e['to_status'] ?? 'open',
                    'note' => $e['note'] ?? null,
                ];
            }
        }

        usort($items, function (array $a, array $b) {
            return strcmp((string) ($a['created_at_iso'] ?? ''), (string) ($b['created_at_iso'] ?? ''));
        });

        return response()->json([
            'success' => true,
            'items' => array_values($items),
        ]);
    }

    private function formatTicketDateTimeForDisplay($value): string
    {
        if ($value === null) {
            return '';
        }

        return \Illuminate\Support\Carbon::parse($value)->format('d M Y, g:i A');
    }
}
