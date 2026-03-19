<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Tbltcstax extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;


    protected $table = 'tcs_tax_tbl';

    protected $fillable = [
        'tax_name',
        'tax_rate',
        'tax_start_date',
        'tax_end_date',
    ];

}
