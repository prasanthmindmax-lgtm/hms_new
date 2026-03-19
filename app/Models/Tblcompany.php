<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Tblcompany extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;


    protected $table = 'company_tbl';

    protected $fillable = [
        'company_name',
        'reg_number',
        'address',
        'email',
        'phone',
        'gst_number',
        'website',
        'city',
        'state',
        'country',
        'zip_code',
        'logo_upload',
        'created_by',
    ];

}
