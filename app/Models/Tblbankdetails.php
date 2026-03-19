<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Tblbankdetails extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'bank_details_tbl';

    protected $fillable = [
        'vendor_id',
        'account_holder_name',
        'bank_name',
        'accont_number',
        'ifsc_code',
        'bank_uploads',

    ];
}
