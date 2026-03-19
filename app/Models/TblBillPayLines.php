<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class TblBillPayLines extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'bill_pay_lines_tbl';
      protected $fillable = [
        'bill_pay_id',
        'bill_id',
        'bill_date',
        'due_date',
        'bill_number',
        'grand_total_amount',
        'balance_amount',
        'payment_date',
        'amount',
        'created_at'
    ];
    public function Bill()
    {
        return $this->belongsTo(Tblbill::class, 'bill_id');
    }
     public function BillLines()
    {
        return $this->hasMany(TblBillLines::class, 'bill_id','bill_id');
    }
     public function aleardypay()
    {
        return $this->hasMany(TblBillPayLines::class, 'bill_id','bill_id');
    }
    public function billPay()
    {
        return $this->belongsTo(TblBillPay::class, 'bill_pay_id');
    }
}
