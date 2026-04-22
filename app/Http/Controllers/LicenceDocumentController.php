<?php

namespace App\Http\Controllers;

use App\Models\BranchLicenceDocument;
use App\Models\TblLocationModel;
use App\Models\TblZonesModel;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\View\View;

class LicenceDocumentController extends Controller
{
    private function branchLicenceLevel(?int $raw): int
    {
        $n = (int) ($raw ?? 1);

        return $n === 2 ? 2 : 1;
    }

    /**
     * Flatten zone → branches into a sorted list, optional search, then paginate.
     *
     * @param  array{zones: array<int, mixed>, doc_total?: int}  $levelBlock
     * @param  array<int, int>  $counts  Uploaded doc counts per branch_id
     * @param  'all'|'complete'|'incomplete'  $branchFilter
     */
    private function paginateLicenceBranchRows(
        array $levelBlock,
        Request $request,
        string $q,
        int $perPage,
        int $level,
        int $otherLevelPage,
        array $counts,
        string $branchFilter
    ): LengthAwarePaginator {
        $flat = [];
        foreach ($levelBlock['zones'] as $zr) {
            $zone = $zr['zone'];
            foreach ($zr['branches'] as $b) {
                $flat[] = ['zone' => $zone, 'branch' => $b];
            }
        }

        usort($flat, function ($a, $b) {
            $z = strcmp((string) $a['zone']->name, (string) $b['zone']->name);
            if ($z !== 0) {
                return $z;
            }

            return strcmp((string) $a['branch']->name, (string) $b['branch']->name);
        });

        if ($q !== '') {
            $ql = Str::lower($q);
            $flat = array_values(array_filter($flat, function ($row) use ($ql) {
                $hay = Str::lower((string) $row['zone']->name.' '.(string) $row['branch']->name);

                return Str::contains($hay, $ql);
            }));
        }

        $docTotal = (int) ($levelBlock['doc_total'] ?? 0);
        if ($branchFilter === 'complete') {
            $flat = array_values(array_filter($flat, function ($row) use ($counts, $docTotal) {
                $bid = (int) $row['branch']->id;
                $uploaded = (int) ($counts[$bid] ?? 0);

                return $docTotal > 0 && $uploaded >= $docTotal;
            }));
        } elseif ($branchFilter === 'incomplete') {
            $flat = array_values(array_filter($flat, function ($row) use ($counts, $docTotal) {
                $bid = (int) $row['branch']->id;
                $uploaded = (int) ($counts[$bid] ?? 0);

                return $docTotal > 0 && $uploaded < $docTotal;
            }));
        }

        $total = count($flat);
        $lastPage = max(1, (int) ceil($total / $perPage));
        $pageName = $level === 1 ? 'page_l1' : 'page_l2';
        $page = max(1, (int) $request->input($pageName, 1));
        if ($page > $lastPage) {
            $page = $lastPage;
        }

        $slice = array_slice($flat, ($page - 1) * $perPage, $perPage);

        $paginator = new LengthAwarePaginator($slice, $total, $perPage, $page, [
            'path' => $request->url(),
            'pageName' => $pageName,
        ]);

        $otherPageKey = $level === 1 ? 'page_l2' : 'page_l1';
        $appends = [
            'level' => $level,
            $otherPageKey => $otherLevelPage,
        ];
        if ($q !== '') {
            $appends['q'] = $q;
        }
        if ($branchFilter !== 'all') {
            $appends['ld_filter'] = $branchFilter;
        }
        $paginator->appends($appends);
        $paginator->fragment('ld-licence-workspace');

        return $paginator;
    }

