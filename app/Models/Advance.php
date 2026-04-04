<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Advance extends Model
{
    use HasFactory;

    protected $table = 'advances';

    protected $fillable = [
        'currency',
        'advance_amount',
        'used_amount',
        'balance_amount',
        'advance_date',
        'reference_no',
        'paid_through',
        'vendor_id',
        'zone_id',
        'branch_id',
        'branch_ids',
        'company_id',
        'report_id',
        'notes',
        'status',
        'created_by',
        'updated_by',
    ];

    public function vendor()
    {
        return $this->belongsTo(Tblvendor::class, 'vendor_id');
    }

    public function company()
    {
        return $this->belongsTo(Tblcompany::class, 'company_id');
    }

    public function zone()
    {
        return $this->belongsTo(TblZonesModel::class, 'zone_id');
    }

    public function branch()
    {
        return $this->belongsTo(TblLocationModel::class, 'branch_id');
    }

    public function report()
    {
        return $this->belongsTo(ExpenseReport::class, 'report_id');
    }

    /**
     * Audit / activity log for this advance (same table as report histories).
     */
    public function pettyCashHistories(): MorphMany
    {
        return $this->morphMany(PettyCashHistory::class, 'historyable');
    }
}
