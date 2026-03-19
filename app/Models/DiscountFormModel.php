<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DiscountFormModel extends Model
{
    use HasFactory;

    protected $table = 'hms_discount_form';
    protected $primaryKey = 'dis_id';
    public $incrementing = true;
    protected $keyType = 'int';
    protected $fillable = [
     'dis_zone_id',
     'dis_wife_name',
     'dis_wife_mrd_no',
     'dis_husband_name',
     'dis_husband_mrd_no',
     'dis_service_name',
     'dis_total_bill',
     'dis_expected_request',
     'dis_post_discount',
     'dis_counselled_by',
     'dis_counselled_by_include',
     'dis_counselled_by_not_include',
     'dis_patient_ph',
     'dis_final_auth',
     'dis_auth_by',
     'dis_approved_by',
     'dis_branch_no',
     'dis_form_status',
     'dis_wife_sign',
     'dis_husband_sign',
     'dis_drsign',
     'dis_cc_sign',
     'dis_admin_sign',
     'dis_attachments',
     'created_by',
     'status',
     'reject_reason'
       ];
}
