<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class TblBilling extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'billing_address_tbl';

    protected $fillable = [
        'customer_id',
        'vendor_id',
        'attention',
        'address',
        'country',
        'city',
        'state',
        'zip_code',
        'phone',
        'fax'
    ];
}
