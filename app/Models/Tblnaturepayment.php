<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Tblnaturepayment extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;


    protected $table = 'nature_of_payment_tbl';

    protected $fillable = [
        'name',
        'user_id',
        'description',
        'created_by',
    ];

}