    private function getZonesAndLocations($admin): array
    {
        $locations = null;
        $zones = null;

        if ($admin->access_limits == 1) {
            $zones = TblZonesModel::select('name', 'id')->orderBy('name')->get();
            $locations = TblLocationModel::query()
                ->active()
                ->select('name', 'id', 'zone_id', 'level')
                ->orderBy('name')
                ->get();
        } elseif ($admin->access_limits == 2) {
            $zoneIds = [];

            if (! empty($admin->multi_location)) {
                $multiLocations = explode(',', $admin->multi_location);

                $locationsFromMulti = TblLocationModel::whereIn('id', $multiLocations)
                    ->pluck('zone_id')
                    ->unique()
                    ->toArray();

                $zoneIds = array_unique(array_merge([$admin->zone_id], $locationsFromMulti));

                $locations = TblLocationModel::query()
                    ->active()
                    ->select('name', 'id', 'zone_id', 'level')
                    ->where('zone_id', $admin->zone_id)
                    ->get();

                $specificLocations = TblLocationModel::query()
                    ->active()
                    ->select('name', 'id', 'zone_id', 'level')
                    ->whereIn('id', $multiLocations)
                    ->get();

                $locations = $locations->merge($specificLocations)->unique('id');
            } else {
                $locations = TblLocationModel::query()
                    ->active()
                    ->select('name', 'id', 'zone_id', 'level')
                    ->where('zone_id', $admin->zone_id)
                    ->get();

                $zoneIds = [$admin->zone_id];
            }

            $zones = TblZonesModel::select('name', 'id')
                ->whereIn('id', $zoneIds)
                ->orderBy('name')
                ->get();
        } else {
            $branchIds = [];
            $branchIds[] = $admin->branch_id;

            if (! empty($admin->multi_location)) {
                $multiLocations = explode(',', $admin->multi_location);
                $branchIds = array_merge($branchIds, $multiLocations);

                $locations = TblLocationModel::query()
                    ->active()
                    ->select('name', 'id', 'zone_id', 'level')
                    ->whereIn('id', $branchIds)
                    ->get();
            } else {
                $locations = TblLocationModel::query()
                    ->active()
                    ->select('name', 'id', 'zone_id', 'level')
                    ->where('id', $admin->branch_id)
                    ->get();
            }

            $zoneIds = $locations->pluck('zone_id')->unique()->toArray();
            $zones = TblZonesModel::select('name', 'id')
                ->whereIn('id', $zoneIds)
                ->orderBy('name')
                ->get();
        }

        return compact('zones', 'locations');
    }

    private function allowedBranchIdList($admin): ?array
    {
        if ($admin->access_limits == 1) {
            return null;
        }

        if ($admin->access_limits == 2) {
            $ids = TblLocationModel::query()
                ->active()
                ->where('zone_id', $admin->zone_id)
                ->pluck('id')
                ->map(fn ($id) => (int) $id)
                ->all();

            if (! empty($admin->multi_location)) {
                $extra = array_map('intval', explode(',', $admin->multi_location));
                $ids = array_values(array_unique(array_merge($ids, $extra)));
            }

            return $this->filterToActiveBranchIds($ids);
        }

        $ids = [(int) $admin->branch_id];
        if (! empty($admin->multi_location)) {
            $ids = array_merge($ids, array_map('intval', explode(',', $admin->multi_location)));
        }

        return $this->filterToActiveBranchIds(array_values(array_unique($ids)));
    }

    private function filterToActiveBranchIds(array $ids): array
    {
        if ($ids === []) {
            return [];
        }

        $active = TblLocationModel::query()
            ->active()
            ->whereIn('id', $ids)
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->all();

        return array_values(array_unique($active));
    }

    private function canAccessBranch($admin, int $branchId): bool
    {
        $allowed = $this->allowedBranchIdList($admin);
        if ($allowed === null) {
            return TblLocationModel::query()->active()->whereKey($branchId)->exists();
        }

        return in_array($branchId, $allowed, true);
    }

