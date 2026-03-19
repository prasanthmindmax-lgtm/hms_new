<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Tblneft extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'tbl_neft_payment';

    protected $fillable = [
        'serial_number',
        'user_id',
        'bill_id',
        'bill_pay_id',
        'created_by',
        'branch_id',
        'branch_name',
        'zone_id',
        'zone_name',
        'company_id',
        'company_name',
        'vendor_id',
        'vendor',
        'nature_payment',
        'payment_status',
        'payment_method',
        'utr_number',
        'pan_number',
        'pan_upload',
        'account_number',
        'ifsc_code',
        'invoice_upload',
        'invoice_amount',
        'bank_upload',
        'po_upload',
        'po_signed_upload',
        'po_delivery_upload',
        'edit_history',
        'checker_status',
        'approval_status',
        'delete_status',
        'bill_date',
    ];
    public function BillLines()
    {
        return $this->hasMany(Tblneftlines::class, 'neft_id');
    }
    public function Tblvendor()
    {
        return $this->belongsTo(Tblvendor::class, 'vendor_id');
    }
    public function Tblbankdetails()
    {
        return $this->hasMany(Tblbankdetails::class, 'vendor_id','vendor_id');
    }
    public function Tblbillpay()
    {
        return $this->belongsTo(Tblbillpay::class, 'bill_pay_id');
    }


}
