<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BillingListModelNew extends Model
{
    use HasFactory;

    protected $table = 'billing_list_new';
    protected $primaryKey = 'id';
    protected $keyType = 'int';
    public $incrementing = true;

    protected $fillable = [

        // 📍 Location
        'location_id',
        'location_name',
        'entitylocation',

        // 👤 Billing / Staff
        'receivedby',
        'paymenttype',

        // 💰 Amounts
        'amt',
        'tax',
        'taxable_percentage',
        'tax_amount',
        'tds_percentage',
        'tds_amount',
        'igst_amount',
        'cgst_amount',
        'sgst_amount',

        // 🧾 Bill Info
        'billno',
        'billdate',
        'billtype',
        'billkey',

        // 👤 Patient
        'patientname',
        'patientkey',
        'gender',
        'age',
        'mobile',
        'isdcode',
        'dob',
        'email',
        'ptsource',

        // 🏥 Medical
        'phid',
        'extphid',
        'consultant',
        'consultantkey',
        'provider',
        'referredby',
        'referredbykey',

        // 📦 Item / Dept
        'itemname',
        'dept',
        'subdept',
        'hsn',

        // 📅 Registration
        'registrationdate',

        // 🔧 System
        'created_at',
        'updated_at',
    ];
}