    public function index(Request $request): View
    {
        $admin = auth()->user();
        $data = $this->getZonesAndLocations($admin);
        $zones = $data['zones'];
        $locations = $data['locations'];

        $branchIds = $locations->pluck('id')->map(fn ($id) => (int) $id)->all();

        $branchExpectedLevel = [];
        foreach ($locations as $loc) {
            $branchExpectedLevel[(int) $loc->id] = $this->branchLicenceLevel(isset($loc->level) ? (int) $loc->level : null);
        }

        $counts = [];
        if ($branchIds !== []) {
            $rows = BranchLicenceDocument::query()
                ->whereIn('branch_id', $branchIds)
                ->whereNotNull('file_path')
                ->get(['branch_id', 'level']);

            foreach ($rows as $r) {
                $bid = (int) $r->branch_id;
                $expected = $branchExpectedLevel[$bid] ?? 1;
                if ((int) $r->level !== $expected) {
                    continue;
                }
                $counts[$bid] = ($counts[$bid] ?? 0) + 1;
            }
        }

        $l1Total = BranchLicenceDocument::requiredDocumentCountForLevel(1);
        $l2Total = BranchLicenceDocument::requiredDocumentCountForLevel(2);

        $levelBlocks = [];
        foreach ([1, 2] as $lv) {
            $docTotal = $lv === 2 ? $l2Total : $l1Total;
            $branchesAtLevel = $locations->filter(function ($loc) use ($lv) {
                return $this->branchLicenceLevel(isset($loc->level) ? (int) $loc->level : null) === $lv;
            });

            $zoneRows = [];
            foreach ($zones as $zone) {
                $branches = $branchesAtLevel->where('zone_id', $zone->id)->values();
                if ($branches->isNotEmpty()) {
                    $zoneRows[] = [
                        'zone' => $zone,
                        'branches' => $branches,
                    ];
                }
            }

            $levelBlocks[] = [
                'level' => $lv,
                'doc_total' => $docTotal,
                'zones' => $zoneRows,
                'branch_count' => $branchesAtLevel->count(),
            ];
        }

        $l1Block = $levelBlocks[0];
        $l2Block = $levelBlocks[1];
        $l1CompleteBranches = 0;
        $l2CompleteBranches = 0;
        $l1PendingBranches = 0;
        $l2PendingBranches = 0;
        foreach ($locations as $loc) {
            $bid = (int) $loc->id;
            $lev = $branchExpectedLevel[$bid] ?? 1;
            $docTotalForBranch = $lev === 2 ? $l2Total : $l1Total;
            $uploaded = (int) ($counts[$bid] ?? 0);
            if ($docTotalForBranch > 0 && $uploaded >= $docTotalForBranch) {
                if ($lev === 1) {
                    $l1CompleteBranches++;
                } else {
                    $l2CompleteBranches++;
                }
            }
            if ($docTotalForBranch > 0 && $uploaded < $docTotalForBranch) {
                if ($lev === 1) {
                    $l1PendingBranches++;
                } else {
                    $l2PendingBranches++;
                }
            }
        }

        $licenceStats = [
            'l1_branches' => (int) $l1Block['branch_count'],
            'l2_branches' => (int) $l2Block['branch_count'],
            'l1_doc_types' => $l1Total,
            'l2_doc_types' => $l2Total,
            'l1_complete_branches' => $l1CompleteBranches,
            'l2_complete_branches' => $l2CompleteBranches,
            'l1_pending_branches' => $l1PendingBranches,
            'l2_pending_branches' => $l2PendingBranches,
            'l1_zones' => count($l1Block['zones']),
            'l2_zones' => count($l2Block['zones']),
        ];

        $q = trim((string) $request->input('q', ''));
        $rawBranchFilter = strtolower((string) $request->input('ld_filter', 'all'));
        $branchFilter = in_array($rawBranchFilter, ['complete', 'incomplete'], true) ? $rawBranchFilter : 'all';

        $perPage = 15;
        $defaultLevel =
            (int) $l1Block['branch_count'] === 0 && (int) $l2Block['branch_count'] > 0 ? 2 : 1;
        $viewLevel = (int) $request->input('level', $defaultLevel);
        if ($viewLevel !== 1 && $viewLevel !== 2) {
            $viewLevel = $defaultLevel;
        }

        $keepPageL2 = max(1, (int) $request->input('page_l2', 1));
        $keepPageL1 = max(1, (int) $request->input('page_l1', 1));

        $branchPaginatorL1 = $this->paginateLicenceBranchRows(
            $l1Block,
            $request,
            $q,
            $perPage,
            1,
            $keepPageL2,
            $counts,
            $branchFilter
        );
        $branchPaginatorL2 = $this->paginateLicenceBranchRows(
            $l2Block,
            $request,
            $q,
            $perPage,
            2,
            $keepPageL1,
            $counts,
            $branchFilter
        );

        $licenceRenewalNotifications = $this->buildLicenceRenewalNotifications($branchIds, $branchExpectedLevel);

        return view('superadmin.licence_documents.index', [
            'admin' => $admin,
            'levelBlocks' => $levelBlocks,
            'counts' => $counts,
            'licenceStats' => $licenceStats,
            'branchPaginatorL1' => $branchPaginatorL1,
            'branchPaginatorL2' => $branchPaginatorL2,
            'searchQuery' => $q,
            'viewLevel' => $viewLevel,
            'branchFilter' => $branchFilter,
            'licenceRenewalNotifications' => $licenceRenewalNotifications,
        ]);
    }

