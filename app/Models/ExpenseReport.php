<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class ExpenseReport extends Model
{
    use HasFactory;

    protected $table = 'expense_reports';

    protected $fillable = [

        'report_id',
        'report_name',
        'business_purpose',
        'start_date',
        'end_date',
        'trip_id',
        'is_active',
        'created_by',
        'status',
        'submitted_at',
        'approved_at',
        'reimbursed_at',
        'approver_name',
    ];

    /**
     * Audit / activity log for this petty cash (expense) report.
     */
    public function pettyCashHistories(): MorphMany
    {
        return $this->morphMany(PettyCashHistory::class, 'historyable');
    }
}
