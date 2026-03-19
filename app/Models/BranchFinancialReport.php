<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BranchFinancialReport extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'branch_financial_reports';

    /**
     * The primary key associated with the table.
     */
    protected $primaryKey = 'id';

    /**
     * Indicates if the model should be timestamped.
     */
    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'branch_id',
        'zone_id',
        'report_date',
        
        // Radiant Collection - UPDATED FIELDS
        'radiant_collection_from_date',
        'radiant_collection_to_date',
        'radiant_collected_date',
        'radiant_collection_amount',
        'radiant_collection_files',
        'radiant_not_collected',
        'radiant_not_collected_remarks',
        
        // Deposit Section - NEW FIELDS
        'deposit_date',
        'deposit_amount',
        'deposit_files',
        
        // Collection amounts
        'actual_card_amount',
        'actual_card_files',
        'bank_deposit_amount',
        'bank_deposit_files',
        
        // UPI Section - NEW FIELDS
        'upi_amount',
        'upi_files',
        
        // Deductions
        'today_discount_amount',
        'cancel_bill_amount',
        'refund_bill_amount',
        
        // Status fields
        'placed_by',
        'placed_at',
        'placed_by_whom',
        'locker_by',
        'locker_at',
        'locker_by_whom',
        'radiant_given_by',
        'radiant_given_at',
        'who_gave_radiant_cash',
        
        // Approval fields - Auditor
        'auditor_approval_status',
        'auditor_approved_by',
        'auditor_approved_at',
        'auditor_approval_remarks',
        
        // Approval fields - Management
        'management_approval_status',
        'management_approved_by',
        'management_approved_at',
        'management_approval_remarks',
        
        // Overall status
        'overall_approval_status',
        
        // Tracking
        'created_by',
        'updated_by',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'report_date' => 'date',
        'radiant_collected_date' => 'date',
        'radiant_collection_from_date' => 'date',
        'radiant_collection_to_date' => 'date',
        'deposit_date' => 'date',
        
        'radiant_collection_amount' => 'decimal:2',
        'actual_card_amount' => 'decimal:2',
        'bank_deposit_amount' => 'decimal:2',
        'deposit_amount' => 'decimal:2',
        'upi_amount' => 'decimal:2',
        'today_discount_amount' => 'decimal:2',
        'cancel_bill_amount' => 'decimal:2',
        'refund_bill_amount' => 'decimal:2',
        
        'radiant_not_collected' => 'boolean',
        
        'auditor_approval_status' => 'integer',
        'management_approval_status' => 'integer',
        'overall_approval_status' => 'integer',
        
        'placed_at' => 'datetime',
        'locker_at' => 'datetime',
        'radiant_given_at' => 'datetime',
        'auditor_approved_at' => 'datetime',
        'management_approved_at' => 'datetime',
        
        // JSON ARRAYS - THIS FIXES THE count() ERROR
        'radiant_collection_files' => 'array',
        'actual_card_files' => 'array',
        'bank_deposit_files' => 'array',
        'deposit_files' => 'array',
        'upi_files' => 'array',
        
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the branch that owns the report.
     */
    public function branch()
    {
        return $this->belongsTo(TblLocationModel::class, 'branch_id', 'id');
    }

    /**
     * Get the zone that owns the report.
     */
    public function zone()
    {
        return $this->belongsTo(TblZonesModel::class, 'zone_id', 'id');
    }
    
    /**
     * Get zone name attribute
     */
    public function getZoneNameAttribute()
    {
        return optional($this->zone)->name;
    }
    
    /**
     * Get branch name attribute
     */
    public function getBranchNameAttribute()
    {
        return optional($this->branch)->name;
    }

    /**
     * Get the usermanagementdetails who created the report.
     */
    public function creator()
    {
        return $this->belongsTo(usermanagementdetails::class, 'created_by', 'id');
    }

    /**
     * Get the usermanagementdetails who last updated the report.
     */
    public function updater()
    {
        return $this->belongsTo(usermanagementdetails::class, 'updated_by', 'id');
    }

    /**
     * Get the usermanagementdetails who placed the report.
     */
    public function placedBy()
    {
        return $this->belongsTo(usermanagementdetails::class, 'placed_by_whom', 'username');
    }

    /**
     * Get the usermanagementdetails who locked the report.
     */
    public function lockerBy()
    {
        return $this->belongsTo(usermanagementdetails::class, 'locker_by_whom', 'username');
    }

    /**
     * Get the usermanagementdetails who gave radiant.
     */
    public function radiantGivenBy()
    {
        return $this->belongsTo(usermanagementdetails::class, 'who_gave_radiant_cash', 'username');
    }

    /**
     * Get the auditor who approved/rejected the report.
     */
    public function auditorApprovedBy()
    {
        return $this->belongsTo(usermanagementdetails::class, 'auditor_approved_by', 'id');
    }

    /**
     * Get the management usermanagementdetails who approved/rejected the report.
     */
    public function managementApprovedBy()
    {
        return $this->belongsTo(usermanagementdetails::class, 'management_approved_by', 'id');
    }

    /**
     * Scope a query to filter by branch.
     */
    public function scopeByBranch($query, $branchId)
    {
        return $query->where('branch_id', $branchId);
    }

    /**
     * Scope a query to filter by zone.
     */
    public function scopeByZone($query, $zoneId)
    {
        return $query->where('zone_id', $zoneId);
    }

    /**
     * Scope a query to filter by date range.
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereDate('report_date', '>=', $startDate)
                     ->whereDate('report_date', '<=', $endDate);
    }

    /**
     * Scope a query to filter by approval status.
     */
    public function scopeByApprovalStatus($query, $status)
    {
        return $query->where('overall_approval_status', $status);
    }

    /**
     * Get the total collection amount.
     * UPDATED: Now includes deposit and UPI amounts
     */
    public function getTotalCollectionAttribute()
    {
        return $this->radiant_collection_amount + 
               $this->actual_card_amount + 
               $this->bank_deposit_amount +
               $this->deposit_amount +
               $this->upi_amount;
    }

    /**
     * Get the total deductions amount.
     */
    public function getTotalDeductionsAttribute()
    {
        return $this->today_discount_amount + 
               $this->cancel_bill_amount + 
               $this->refund_bill_amount;
    }

    /**
     * Get the net amount.
     */
    public function getNetAmountAttribute()
    {
        return $this->total_collection - $this->total_deductions;
    }

    /**
     * Get the approval status label for auditor.
     */
    public function getAuditorApprovalStatusLabelAttribute()
    {
        $statuses = [
            0 => 'Pending',
            1 => 'Approved',
            2 => 'Rejected',
        ];

        return $statuses[$this->auditor_approval_status] ?? 'Unknown';
    }

    /**
     * Get the approval status label for management.
     */
    public function getManagementApprovalStatusLabelAttribute()
    {
        $statuses = [
            0 => 'Pending',
            1 => 'Approved',
            2 => 'Rejected',
        ];

        return $statuses[$this->management_approval_status] ?? 'Unknown';
    }

    /**
     * Get the overall approval status label.
     */
    public function getOverallApprovalStatusLabelAttribute()
    {
        $statuses = [
            0 => 'Pending',
            1 => 'Partially Approved',
            2 => 'Fully Approved',
            3 => 'Rejected',
        ];

        return $statuses[$this->overall_approval_status] ?? 'Unknown';
    }

    /**
     * Get the approval status badge class for auditor.
     */
    public function getAuditorApprovalBadgeClassAttribute()
    {
        $classes = [
            0 => 'badge-warning',
            1 => 'badge-success',
            2 => 'badge-danger',
        ];

        return $classes[$this->auditor_approval_status] ?? 'badge-secondary';
    }

    /**
     * Get the approval status badge class for management.
     */
    public function getManagementApprovalBadgeClassAttribute()
    {
        $classes = [
            0 => 'badge-warning',
            1 => 'badge-success',
            2 => 'badge-danger',
        ];

        return $classes[$this->management_approval_status] ?? 'badge-secondary';
    }

    /**
     * Get the overall approval status badge class.
     */
    public function getOverallApprovalBadgeClassAttribute()
    {
        $classes = [
            0 => 'badge-warning',
            1 => 'badge-info',
            2 => 'badge-success',
            3 => 'badge-danger',
        ];

        return $classes[$this->overall_approval_status] ?? 'badge-secondary';
    }

    /**
     * Check if report is pending auditor approval.
     */
    public function isPendingAuditorApproval()
    {
        return $this->auditor_approval_status === 0;
    }

    /**
     * Check if report is approved by auditor.
     */
    public function isApprovedByAuditor()
    {
        return $this->auditor_approval_status === 1;
    }

    /**
     * Check if report is rejected by auditor.
     */
    public function isRejectedByAuditor()
    {
        return $this->auditor_approval_status === 2;
    }

    /**
     * Check if report is pending management approval.
     */
    public function isPendingManagementApproval()
    {
        return $this->management_approval_status === 0;
    }

    /**
     * Check if report is approved by management.
     */
    public function isApprovedByManagement()
    {
        return $this->management_approval_status === 1;
    }

    /**
     * Check if report is rejected by management.
     */
    public function isRejectedByManagement()
    {
        return $this->management_approval_status === 2;
    }

    /**
     * Check if report is fully approved.
     */
    public function isFullyApproved()
    {
        return $this->overall_approval_status === 2;
    }

    /**
     * Check if report is rejected.
     */
    public function isRejected()
    {
        return $this->overall_approval_status === 3;
    }
}