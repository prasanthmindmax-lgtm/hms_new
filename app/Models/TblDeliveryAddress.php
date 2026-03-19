<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class TblDeliveryAddress extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;


    protected $table = 'delivery_address_tbl';

    protected $fillable = [
        'address',
        'created_by',
    ];
    public function user()
    {
        return $this->belongsTo(usermanagementdetails::class, 'created_by', 'id');
    }

}
