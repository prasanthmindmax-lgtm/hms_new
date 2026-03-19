<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class TblPoEmail extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;


    protected $table = 'po_email_tbl';

    protected $fillable = [
        'email',
        'user_id',
        'created_by',
    ];

}
