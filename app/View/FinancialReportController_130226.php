<?php

namespace App\Http\Controllers;

use App\Models\BranchFinancialReport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\FinancialReportExport;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class FinancialReportController extends Controller
{
    /**
     * Display the financial reports with access-based filtering
     */
    public function index(Request $request)
    {
        $admin = Auth::user();

        $query = BranchFinancialReport::with([
            'branch:id,name,zone_id',
            'zone:id,name',
            'creator:id,user_fullname',
            'updater:id,user_fullname',
            'placedBy:id,username,user_fullname',
            'lockerBy:id,username,user_fullname',
            'radiantGivenBy:id,username,user_fullname',
            'auditorApprovedBy:id,user_fullname',
            'managementApprovedBy:id,user_fullname',
        ]);

        /* =========================
        ACCESS-BASED FILTERING
        ========================== */
        $this->applyAccessFilter($query, $admin);

        /* =========================
        DATE FILTER (Single Date Picker for both from/to)
        ========================== */
        if ($request->filled('date_range')) {
            $dates = explode(' - ', $request->date_range);
            if (count($dates) == 2) {
                $query->whereDate('report_date', '>=', $dates[0])
                      ->whereDate('report_date', '<=', $dates[1]);
            }
        }

        /* =========================
        ZONE FILTER (Based on Access)
        ========================== */
        if ($request->filled('zone_id')) {
            $allowedZones = $this->getAllowedZones($admin);
            $requestedZones = (array) $request->zone_id;
            
            // Only filter by zones that user has access to
            $filteredZones = array_intersect($requestedZones, $allowedZones);
            
            if (!empty($filteredZones)) {
                $query->whereIn('zone_id', $filteredZones);
            }
        }

        /* =========================
        BRANCH FILTER (Based on Access)
        ========================== */
        if ($request->filled('branch_id')) {
            $allowedBranches = $this->getAllowedBranches($admin);
            $requestedBranches = (array) $request->branch_id;
            
            // Only filter by branches that user has access to
            $filteredBranches = array_intersect($requestedBranches, $allowedBranches);
            
            if (!empty($filteredBranches)) {
                $query->whereIn('branch_id', $filteredBranches);
            }
        }

        /* =========================
        REPORTS with per_page parameter
        ========================== */
        $perPage = $request->get('per_page', 10);
        
        $reports = $query
            ->orderBy('report_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        // Add zone_name and branch_name to each report
        foreach ($reports as $report) {
            $report->zone_name = optional($report->zone)->name;
            $report->branch_name = optional($report->branch)->name;
        }

        /* =========================
        SUMMARY (clone query before pagination)
        ========================== */
        $summaryQuery = BranchFinancialReport::query();
        $this->applyAccessFilter($summaryQuery, $admin);
        
        if ($request->filled('date_range')) {
            $dates = explode(' - ', $request->date_range);
            if (count($dates) == 2) {
                $summaryQuery->whereDate('report_date', '>=', $dates[0])
                             ->whereDate('report_date', '<=', $dates[1]);
            }
        }
        
        if ($request->filled('zone_id')) {
            $allowedZones = $this->getAllowedZones($admin);
            $requestedZones = (array) $request->zone_id;
            $filteredZones = array_intersect($requestedZones, $allowedZones);
            if (!empty($filteredZones)) {
                $summaryQuery->whereIn('zone_id', $filteredZones);
            }
        }
        
        if ($request->filled('branch_id')) {
            $allowedBranches = $this->getAllowedBranches($admin);
            $requestedBranches = (array) $request->branch_id;
            $filteredBranches = array_intersect($requestedBranches, $allowedBranches);
            if (!empty($filteredBranches)) {
                $summaryQuery->whereIn('branch_id', $filteredBranches);
            }
        }

        $summary = [
            'total_radiant'  => $summaryQuery->sum('radiant_collection_amount'),
            'total_card'     => $summaryQuery->sum('actual_card_amount'),
            'total_bank'     => $summaryQuery->sum('bank_deposit_amount'),
            'total_discount' => $summaryQuery->sum('today_discount_amount'),
            'total_cancel'   => $summaryQuery->sum('cancel_bill_amount'),
            'total_refund'   => $summaryQuery->sum('refund_bill_amount'),
            'report_count'   => $summaryQuery->count(),
        ];

        $summary['total_collection'] =
            $summary['total_radiant'] + $summary['total_card'] + $summary['total_bank'];

        $summary['total_deductions'] =
            $summary['total_discount'] + $summary['total_cancel'] + $summary['total_refund'];

        $summary['net_amount'] =
            $summary['total_collection'] - $summary['total_deductions'];

        /* =========================
        AJAX RESPONSE
        ========================== */
        if ($request->ajax() || $request->has('ajax')) {
            return view('branch.partials.reports-table', compact('reports', 'summary', 'admin'));
        }

        /* =========================
        FILTER DATA (Based on Access)
        ========================== */
        $zones = $this->getAccessibleZones($admin);
        $branches = $this->getAccessibleBranches($admin);

        return view('branch.index', compact('admin','reports','summary','zones','branches'));
    }

    /**
     * Apply access-based filter to query
     */
    private function applyAccessFilter($query, $admin)
    {
        if ($admin->access_limits == 1 || $admin->access_limits == 4) {
            // SUPERADMIN / AUDITOR → ALL DATA
            return;
        } elseif ($admin->access_limits == 2) {
            // ZONAL ADMIN → zone branches + multi-location branches
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
                $query->whereIn('branch_id', $branchIds);
            }
        } elseif ($admin->access_limits == 3) {
            // ADMIN → multi-location branches only
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
                $query->whereIn('branch_id', $branchIds);
            }
        } elseif ($admin->access_limits == 5) {
            // USER → own records only
            $query->where('created_by', $admin->id);
        }
    }

    /**
     * Get allowed zones for user
     */
    private function getAllowedZones($admin)
    {
        if ($admin->access_limits == 1 || $admin->access_limits == 4) {
            return DB::table('tblzones')->pluck('id')->toArray();
        } elseif ($admin->access_limits == 2) {
            return [$admin->zone_id];
        }
        
        return [];
    }

    /**
     * Get allowed branches for user
     */
    private function getAllowedBranches($admin)
    {
        if ($admin->access_limits == 1 || $admin->access_limits == 4) {
            return DB::table('tbl_locations')->pluck('id')->toArray();
        } elseif ($admin->access_limits == 2) {
            $branchIds = [];
            if (!empty($admin->zone_id)) {
                $branchIds = array_merge($branchIds, 
                    DB::table('tbl_locations')->where('zone_id', $admin->zone_id)->pluck('id')->toArray()
                );
            }
            if (!empty($admin->multi_location)) {
                $branchIds = array_merge($branchIds, 
                    array_map('intval', explode(',', $admin->multi_location))
                );
            }
            return array_unique($branchIds);
        } elseif ($admin->access_limits == 3) {
            if (!empty($admin->multi_location)) {
                return array_map('intval', explode(',', $admin->multi_location));
            }
        }
        
        return [];
    }

    /**
     * Get accessible zones based on user role
     */
    private function getAccessibleZones($admin)
    {
        $query = DB::table('tblzones')->select('id', 'name')->orderBy('name');

        if (in_array($admin->access_limits, [1, 4])) {
            return $query->get();
        }

        if ($admin->access_limits == 2) {
            $zoneIds = [$admin->zone_id];

            if (!empty($admin->multi_location)) {
                $multiLocations = array_map('intval', explode(',', $admin->multi_location));

                $multiZoneIds = DB::table('tbl_locations')
                    ->whereIn('id', $multiLocations)
                    ->pluck('zone_id')
                    ->unique()
                    ->toArray();

                $zoneIds = array_unique(array_merge($zoneIds, $multiZoneIds));
            }

            $query->whereIn('id', $zoneIds);
        }

        if (in_array($admin->access_limits, [3, 5])) {
            $branchIds = [$admin->branch_id];

            if (!empty($admin->multi_location)) {
                $branchIds = array_unique(array_merge(
                    $branchIds,
                    array_map('intval', explode(',', $admin->multi_location))
                ));
            }

            $zoneIds = DB::table('tbl_locations')
                ->whereIn('id', $branchIds)
                ->pluck('zone_id')
                ->unique()
                ->toArray();

            $query->whereIn('id', $zoneIds);
        }

        return $query->get();
    }

    /**
     * Get accessible branches based on user role
     */
    private function getAccessibleBranches($admin)
    {
        $query = DB::table('tbl_locations')
            ->select('id', 'name', 'zone_id')
            ->orderBy('name');

        if (in_array($admin->access_limits, [1, 4])) {
            return $query->get();
        }

        if ($admin->access_limits == 2) {
            $branchIds = [];

            $zoneBranches = DB::table('tbl_locations')
                ->where('zone_id', $admin->zone_id)
                ->pluck('id')
                ->toArray();

            $branchIds = $zoneBranches;

            if (!empty($admin->multi_location)) {
                $multiBranches = array_map('intval', explode(',', $admin->multi_location));
                $branchIds = array_unique(array_merge($branchIds, $multiBranches));
            }

            $query->whereIn('id', $branchIds);
        }

        if (in_array($admin->access_limits, [3, 5])) {
            $branchIds = [$admin->branch_id];

            if (!empty($admin->multi_location)) {
                $branchIds = array_unique(array_merge(
                    $branchIds,
                    array_map('intval', explode(',', $admin->multi_location))
                ));
            }

            $query->whereIn('id', $branchIds);
        }

        return $query->get();
    }

    /**
     * View single report details in modal
     */
    public function show($id)
    {
        $admin = Auth::user();
        
        $report = BranchFinancialReport::with([
            'branch:id,name,zone_id',
            'zone:id,name',
            'creator:id,user_fullname',
            'updater:id,user_fullname',
            'placedBy:id,username,user_fullname',
            'lockerBy:id,username,user_fullname',
            'radiantGivenBy:id,username,user_fullname',
            'auditorApprovedBy:id,user_fullname',
            'managementApprovedBy:id,user_fullname'
        ])->findOrFail($id);
        
        $report->zone_name = optional($report->zone)->name;
        $report->branch_name = optional($report->branch)->name;
        
        if (!$this->canAccessReport($admin, $report)) {
            return response()->json(['error' => 'Access denied'], 403);
        }
        
        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'report' => $report,
                'can_approve_auditor' => $this->canApproveAsAuditor($admin, $report),
                'can_approve_management' => $this->canApproveAsManagement($admin, $report)
            ]);
        }
        
        return view('branch.show', compact('admin', 'report'));
    }

    /**
     * Get attachments for a report
     */
    public function getAttachments($id)
    {
        $admin = Auth::user();

        $report = BranchFinancialReport::findOrFail($id);

        if (!$this->canAccessReport($admin, $report)) {
            return response()->json(['error' => 'Access denied'], 403);
        }

        $attachments = [
            'radiant' => [
                'label' => 'Radiant Collection Files',
                'files' => $report->radiant_collection_files ?? [],
            ],
            'card' => [
                'label' => 'Card Payment Files',
                'files' => $report->actual_card_files ?? [],
            ],
            'bank' => [
                'label' => 'Bank Deposit Files',
                'files' => $report->bank_deposit_files ?? [],
            ],
        ];

        return response()->json([
            'success' => true,
            'report_id' => $report->id,
            'report_date' => $report->report_date,
            'branch_name' => optional($report->branch)->name,
            'attachments' => $attachments,
        ]);
    }

    /**
     * Check if user can access this report
     */
    private function canAccessReport($admin, $report)
    {
        if ($admin->access_limits == 1 || $admin->access_limits == 4) {
            return true;
        }
        
        $allowedBranches = $this->getAllowedBranches($admin);
        return in_array($report->branch_id, $allowedBranches);
    }

    /**
     * Check if user can approve as Auditor
     */
    private function canApproveAsAuditor($admin, $report)
    {
        if ($admin->access_limits != 4) {
            return false;
        }
        
        // if ($report->created_by == $admin->id) {
        //     return false;
        // }
        
        if ($report->auditor_approval_status != 0) {
            return false;
        }
        
        return true;
    }

    /**
     * Check if user can approve as Management
     */
    private function canApproveAsManagement($admin, $report)
    {
        if ($admin->access_limits != 1) {
            return false;
        }
        
        if ($report->created_by == $admin->id) {
            return false;
        }
        
        if ($report->auditor_approval_status != 1) {
            return false;
        }
        
        if ($report->management_approval_status != 0) {
            return false;
        }
        
        return true;
    }

    /**
     * Approve by Auditor
     */
    public function approveAuditor(Request $request, $id)
    {
        $admin = Auth::user();
        
        $report = BranchFinancialReport::findOrFail($id);
        
        if (!$this->canApproveAsAuditor($admin, $report)) {
            return response()->json(['error' => 'You cannot approve this report at auditor level'], 403);
        }
        
        $report->auditor_approval_status = 1;
        $report->auditor_approved_by = $admin->id;
        $report->auditor_approved_at = Carbon::now();
        $report->auditor_approval_remarks = $request->remarks;
        $report->overall_approval_status = 1;
        
        $report->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Report approved by Auditor successfully',
            'report' => $report->load(['auditorApprovedBy:id,user_fullname', 'managementApprovedBy:id,user_fullname'])
        ]);
    }

    /**
     * Reject by Auditor
     */
    public function rejectAuditor(Request $request, $id)
    {
        $admin = Auth::user();
        
        $report = BranchFinancialReport::findOrFail($id);
        
        if (!$this->canApproveAsAuditor($admin, $report)) {
            return response()->json(['error' => 'You cannot reject this report at auditor level'], 403);
        }
        
        $request->validate([
            'remarks' => 'required|string|max:500'
        ]);
        
        $report->auditor_approval_status = 2;
        $report->auditor_approved_by = $admin->id;
        $report->auditor_approved_at = Carbon::now();
        $report->auditor_approval_remarks = $request->remarks;
        $report->overall_approval_status = 3;
        
        $report->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Report rejected by Auditor',
            'report' => $report->load(['auditorApprovedBy:id,user_fullname', 'managementApprovedBy:id,user_fullname'])
        ]);
    }

    /**
     * Approve by Management
     */
    public function approveManagement(Request $request, $id)
    {
        $admin = Auth::user();
        
        $report = BranchFinancialReport::findOrFail($id);
        
        if (!$this->canApproveAsManagement($admin, $report)) {
            return response()->json(['error' => 'You cannot approve this report at management level'], 403);
        }
        
        $report->management_approval_status = 1;
        $report->management_approved_by = $admin->id;
        $report->management_approved_at = Carbon::now();
        $report->management_approval_remarks = $request->remarks;
        $report->overall_approval_status = 2;
        
        $report->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Report approved by Management successfully',
            'report' => $report->load(['auditorApprovedBy:id,user_fullname', 'managementApprovedBy:id,user_fullname'])
        ]);
    }

    /**
     * Reject by Management
     */
    public function rejectManagement(Request $request, $id)
    {
        $admin = Auth::user();
        
        $report = BranchFinancialReport::findOrFail($id);
        
        if (!$this->canApproveAsManagement($admin, $report)) {
            return response()->json(['error' => 'You cannot reject this report at management level'], 403);
        }
        
        $request->validate([
            'remarks' => 'required|string|max:500'
        ]);
        
        $report->management_approval_status = 2;
        $report->management_approved_by = $admin->id;
        $report->management_approved_at = Carbon::now();
        $report->management_approval_remarks = $request->remarks;
        $report->overall_approval_status = 3;
        
        $report->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Report rejected by Management',
            'report' => $report->load(['auditorApprovedBy:id,user_fullname', 'managementApprovedBy:id,user_fullname'])
        ]);
    }

    /**
     * Export to Excel with filters
     */
    public function exportExcel(Request $request)
    {
        $admin = Auth::user();
        $query = BranchFinancialReport::with([
            'branch:id,name,zone_id',
            'zone:id,name',
            'creator',
            'updater',
            'auditorApprovedBy',
            'managementApprovedBy'
        ]);

        $this->applyAccessFilter($query, $admin);

        if ($request->filled('date_range')) {
            $dates = explode(' - ', $request->date_range);
            if (count($dates) == 2) {
                $query->whereDate('report_date', '>=', $dates[0])
                      ->whereDate('report_date', '<=', $dates[1]);
            }
        }

        if ($request->filled('zone_id')) {
            $allowedZones = $this->getAllowedZones($admin);
            $requestedZones = (array) $request->zone_id;
            $filteredZones = array_intersect($requestedZones, $allowedZones);
            if (!empty($filteredZones)) {
                $query->whereIn('zone_id', $filteredZones);
            }
        }

        if ($request->filled('branch_id')) {
            $allowedBranches = $this->getAllowedBranches($admin);
            $requestedBranches = (array) $request->branch_id;
            $filteredBranches = array_intersect($requestedBranches, $allowedBranches);
            if (!empty($filteredBranches)) {
                $query->whereIn('branch_id', $filteredBranches);
            }
        }

        $reports = $query->orderBy('report_date', 'desc')
                        ->orderBy('created_at', 'desc')
                        ->get();

        return Excel::download(
            new FinancialReportExport($reports), 
            'financial-report-' . date('Y-m-d-His') . '.xlsx'
        );
    }

    /**
     * Export to CSV with filters
     */
    public function exportCsv(Request $request)
    {
        $admin = Auth::user();
        $query = BranchFinancialReport::with([
            'branch:id,name,zone_id',
            'zone:id,name',
            'creator',
            'updater',
            'auditorApprovedBy',
            'managementApprovedBy'
        ]);

        $this->applyAccessFilter($query, $admin);

        if ($request->filled('date_range')) {
            $dates = explode(' - ', $request->date_range);
            if (count($dates) == 2) {
                $query->whereDate('report_date', '>=', $dates[0])
                      ->whereDate('report_date', '<=', $dates[1]);
            }
        }

        if ($request->filled('zone_id')) {
            $allowedZones = $this->getAllowedZones($admin);
            $requestedZones = (array) $request->zone_id;
            $filteredZones = array_intersect($requestedZones, $allowedZones);
            if (!empty($filteredZones)) {
                $query->whereIn('zone_id', $filteredZones);
            }
        }

        if ($request->filled('branch_id')) {
            $allowedBranches = $this->getAllowedBranches($admin);
            $requestedBranches = (array) $request->branch_id;
            $filteredBranches = array_intersect($requestedBranches, $allowedBranches);
            if (!empty($filteredBranches)) {
                $query->whereIn('branch_id', $filteredBranches);
            }
        }

        $reports = $query->orderBy('report_date', 'desc')
                        ->orderBy('created_at', 'desc')
                        ->get();

        return Excel::download(
            new FinancialReportExport($reports), 
            'financial-report-' . date('Y-m-d-His') . '.csv'
        );
    }
}