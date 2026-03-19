<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class TblContact extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'contact_tbl';
      protected $fillable = [
        'customer_id',
        'vendor_id',
        'salutation',
        'first_name',
        'last_name',
        'email',
        'work_phone',
        'mobile',
        'created_at'
    ];
}
