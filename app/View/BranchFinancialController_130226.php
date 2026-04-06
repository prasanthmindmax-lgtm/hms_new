<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class BranchFinancialController extends Controller
{
    /**
     * Display the main page
     */
    public function index(Request $request)
    {
        $admin = Auth::user();
        $perPage = $request->get('per_page', 10);
        
        // Get zones and locations based on user access
        $zones = DB::table('tblzones')->select('id', 'name')->orderBy('name')->get();
        $locations = DB::table('tbl_locations')->select('id', 'name', 'zone_id')->orderBy('name')->get();
        
        // Get statistics
        $statistics = $this->getStatistics($admin);
        
        // Get reports with pagination
        $reports = $this->getReports($admin, $perPage);
        
        // Calculate summary for footer totals
        $summary = [
            'total_radiant' => $reports->sum('radiant_collection_amount'),
            'total_card' => $reports->sum('actual_card_amount'),
            'total_bank' => $reports->sum('bank_deposit_amount'),
            'total_discount' => $reports->sum('today_discount_amount'),
            'total_cancel' => $reports->sum('cancel_bill_amount'),
            'total_refund' => $reports->sum('refund_bill_amount'),
            'total_pos_refund' => $reports->sum('pos_refund_amount'),
            'total_cash_drawer' => $reports->sum('cash_in_drawer'),
            'report_count' => $reports->count(),
        ];
        
        // If AJAX request, return only the table partial
        if ($request->ajax()) {
            return view('branch.partials.financial-table', compact('reports', 'summary'))->render();
        }
        
        return view('branch.branch-financial-index', compact('admin', 'zones', 'locations', 'statistics', 'reports', 'summary'));
    }
    
    /**
     * Get statistics for cards
     */
    private function getStatistics($user)
    {
        $baseQuery = DB::table('branch_financial_reports');
        
        return [
            'total_reports' => $baseQuery->count(),
            'today_reports' => (clone $baseQuery)->whereDate('report_date', today())->count(),
            'this_month' => (clone $baseQuery)->whereMonth('created_at', now()->month)->count(),
            'pending_review' => (clone $baseQuery)->where('edit_count', 0)->count(),
            'total_radiant' => (clone $baseQuery)->sum('radiant_collection_amount'),
            'total_bank' => (clone $baseQuery)->sum('bank_deposit_amount'),
        ];
    }
    
    /**
     * Get reports list
     */
    private function getReports($user, $perPage = 10)
    {
        $query = DB::table('branch_financial_reports as bfr')
            ->leftJoin('users as creator', 'bfr.created_by', '=', 'creator.id')
            ->leftJoin('users as updater', 'bfr.updated_by', '=', 'updater.id')
            ->select(
                'bfr.*',
                'creator.user_fullname as created_by_name',
                'updater.user_fullname as updated_by_name'
            )
            ->where('bfr.created_by', $user->id)
            ->orderBy('bfr.created_at', 'desc');
        
        return $query->paginate($perPage);
    }
    
    /**
     * Store new report
     */
    public function store(Request $request)
    {
        $request->validate([
            'report_date' => 'required|date',
            'zone_name' => 'required|string',
            'zone_id' => 'required|integer',
            'branch_name' => 'required|string',
            'branch_id' => 'required|integer',
            'acknowledgement_agreed' => 'required|accepted',
        ]);
        
        // Handle file uploads
        $radiantFiles = $this->uploadFiles($request, 'radiant_collection_files');
        $actualCardFiles = $this->uploadFiles($request, 'actual_card_files');
        $bankDepositFiles = $this->uploadFiles($request, 'bank_deposit_files');
        $cashierInfoFiles = $this->uploadFiles($request, 'cashier_info_files');
        $additionalAmountsFiles = $this->uploadFiles($request, 'additional_amounts_files');
        
        // Prepare data
        $data = [
            'report_date' => $request->report_date,
            'zone_name' => $request->zone_name,
            'zone_id' => $request->zone_id,
            'branch_name' => $request->branch_name,
            'branch_id' => $request->branch_id,
            
            'radiant_collected_date' => $request->radiant_collected_date,
            'radiant_collection_amount' => $request->radiant_collection_amount ?? 0,
            'radiant_collection_files' => json_encode($radiantFiles),
            
            'actual_card_amount' => $request->actual_card_amount ?? 0,
            'actual_card_files' => json_encode($actualCardFiles),
            
            'bank_deposit_amount' => $request->bank_deposit_amount ?? 0,
            'bank_deposit_files' => json_encode($bankDepositFiles),
            
            'placed_by_whom' => $request->placed_by_whom,
            'locker_by_whom' => $request->locker_by_whom,
            'who_gave_radiant_cash' => $request->who_gave_radiant_cash,
            'cash_in_drawer' => $request->cash_in_drawer ?? 0,
            'cashier_info_files' => json_encode($cashierInfoFiles),
            
            'today_discount_amount' => $request->today_discount_amount ?? 0,
            'cancel_bill_amount' => $request->cancel_bill_amount ?? 0,
            'refund_bill_amount' => $request->refund_bill_amount ?? 0,
            'pos_refund_amount' => $request->pos_refund_amount ?? 0,
            'additional_amounts_files' => json_encode($additionalAmountsFiles),
            
            'acknowledgement_agreed' => $request->acknowledgement_agreed ? 1 : 0,
            
            'created_by' => Auth::id(),
            'edit_count' => 0,
            'edit_history' => json_encode([]),
            'created_at' => now(),
            'updated_at' => now(),
        ];
        
        $id = DB::table('branch_financial_reports')->insertGetId($data);
        
        // Get the created record
        $record = DB::table('branch_financial_reports')->where('id', $id)->first();
        
        return response()->json([
            'success' => true,
            'message' => 'Financial report saved successfully',
            'data' => $record,
            'files' => [
                'radiant_collection' => $radiantFiles,
                'actual_card' => $actualCardFiles,
                'bank_deposit' => $bankDepositFiles,
                'cashier_info' => $cashierInfoFiles,
                'additional_amounts' => $additionalAmountsFiles,
            ]
        ]);
    }
    
    /**
     * Update existing report
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'acknowledgement_agreed' => 'required|accepted',
        ]);
        
        $existing = DB::table('branch_financial_reports')->where('id', $id)->first();
        
        if (!$existing) {
            return response()->json(['success' => false, 'message' => 'Report not found'], 404);
        }
        
        // Handle file uploads
        $radiantFiles = $this->uploadFiles($request, 'radiant_collection_files');
        $actualCardFiles = $this->uploadFiles($request, 'actual_card_files');
        $bankDepositFiles = $this->uploadFiles($request, 'bank_deposit_files');
        $cashierInfoFiles = $this->uploadFiles($request, 'cashier_info_files');
        $additionalAmountsFiles = $this->uploadFiles($request, 'additional_amounts_files');
        
        // Merge with existing files if no new files uploaded
        if (empty($radiantFiles)) {
            $radiantFiles = json_decode($existing->radiant_collection_files, true) ?? [];
        }
        if (empty($actualCardFiles)) {
            $actualCardFiles = json_decode($existing->actual_card_files, true) ?? [];
        }
        if (empty($bankDepositFiles)) {
            $bankDepositFiles = json_decode($existing->bank_deposit_files, true) ?? [];
        }
        if (empty($cashierInfoFiles)) {
            $cashierInfoFiles = json_decode($existing->cashier_info_files, true) ?? [];
        }
        if (empty($additionalAmountsFiles)) {
            $additionalAmountsFiles = json_decode($existing->additional_amounts_files, true) ?? [];
        }
        
        // Update edit history
        $editHistory = json_decode($existing->edit_history, true) ?? [];
        $editHistory[] = [
            'edited_by' => Auth::id(),
            'edited_by_name' => Auth::user()->user_fullname ?? Auth::user()->name,
            'edited_at' => now()->toDateTimeString(),
        ];
        
        // Update data
        $data = [
            'report_date' => $request->report_date,
            'zone_name' => $request->zone_name,
            'zone_id' => $request->zone_id,
            'branch_name' => $request->branch_name,
            'branch_id' => $request->branch_id,
            
            'radiant_collected_date' => $request->radiant_collected_date,
            'radiant_collection_amount' => $request->radiant_collection_amount ?? 0,
            'radiant_collection_files' => json_encode($radiantFiles),
            
            'actual_card_amount' => $request->actual_card_amount ?? 0,
            'actual_card_files' => json_encode($actualCardFiles),
            
            'bank_deposit_amount' => $request->bank_deposit_amount ?? 0,
            'bank_deposit_files' => json_encode($bankDepositFiles),
            
            'placed_by_whom' => $request->placed_by_whom,
            'locker_by_whom' => $request->locker_by_whom,
            'who_gave_radiant_cash' => $request->who_gave_radiant_cash,
            'cash_in_drawer' => $request->cash_in_drawer ?? 0,
            'cashier_info_files' => json_encode($cashierInfoFiles),
            
            'today_discount_amount' => $request->today_discount_amount ?? 0,
            'cancel_bill_amount' => $request->cancel_bill_amount ?? 0,
            'refund_bill_amount' => $request->refund_bill_amount ?? 0,
            'pos_refund_amount' => $request->pos_refund_amount ?? 0,
            'additional_amounts_files' => json_encode($additionalAmountsFiles),
            
            'acknowledgement_agreed' => $request->acknowledgement_agreed ? 1 : 0,
            
            'updated_by' => Auth::id(),
            'edit_count' => $existing->edit_count + 1,
            'edit_history' => json_encode($editHistory),
            'updated_at' => now(),
        ];
        
        DB::table('branch_financial_reports')->where('id', $id)->update($data);
        
        // Get updated record
        $record = DB::table('branch_financial_reports')->where('id', $id)->first();
        
        return response()->json([
            'success' => true,
            'message' => 'Financial report updated successfully',
            'data' => $record,
            'files' => [
                'radiant_collection' => $radiantFiles,
                'actual_card' => $actualCardFiles,
                'bank_deposit' => $bankDepositFiles,
                'cashier_info' => $cashierInfoFiles,
                'additional_amounts' => $additionalAmountsFiles,
            ]
        ]);
    }
    
    /**
     * Get single report for editing
     */
    public function show($id)
    {
        $report = DB::table('branch_financial_reports')->where('id', $id)->first();
        
        if (!$report) {
            return response()->json(['success' => false, 'message' => 'Report not found'], 404);
        }
        
        return response()->json([
            'success' => true,
            'data' => $report
        ]);
    }
    
    /**
     * Delete report
     */
    public function destroy($id)
    {
        DB::table('branch_financial_reports')->where('id', $id)->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Report deleted successfully'
        ]);
    }
    
    /**
     * Upload multiple files
     */
    private function uploadFiles($request, $fieldName)
    {
        $files = [];
        
        if ($request->has($fieldName)) {
            $uploadedFiles = $request->file($fieldName);
            if (!is_array($uploadedFiles)) {
                $uploadedFiles = [$uploadedFiles];
            }
            
            foreach ($uploadedFiles as $file) {
                if ($file && $file->isValid()) {
                    $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                    $file->move(public_path('branch_financial_files'), $filename);
                    $files[] = 'branch_financial_files/' . $filename;
                }
            }
        }
        
        return $files;
    }
}