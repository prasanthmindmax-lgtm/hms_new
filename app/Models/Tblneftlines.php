<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
class Tblneftlines extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'tbl_neft_lines';

    protected $fillable = [
        'neft_id',
        'bill_id',
        'bill_pay_id',
        'bill_pay_lines_id',
        'invoice_amount',
        'already_paid',
        'tds_tax_name',
        'tds_tax_id',
        'tax_amount',
        'gst_name',
        'gst_tax_id',
        'gst_amount',
        'only_payable'
    ];

    // Existing
    public function Neft()
    {
        return $this->belongsTo(Tblneft::class, 'neft_id');
    }

    public function alreadypaid()
    {
        return $this->hasMany(TblBillPayLines::class, 'bill_id','bill_id');
    }

    public function Tblbilllines()
    {
        return $this->hasMany(TblBillLines::class, 'bill_id','bill_id');
    }

    public function Bill()
    {
        return $this->belongsTo(Tblbill::class, 'bill_id', 'id');
    }

}

