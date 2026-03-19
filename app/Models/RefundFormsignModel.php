<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RefundFormsignModel extends Model
{
    use HasFactory;

    protected $table = 'hms_refund_form';
    protected $fillable = [
     'ref_total_bill',
     'ref_expected_request',
     'ref_counselled_by',
     'ref_final_auth',
     'ref_auth_by',
     'ref_branch_no',
     'ref_form_status',
     'ref_approved_by',
     'ref_wife_sign',
     'ref_husband_sign',
     'ref_drsign',
     'ref_cc_sign',
     'ref_admin_sign',
     'status'

       ];

}
