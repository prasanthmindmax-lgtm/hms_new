<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class TblQuotation extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;


    protected $table = 'quotation_order_tbl';

    protected $fillable = [
        'user_id',
        'vendor_id',
        'vendor_name',
        'zone_id',
        'zone_name',
        'branch_id',
        'branch_name',
        'company_name',
        'company_id',
        'quotation_no',
        'quotation_gen_no',
        'delivery_address',
        'delivery_id',
        'company_name',
        'order_number',
        'bill_date',
        'due_date',
        'payment_terms',
        'subject',
        'discount_percent',
        'discount_type',
        'discount_amount',
        'discount_tax',
        'adjustment_value',
        'adjustment_reason',
        'tds_tax_id',
        'tcs_tax_id',
        'tax_type',
        'tax_name',
        'tax_rate',
        'tax_amount',
        'export_name',
        'export_amount',
        'sub_total_amount',
        'adjustment_amount',
        'grand_total_amount',
        'balance_amount',
        'note',
        'esi_type',
        'esi_value',
        'esi_amount',
        'pf_type',
        'pf_value',
        'pf_amount',
        'other_type',
        'other_value',
        'other_amount',
        'other_reason',
        'documents',
        'status',
        'edit_history',
    ];
    public function BillLines()
    {
        return $this->hasMany(TblQuotationLines::class, 'quotation_id');
    }
    public function Tblvendor()
    {
        return $this->belongsTo(Tblvendor::class, 'vendor_id');
    }
    public function TblBilling()
    {
        return $this->belongsTo(TblBilling::class, 'vendor_id');
    }
    public function TblCompany()
    {
        return $this->belongsTo(Tblcompany::class, 'company_id');
    }


}