    /**
     * Notices for documents with a renewal date: show from one month before renewal through overdue.
     *
     * @param  array<int, int>  $branchExpectedLevel
     * @return array<int, array{branch_id: int, branch_name: string, document_label: string, renewal_date: Carbon, kind: string, days_note: string}>
     */
    private function buildLicenceRenewalNotifications(array $branchIds, array $branchExpectedLevel): array
    {
        if ($branchIds === []) {
            return [];
        }

        $today = Carbon::today();
        $rows = BranchLicenceDocument::query()
            ->whereIn('branch_id', $branchIds)
            ->whereNotNull('file_path')
            ->whereNotNull('renewal_date')
            ->with(['branch:id,name'])
            ->get();

        $out = [];
        foreach ($rows as $r) {
            $bid = (int) $r->branch_id;
            $expected = $branchExpectedLevel[$bid] ?? 1;
            if ((int) $r->level !== $expected) {
                continue;
            }

            $renewal = $r->renewal_date->copy()->startOfDay();
            $notifyFrom = $renewal->copy()->subMonths(1)->startOfDay();
            if ($today->lt($notifyFrom)) {
                continue;
            }

            $label = BranchLicenceDocument::documentLabelForKey($expected, (string) $r->document_key)
                ?? (string) $r->document_key;
            $branchName = (string) ($r->branch->name ?? 'Branch #'.$bid);

            if ($today->gt($renewal)) {
                $daysPast = (int) $renewal->diffInDays($today);
                $kind = 'overdue';
                $daysNote = $daysPast === 1 ? '1 day overdue' : $daysPast.' days overdue';
            } elseif ($today->eq($renewal)) {
                $kind = 'today';
                $daysNote = 'Renews today';
            } else {
                $kind = 'upcoming';
                $daysLeft = (int) $today->diffInDays($renewal);
                $daysNote = $daysLeft === 1 ? '1 day until renewal' : $daysLeft.' days until renewal';
            }

            $out[] = [
                'branch_id' => $bid,
                'branch_name' => $branchName,
                'document_label' => $label,
                'renewal_date' => $renewal,
                'kind' => $kind,
                'days_note' => $daysNote,
            ];
        }

        usort($out, function ($a, $b) {
            return $a['renewal_date']->timestamp <=> $b['renewal_date']->timestamp;
        });

        return $out;
    }

