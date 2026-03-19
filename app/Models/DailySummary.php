<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailySummary extends Model
{
    use HasFactory;

    protected $table = 'daily_summary';
    protected $fillable = [
      'type',
     'paymenttype',
     'amt',
     'billno',
      'billdate',
       'user',
        'userid',
         'phid',
         'gender',
         'age',
         'mobile',
         'ptsource',
         'isdcode',
         'dob',
         'email',
         'patientname',
         'grandprodvalue',
         'grandtax',
         'granddiscountvalue',
         'discountamt',
         'grandtotal',
         'consultant',
         'consultantkey',
         'referredbykey',
         'referredby',
         'provider',
         'billtype',
         'patientkey',
         'billkey',
         'opno',
         'advances_amt',
         'receiptno',
         'receivedby',
         'receivedbyid',
         'ipno',
         'total_amt',
        
        ];
}
