<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class TblVendortype extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;


    protected $table = 'vendor_type_tbl';

    protected $fillable = [
        'name',
        'user_id',
        'description',
        'created_by',
    ];

}
