<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Tblbillpay extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;


    protected $table = 'bill_pay_tbl';
    
    protected $primaryKey = 'id';

    public $incrementing = true;

    protected $keyType = 'int';

    public $timestamps = true;

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
        'bank_statement_id',
        'bank_statement_status',
        'payment',
        'payment_gen_order',
        'payment_made',
        'payment_date',
        'payment_mode',
        'paid_through',
        'reference',
        'remark',
        'note',
        'amount_paid',
        'amount_used',
        'amount_refunded',
        'amount_excess',
        'save_status',
        'documents',
    ];
    public function BillLines()
    {
        return $this->hasMany(TblBillPayLines::class, 'bill_pay_id');
    }

    public function Tblbankdetails()
    {
        // return $this->hasMany(Tblbankdetails::class, 'vendor_id');
        return $this->hasMany(Tblbankdetails::class, 'vendor_id', 'vendor_id');

    }
    public function Tblvendor()
    {
        return $this->belongsTo(Tblvendor::class, 'vendor_id');
    }
    public function TblBilling()
    {
        return $this->belongsTo(TblBilling::class, 'vendor_id');
    }
    public function billPayLines()
    {
        return $this->hasMany(TblBillPayLines::class, 'bill_pay_id', 'id');
    }
    public function Neftget()
    {
        return $this->hasMany(Tblneft::class, 'bill_pay_id', 'id');
    }
    public function bankStatement()
    {
        return $this->belongsTo(BankStatement::class, 'bank_statement_id', 'id')
                    ->select('id', 'description');
    }
    public function TblCompany()
    {
        return $this->belongsTo(Tblcompany::class, 'company_id');
    }

  



}
