<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Tblbill extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;


    protected $table = 'bill_tbl';

    protected $fillable = [
        'user_id',
        'vendor_id',
        'quotation_id',
        'purchase_id',
        'vendor_name',
        'zone_id',
        'zone_name',
        'department_id',
        'branch_id',
        'branch_name',
        'company_name',
        'bill_category',
        'company_id',
        'bill_number',
        'bill_gen_number',
        'company_name',
        'order_number',
        'bill_date',
        'due_date',
        'payment_terms',
        'subject',
        'discount_type',
        'discount_percent',
        'discount_tax',
        'discount_amount',
        'adjustment_value',
        'tds_tax_id',
        'tcs_tax_id',
        'tax_type',
        'tax_name',
        'tax_rate',
        'tax_amount',
        'sub_total_amount',
        'adjustment_amount',
        'grand_total_amount',
        'balance_amount',
        'partially_payment',
        'note',
        'documents',
        'edit_history',
        'bill_status',
        'tds_pay_date',
        'tds_challan_no',
        'tds_quarter',
        'tds_paid_status',
        'export_name',
        'export_amount',
        'timeline_date',
        'loading_unloading_name',
        'loading_unloading_amount',
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
        'status',
        'br_nature_account_ids',
        'br_nature_account_names',
    ];
    public function BillLines()
    {
        return $this->hasMany(TblBillLines::class, 'bill_id');
    }
    public function Tblvendor()
    {
        return $this->belongsTo(Tblvendor::class, 'vendor_id');
    }
    public function TblBilling()
    {
        return $this->belongsTo(TblBilling::class, 'vendor_id', 'vendor_id');
    }
    public function Tblbankdetails()
    {
        return $this->belongsTo(Tblbankdetails::class, 'vendor_id', 'vendor_id');
    }
    public function TblCompany()
    {
        return $this->belongsTo(Tblcompany::class, 'company_id');
    }
    public function Purchase()
    {
        return $this->belongsTo(TblPurchaseorder::class, 'purchase_id');
    }
    public function Quotation()
    {
        return $this->belongsTo(TblQuotation::class, 'quotation_id');
    }
    public function BillAgaintsPay()
    {
        return $this->belongsTo(TblQuotation::class, 'quotation_id');
    }
    public function billPayments()
    {
        return $this->hasManyThrough(
            Tblbillpay::class,        // Final target model
            TblBillPayLines::class,   // Intermediate model
            'bill_id',                // Foreign key on TblBillPayLines referencing TblBill
            'id',                     // Foreign key on TblBillPay (primary key)
            'id',                     // Local key on TblBill
            'bill_pay_id'             // Local key on TblBillPayLines referencing TblBillPay
        )->with('billPayLines', 'Neftget'); // <-- this loads lines inside each TblBillPay
    }
    public function TblTDSsection()
    {
        return $this->belongsTo(Tbltdstax::class, 'tds_tax_id');
    }

    public function category()
    {
        return $this->belongsTo(BillCategory::class, 'bill_category');
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }
}