    public function branch(int $branchId): View
    {
        $admin = auth()->user();
        if (! $this->canAccessBranch($admin, $branchId)) {
            abort(403);
        }

        $branch = TblLocationModel::query()->active()->with('zone')->find($branchId);
        if (! $branch) {
            abort(404);
        }

        $assignedLevel = $this->branchLicenceLevel(isset($branch->level) ? (int) $branch->level : null);

        $docs = BranchLicenceDocument::query()
            ->where('branch_id', $branchId)
            ->where('level', $assignedLevel)
            ->get()
            ->keyBy('document_key');

        $catalog = BranchLicenceDocument::catalogForLevel($assignedLevel);
        $documentRows = [];
        $today = Carbon::today();
        foreach ($catalog as $row) {
            $key = $row['key'];
            $rec = $docs->get($key);
            $hasFile = $rec && ! empty($rec->file_path);
            $renewalCarbon = $rec && $rec->renewal_date ? $rec->renewal_date->copy()->startOfDay() : null;

            if (! $hasFile) {
                $status = 'missing';
                $statusLabel = 'Missing';
            } elseif (! $renewalCarbon) {
                $status = 'on_file';
                $statusLabel = 'On file';
            } elseif ($renewalCarbon->lt($today)) {
                $status = 'overdue';
                $statusLabel = 'Renewal overdue';
            } elseif ($renewalCarbon->lte($today->copy()->addDays(60))) {
                $status = 'expiring';
                $statusLabel = 'Expiring soon';
            } else {
                $status = 'valid';
                $statusLabel = 'Valid';
            }

            $documentRows[] = [
                'key' => $key,
                'label' => $row['label'],
                'file_path' => $rec?->file_path,
                'original_filename' => $rec?->original_filename,
                'renewal_date' => $rec && $rec->renewal_date ? $rec->renewal_date->format('Y-m-d') : null,
                'updated_at' => $rec && $rec->updated_at ? $rec->updated_at->format('d M Y, H:i') : null,
                'status' => $status,
                'status_label' => $statusLabel,
            ];
        }

        $docTotal = BranchLicenceDocument::requiredDocumentCountForLevel($assignedLevel);

        return view('superadmin.licence_documents.create', [
            'admin' => $admin,
            'branch' => $branch,
            'assignedLevel' => $assignedLevel,
            'documentRows' => $documentRows,
            'docTotal' => $docTotal,
        ]);
    }

    public function save(Request $request): RedirectResponse
    {
        $admin = auth()->user();

        $request->merge([
            'renewal_date' => $request->input('renewal_date') ?: null,
        ]);

        $validated = $request->validate([
            'branch_id' => 'required|integer',
            'level' => 'required|in:1,2',
            'document_key' => 'required|string|max:120',
            'renewal_date' => 'nullable|date',
            'file' => 'nullable|file|max:15360|mimes:pdf,png,jpg,jpeg,gif,webp,doc,docx,xls,xlsx',
        ]);

        $branchId = (int) $validated['branch_id'];
        if (! $this->canAccessBranch($admin, $branchId)) {
            abort(403);
        }

        $branch = TblLocationModel::query()->active()->find($branchId);
        if (! $branch) {
            abort(404);
        }

        $expectedLevel = $this->branchLicenceLevel(isset($branch->level) ? (int) $branch->level : null);

        $level = (int) $validated['level'];
        if ($level !== $expectedLevel) {
            return back()->with('error', 'This branch is assigned to Level '.$expectedLevel.' licence documents only. Change the assignment in Location Master if needed.');
        }

        $documentKey = $validated['document_key'];
        $validKeys = BranchLicenceDocument::validKeysForLevel($level);
        if (! in_array($documentKey, $validKeys, true)) {
            return back()->with('error', 'Invalid document type.');
        }

        $existing = BranchLicenceDocument::query()
            ->where('branch_id', $branchId)
            ->where('level', $level)
            ->where('document_key', $documentKey)
            ->first();

        if (! $request->hasFile('file') && ! $existing) {
            return back()->with('error', 'Please choose a file to upload.');
        }

        $dir = public_path('licence_documents');
        if (! File::isDirectory($dir)) {
            File::makeDirectory($dir, 0755, true);
        }

        $filePath = $existing->file_path ?? null;
        $originalName = $existing->original_filename ?? null;

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $originalName = $file->getClientOriginalName();
            $safe = time().'_'.mt_rand(1000, 9999).'_'.preg_replace('/[^a-zA-Z0-9._-]/', '_', $originalName);
            $file->move($dir, $safe);
            $newRel = 'licence_documents/'.$safe;
            if ($filePath && File::exists(public_path($filePath))) {
                @File::delete(public_path($filePath));
            }
            $filePath = $newRel;
        }

        BranchLicenceDocument::query()->updateOrCreate(
            [
                'branch_id' => $branchId,
                'level' => $level,
                'document_key' => $documentKey,
            ],
            [
                'file_path' => $filePath,
                'original_filename' => $originalName,
                'renewal_date' => $validated['renewal_date'] ?? null,
                'updated_by' => auth()->id(),
            ]
        );

        $successMessage = $request->hasFile('file')
            ? 'Licence document uploaded successfully.'
            : 'Licence document updated successfully.';

        return back()->with('success', $successMessage);
    }
}
