<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BillingListModel extends Model
{
    use HasFactory;
    protected $table = 'billing_list';
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    protected $fillable = [
      'type','paymenttype','location_id','location_name','amt','billno','billdate','user_name','userid',
      'phid','extphid','gender','age','mobile','ptsource','isdcode','dob',
      'email','patientname','patientkey','consultant','consultantkey',
      'referredbykey','referredby','provider','billkey','billtype','tax','opno','receiptno','receivedat','grandtotal',
      'granddiscountvalue','grandprodvalue','paymentinfo',
  ];


}
