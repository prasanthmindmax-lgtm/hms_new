<?php

namespace App\Http\Controllers;

use App\Models\GrnRecord;
use App\Models\Tblcompany;
use App\Models\TblLocationModel;
use App\Models\Tblvendor;
use App\Models\TblZonesModel;
use App\Services\HrmsEmployeeService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class GrnController extends Controller
{
    private const AUDIT_APPROVER_ACCESS = [1, 4];

    public function __construct(private readonly HrmsEmployeeService $hrms)
    {
    }

    private function hrmsEmployees(bool $forceRefresh = false): array
    {
        return $this->hrms->all($forceRefresh);
    }

    private function userRow(): object
    {
        $u = auth()->user();
        if (! $u) {
            abort(403);
        }

        return is_object($u) ? $u : (object) (array) $u;
    }

    private function canAudit(object $u): bool
    {
        return in_array((int) ($u->access_limits ?? 0), self::AUDIT_APPROVER_ACCESS, true);
    }

    private function canEditRecord(GrnRecord $record, object $u): bool
    {
        if (! $record->isPending()) {
            return false;
        }

        // Pending records are editable by audit-approvers and by the original creator.
        if ($this->canAudit($u)) {
            return true;
        }

        return (int) $record->created_by === (int) auth()->id();
    }

    private function grnFilterIntList(Request $request, string $key): array
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

    /**
     * @return list<string>
     */
    private function grnFilterStringList(Request $request, string $key, array $allowed = []): array
    {
        $raw = $request->input($key);
        if ($raw === null || $raw === '') {
            return [];
        }
        $items = is_array($raw) ? $raw : explode(',', (string) $raw);
        $clean = [];
        foreach ($items as $item) {
            $val = trim((string) $item);
            if ($val === '') {
                continue;
            }
            if ($allowed && ! in_array($val, $allowed, true)) {
                continue;
            }
            $clean[$val] = true;
        }

        return array_keys($clean);
    }

    /**
     * Shared filters for the GRN list (and dashboard stats when $applyStatusFilter is false).
     */
    private function grnIndexFilteredQuery(Request $request, bool $applyStatusFilter = true): Builder
    {
        $allowedStatuses = [GrnRecord::STATUS_PENDING, GrnRecord::STATUS_APPROVED, GrnRecord::STATUS_REJECTED];

        $q = GrnRecord::query();
        if ($applyStatusFilter) {
            $statuses = $this->grnFilterStringList($request, 'status', $allowedStatuses);
            if ($statuses !== []) {
                $q->whereIn('audit_approval_status', $statuses);
            }
        }
        $companyIds = $this->grnFilterIntList($request, 'company_id');
        if ($companyIds !== []) {
            $q->whereIn('company_id', $companyIds);
        }
        $zoneIds = $this->grnFilterIntList($request, 'zone_id');
        if ($zoneIds !== []) {
            $q->whereIn('zone_id', $zoneIds);
        }
        $branchIds = $this->grnFilterIntList($request, 'branch_id');
        if ($branchIds !== []) {
            $q->whereIn('branch_id', $branchIds);
        }
        $vendorIds = $this->grnFilterIntList($request, 'vendor_id');
        if ($vendorIds !== []) {
            $q->whereIn('vendor_id', $vendorIds);
        }
        $search = trim((string) $request->input('universal_search', ''));
        if ($search !== '') {
            $like = '%'.addcslashes($search, '%_\\').'%';
            $q->where(function (Builder $sub) use ($like) {
                $sub->where('grn_number', 'like', $like)
                    ->orWhere('vendor_name', 'like', $like)
                    ->orWhere('invoice_number', 'like', $like)
                    ->orWhere('company_name', 'like', $like)
                    ->orWhere('zone_name', 'like', $like)
                    ->orWhere('branch_name', 'like', $like)
                    ->orWhere('received_by', 'like', $like)
                    ->orWhere('remarks', 'like', $like);
            });
        }
        if ($request->filled('date_from')) {
            $q->whereDate('created_at', '>=', $request->date('date_from'));
        }
        if ($request->filled('date_to')) {
            $q->whereDate('created_at', '<=', $request->date('date_to'));
        }

        return $q;
    }

    /** @return array<string, string> */
    private function grnFieldMessages(): array
    {
        return [
            'company_id.required' => 'Company field is required.',
            'zone_id.required' => 'Zone field is required.',
            'branch_id.required' => 'Branch field is required.',
            'vendor_name.required' => 'Vendor name field is required.',
            'invoice_number.required' => 'Invoice number field is required.',
            'invoice_date.required' => 'Invoice date field is required.',
            'received_date.required' => 'Received date field is required.',
            'received_by.required' => 'Received by field is required.',
            'invoice_copy.required' => 'Invoice copy (PDF) field is required.',
            'gps_video.required' => 'GPS verification video field is required.',
        ];
    }

    private function nextGrnNumber(): string
    {
        $y = date('Y');
        $prefix = 'GRN-'.$y.'-';
        $last = GrnRecord::query()
            ->where('grn_number', 'like', $prefix.'%')
            ->orderByDesc('id')
            ->value('grn_number');
        $n = 1;
        if ($last && preg_match('/^'.preg_quote($prefix, '/').'(\d+)$/', (string) $last, $m)) {
            $n = (int) $m[1] + 1;
        }

        return $prefix.str_pad((string) $n, 5, '0', STR_PAD_LEFT);
    }

    private function storeUploaded(?\Illuminate\Http\UploadedFile $file, array $allowedMime, int $maxKb): ?string
    {
        if (! $file || ! $file->isValid()) {
            return null;
        }
        $mime = strtolower((string) $file->getMimeType());
        if (! in_array($mime, $allowedMime, true)) {
            abort(422, 'Invalid file type.');
        }
        if ($file->getSize() > $maxKb * 1024) {
            abort(422, 'File too large (max '.$maxKb.' KB).');
        }

        $dir = 'grn_files';
        $name = time().'_'.uniqid('', false).'_'.preg_replace('/[^a-zA-Z0-9._-]/', '_', $file->getClientOriginalName());
        $file->move(public_path($dir), $name);

        return $dir.'/'.$name;
    }

    private function deletePublicFile(?string $relative): void
    {
        if ($relative === null || $relative === '') {
            return;
        }
        $path = public_path($relative);
        if (is_file($path)) {
            @unlink($path);
        }
    }

    public function index(Request $request): View
    {
        $u = $this->userRow();

        $perPageChoices = [10, 15, 25, 50, 100];
        $perPage = (int) $request->input('per_page', 10);
        if (! in_array($perPage, $perPageChoices, true)) {
            $perPage = 10;
        }

        $records = $this->grnIndexFilteredQuery($request, true)
            ->with(['reviewer:id,user_fullname,username,email'])
            ->orderByDesc('id')
            ->paginate($perPage)
            ->withQueryString();

        $statsBase = $this->grnIndexFilteredQuery($request, false);
        $stats = [
            'total' => (clone $statsBase)->count(),
            'pending' => (clone $statsBase)->where('audit_approval_status', GrnRecord::STATUS_PENDING)->count(),
            'approved' => (clone $statsBase)->where('audit_approval_status', GrnRecord::STATUS_APPROVED)->count(),
            'rejected' => (clone $statsBase)->where('audit_approval_status', GrnRecord::STATUS_REJECTED)->count(),
        ];

        $statusLabels = [
            GrnRecord::STATUS_PENDING => GrnRecord::statusLabel(GrnRecord::STATUS_PENDING),
            GrnRecord::STATUS_APPROVED => GrnRecord::statusLabel(GrnRecord::STATUS_APPROVED),
            GrnRecord::STATUS_REJECTED => GrnRecord::statusLabel(GrnRecord::STATUS_REJECTED),
        ];

        return view('superadmin.grn.index', [
            'admin' => $u,
            'records' => $records,
            'canAudit' => $this->canAudit($u),
            'companies' => Tblcompany::query()->orderBy('company_name')->get(['id', 'company_name']),
            'zones' => TblZonesModel::query()->orderBy('name')->get(['id', 'name']),
            'branches' => TblLocationModel::query()->orderBy('name')->get(['id', 'name', 'zone_id']),
            'vendors' => Tblvendor::query()->orderBy('display_name')->where('active_status', 0)->get(['id', 'display_name', 'company_name']),
            'statusLabels' => $statusLabels,
            'stats' => $stats,
            'grnPerPageChoices' => $perPageChoices,
            'grnPerPage' => $perPage,
        ]);
    }

    public function create(): View
    {
        $u = $this->userRow();
        $selectedZoneId = old('zone_id') ?: null;

        return view('superadmin.grn.create', [
            'admin' => $u,
            'record' => null,
            'isEdit' => false,
            'companies' => Tblcompany::query()->orderBy('company_name')->get(['id', 'company_name']),
            'zones' => TblZonesModel::query()->orderBy('name')->get(['id', 'name']),
            'branches' => TblLocationModel::query()
                ->when($selectedZoneId, fn($q) => $q->where('zone_id', $selectedZoneId))
                ->orderBy('name')
                ->get(['id', 'name', 'zone_id']),
            'vendors' => Tblvendor::query()->orderBy('display_name')->get(['id', 'display_name']),
            'employees' => $this->hrmsEmployees(),
            'canAudit' => $this->canAudit($u),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->userRow();
        $validated = $request->validate([
            'company_id' => 'required|integer|exists:company_tbl,id',
            'company_name' => 'nullable|string|max:255',
            'zone_id' => 'required|integer|exists:tblzones,id',
            'zone_name' => 'nullable|string|max:120',
            'branch_id' => 'required|integer|exists:tbl_locations,id',
            'branch_name' => 'nullable|string|max:255',
            'vendor_id' => 'nullable|integer',
            'vendor_name' => 'required|string|max:255',
            'invoice_number' => 'required|string|max:120',
            'invoice_date' => 'required|date',
            'received_date' => 'required|date',
            'received_by' => 'required|string|max:255',
            'remarks' => 'nullable|string|max:5000',
            'invoice_copy' => 'required|file|mimes:pdf|max:10240',
            'gps_video' => 'required|file|mimes:mp4,webm,mov,quicktime|max:10240',
        ], $this->grnFieldMessages());

        $invoicePath = $this->storeUploaded(
            $request->file('invoice_copy'),
            ['application/pdf'],
            10240
        );
        $videoPath = $this->storeUploaded(
            $request->file('gps_video'),
            ['video/mp4', 'video/webm', 'video/quicktime'],
            51200
        );

        GrnRecord::query()->create([
            'grn_number' => $this->nextGrnNumber(),
            'company_id' => $validated['company_id'] ?? null,
            'company_name' => $validated['company_name'] ?? null,
            'zone_id' => $validated['zone_id'] ?? null,
            'zone_name' => $validated['zone_name'] ?? null,
            'branch_id' => $validated['branch_id'] ?? null,
            'branch_name' => $validated['branch_name'] ?? null,
            'vendor_id' => $validated['vendor_id'] ?? null,
            'vendor_name' => $validated['vendor_name'],
            'invoice_number' => $validated['invoice_number'],
            'invoice_date' => $validated['invoice_date'] ?? null,
            'received_date' => $validated['received_date'] ?? null,
            'received_by' => $validated['received_by'] ?? null,
            'invoice_copy_path' => $invoicePath,
            'gps_video_path' => $videoPath,
            'gps_video_uploaded' => $videoPath !== null,
            'audit_approval_status' => GrnRecord::STATUS_PENDING,
            'remarks' => $validated['remarks'] ?? null,
            'created_by' => (int) auth()->id(),
        ]);

        return redirect()->route('grn.index')->with('success', 'GRN record created.');
    }

    public function show(GrnRecord $grnRecord): View
    {
        $u = $this->userRow();
        $grnRecord->loadMissing([
            'reviewer:id,user_fullname,username,email',
            'creator:id,user_fullname,username,email',
        ]);

        return view('superadmin.grn.show', [
            'admin' => $u,
            'record' => $grnRecord,
            'canEdit' => $this->canEditRecord($grnRecord, $u),
            'canAudit' => $this->canAudit($u),
        ]);
    }

    public function edit(GrnRecord $grnRecord): View
    {
        $u = $this->userRow();
        if (! $this->canEditRecord($grnRecord, $u)) {
            abort(403, 'You cannot edit this GRN record.');
        }

        $selectedZoneId = old('zone_id') ?: $grnRecord->zone_id ?: null;

        return view('superadmin.grn.create', [
            'admin' => $u,
            'record' => $grnRecord,
            'isEdit' => true,
            'companies' => Tblcompany::query()->orderBy('company_name')->get(['id', 'company_name']),
            'zones' => TblZonesModel::query()->orderBy('name')->get(['id', 'name']),
            'branches' => TblLocationModel::query()
                ->when($selectedZoneId, fn($q) => $q->where('zone_id', $selectedZoneId))
                ->orderBy('name')
                ->get(['id', 'name', 'zone_id']),
            'vendors' => Tblvendor::query()->orderBy('display_name')->get(['id', 'display_name']),
            'employees' => $this->hrmsEmployees(),
            'canAudit' => $this->canAudit($u),
        ]);
    }

    public function update(Request $request, GrnRecord $grnRecord): RedirectResponse
    {
        $u = $this->userRow();
        if (! $this->canEditRecord($grnRecord, $u)) {
            abort(403, 'You cannot edit this GRN record.');
        }

        $validator = Validator::make($request->all(), [
            'company_id' => 'required|integer|exists:company_tbl,id',
            'company_name' => 'nullable|string|max:255',
            'zone_id' => 'required|integer|exists:tblzones,id',
            'zone_name' => 'nullable|string|max:120',
            'branch_id' => 'required|integer|exists:tbl_locations,id',
            'branch_name' => 'nullable|string|max:255',
            'vendor_id' => 'nullable|integer',
            'vendor_name' => 'required|string|max:255',
            'invoice_number' => 'required|string|max:120',
            'invoice_date' => 'required|date',
            'received_date' => 'required|date',
            'received_by' => 'required|string|max:255',
            'remarks' => 'nullable|string|max:5000',
            'invoice_copy' => 'nullable|file|mimes:pdf|max:10240',
            'gps_video' => 'nullable|file|mimes:mp4,webm,mov,quicktime|max:10240',
        ], $this->grnFieldMessages());

        $validator->after(function (\Illuminate\Validation\Validator $v) use ($request, $grnRecord) {
            if (! $request->hasFile('invoice_copy') && empty($grnRecord->invoice_copy_path)) {
                $v->errors()->add('invoice_copy', 'Invoice copy (PDF) field is required.');
            }
            if (! $request->hasFile('gps_video') && empty($grnRecord->gps_video_path)) {
                $v->errors()->add('gps_video', 'GPS verification video field is required.');
            }
        });

        $validated = $validator->validate();

        $data = [
            'company_id' => $validated['company_id'] ?? null,
            'company_name' => $validated['company_name'] ?? null,
            'zone_id' => $validated['zone_id'] ?? null,
            'zone_name' => $validated['zone_name'] ?? null,
            'branch_id' => $validated['branch_id'] ?? null,
            'branch_name' => $validated['branch_name'] ?? null,
            'vendor_id' => $validated['vendor_id'] ?? null,
            'vendor_name' => $validated['vendor_name'],
            'invoice_number' => $validated['invoice_number'],
            'invoice_date' => $validated['invoice_date'] ?? null,
            'received_date' => $validated['received_date'] ?? null,
            'received_by' => $validated['received_by'] ?? null,
            'remarks' => $validated['remarks'] ?? null,
        ];

        if ($request->hasFile('invoice_copy')) {
            $this->deletePublicFile($grnRecord->invoice_copy_path);
            $data['invoice_copy_path'] = $this->storeUploaded(
                $request->file('invoice_copy'),
                ['application/pdf'],
                10240
            );
        }

        if ($request->hasFile('gps_video')) {
            $this->deletePublicFile($grnRecord->gps_video_path);
            $data['gps_video_path'] = $this->storeUploaded(
                $request->file('gps_video'),
                ['video/mp4', 'video/webm', 'video/quicktime'],
                51200
            );
            $data['gps_video_uploaded'] = $data['gps_video_path'] !== null;
        }

        $grnRecord->update($data);

        return redirect()->route('grn.index')->with('success', 'GRN record updated.');
    }

    public function approve(GrnRecord $grnRecord): RedirectResponse
    {
        $u = $this->userRow();
        if (! $this->canAudit($u)) {
            abort(403, 'You are not authorized to approve GRN audit.');
        }
        if (! $grnRecord->isPending()) {
            return back()->with('error', 'This record is not pending audit.');
        }
        $grnRecord->update([
            'audit_approval_status' => GrnRecord::STATUS_APPROVED,
            'reviewed_by' => (int) auth()->id(),
            'reviewed_at' => now(),
            'rejection_reason' => null,
        ]);

        return back()->with('success', 'Audit approved.');
    }

    public function reject(Request $request, GrnRecord $grnRecord): RedirectResponse
    {
        $u = $this->userRow();
        if (! $this->canAudit($u)) {
            abort(403, 'You are not authorized to reject GRN audit.');
        }
        if (! $grnRecord->isPending()) {
            return back()->with('error', 'This record is not pending audit.');
        }
        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:2000',
        ]);
        $grnRecord->update([
            'audit_approval_status' => GrnRecord::STATUS_REJECTED,
            'reviewed_by' => (int) auth()->id(),
            'reviewed_at' => now(),
            'rejection_reason' => $validated['rejection_reason'],
        ]);

        return back()->with('success', 'Audit rejected.');
    }
}
