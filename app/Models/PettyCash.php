<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PettyCash extends Model
{
    use HasFactory;

    protected $table = 'petty_cash';

    protected $casts = [
        'reverse_charge' => 'boolean',
        'attachment_paths' => 'array',
    ];

    protected $fillable = [
        'report_id',
        'expense_date',
        'vendor_id',
        'company_id',
        'zone_id',
        'branch_id',
        'branch_ids',
        'expense_category_id',
        'currency',
        'total_amount',
        'claim_reimbursement',
        'tax_type',
        'supply_kind',
        'gstin',
        'reverse_charge',
        'destination_of_supply',
        'gst_tax_label',
        'sac_hsn',
        'invoice_no',
        'notes',
        'reference_no',
        'attachment_paths',
        'status',
        'created_by',
        'updated_by',
    ];

    public function items()
    {
        return $this->hasMany(PettyCashItem::class, 'petty_cash_id');
    }

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

    public function category()
    {
        return $this->belongsTo(ExpenseCategory::class, 'expense_category_id');
    }
}
