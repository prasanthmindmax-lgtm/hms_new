<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CancelbillFormModel extends Model
{
    use HasFactory;
    protected $table = 'hms_cancelbill_form';
    protected $primaryKey = 'can_bill_no';
    protected $keyType = 'string';
    protected $fillable = [
     'can_zone_id' ,
    'can_op_no',
    'can_token_no' ,
    'can_bill_no' ,
    'can_consultant',
    'can_date',
    'can_name' ,
    'can_mrdno' ,
    'can_age' ,
    'can_gender' ,
    'can_mobile' ,
    'can_payment_type' ,
    'can_payment_details' ,
    'can_form_status' ,
    'can_sno' ,
    'can_particulars' ,
    'can_qty',
    'can_rate',
    'can_tax',
    'can_amount',
    'can_total',
    'can_previous_alance',
    'can_amount_receivable',
    'can_amount_received',
    'can_advance',
    'can_amount_word',
    'can_advance_word',
    'can_prepared_by',
    'can_reason',
    'can_zonal_sign',
    'can_admin_sign',
    'created_by',
     'status',
     'reject_reason'
       ];
}
