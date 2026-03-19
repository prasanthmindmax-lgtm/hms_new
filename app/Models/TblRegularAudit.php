<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class TblRegularAudit extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'tbl_regular_audit';

    protected $fillable = [
        'date_of_procedure',
        'branch',
        'mrd_number',
        'patient_details',
        'wife_name',
        'wife_age',
        'husband_name',
        'husband_age',
        'comprehensive',
        'legal_fees',
        'days_of_injection',
        'injection_used',
        'value',
        'mention_if_done_verified',
        'date_of_freezing',
        'straw_detach',
        'cost_of_paid',
        'split_up',
        'paid_details',
        'd_o_r',
        'fet_paid_details',
        'legal_charges',
        'spl_media_charges',
        'D5_blast',
        'macs',
        'microflidises',
        'ds',
        'tesa',
        'seman_freezing',
        'pgs_non_inv',
        'pgs_inv',
        'phd_non_inv',
        'phd_inv',
        'scopy_details',
        'ip_bill',
        'due_date',
        'package_fixed',
        'package_paid',
        'ip',
        'remarks',
        'co_user_dt',
        'co_user_name',
        'co_user',
        'cc_audit_id',
        'credit_provider',
        'purpose',
        'natureofvisit',
        'unregistered_dr',
        'referredbykey',
        'referred_by',
        'token',
        'createdby',
        'createdby_name',
        'checkin_date',
        'start',
        'bookeddr',
        'bookeddr_name',
        'consultingdr',
        'consultingdr_name',
        'entitykey',
        'apptkey',
        'entitylocation',
        'branch',
        'opno',
        'hadmlc',
        'hadfood',
        'dr_dept',
        'type',
        'created_at',
        'updated_at'
    ];

}
