<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RefundFormModel extends Model
{
    use HasFactory;

    protected $table = 'hms_refund_form';
     protected $primaryKey = 'ref_id';
    public $incrementing = true;
    protected $keyType = 'int';
    protected $fillable = [
      'ref_zone_id',
      'ref_wife_name',
      'ref_wife_mrd_no',
      'ref_husband_name',
      'ref_husband_mrd_no',
      'ref_service_name',
      'ref_total_bill',
      'ref_expected_request',
      'ref_form_status',
      'ref_counselled_by',
      'ref_final_auth',
      'ref_branch_no',
      'ref_auth_by',
      'ref_patient_ph',
      'ref_approved_by',
      'ref_wife_sign',
      'ref_husband_sign',
      'ref_drsign',
      'ref_admin_sign',
      'ref_zonal_sign',
      'admin_approver',
      'admin_approved_by',
      'admin_approved_at',
      'zonal_approver',
      'zonal_approved_by',
      'zonal_approved_at',
      'audit_approver',
      'audit_approved_by',
      'audit_approved_at',
      'final_approver',
      'final_approved_by',
      'final_approved_at',
      'created_by',
      'status',
      'reject_reason',
  ];

  protected $casts = [
      'ref_wife_sign' => 'array',
      'ref_husband_sign' => 'array',
      'ref_drsign' => 'array',
      'ref_admin_sign' => 'array',
      'ref_zonal_sign' => 'array',
  ];

}
