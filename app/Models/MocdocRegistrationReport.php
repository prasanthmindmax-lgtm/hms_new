<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MocdocRegistrationReport extends Model
{
    protected $table = 'mocdoc_registration_reports';

    protected $fillable = [
        'phid',
        'prefix',
        'name',
        'mobile',
        'gender',
        'age',
        'area',
        'reg_date',
        'synced_at',
    ];

    protected $casts = [
        'reg_date'  => 'date',
        'synced_at' => 'datetime',
    ];
}
