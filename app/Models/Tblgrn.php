<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Tblgrn extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;


    protected $table = 'grn_tbl';

    protected $fillable = [
        'user_id',
        'vendor_id',
        'quotation_id',
        'purchase_id',
        'vendor_name',
        'zone_id',
        'zone_name',
        'branch_id',
        'branch_name',
        'company_name',
        'company_id',
        'grn_number',
        'company_name',
        'order_number',
        'bill_date',
        'due_date',
        'payment_terms',
        'note',
        'qc_ststus',
        'qc_checked_by',
        'documents',
        'status',
    ];
    public function BillLines()
    {
        return $this->hasMany(TblgrnLines::class, 'grn_id');
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
    public function QcCheckedBy()
    {
        return $this->belongsTo(usermanagementdetails::class, 'qc_checked_by');
    }

}
