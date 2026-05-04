<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MocdocCheckinReport extends Model
{
    protected $table = 'mocdoc_checkin_reports';

    protected $fillable = [
        'checkinkey',
        'phid',
        'checkin_date',
        'checkin_time',
        'patient_name',
        'mobile',
        'dob',
        'age',
        'gender',
        'purpose',
        'ptsource',
        'city',
        'state',
        'bookeddr_name',
        'visittype',
        'opno',
        'mocdoc_location_key',
        'mocdoc_location_name',
        'synced_at',
    ];

    protected $casts = [
        'checkin_date' => 'date',
        'dob'          => 'date',
        'synced_at'    => 'datetime',
    ];
}
