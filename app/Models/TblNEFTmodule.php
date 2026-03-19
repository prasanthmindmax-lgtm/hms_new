<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class TblNEFTmodule extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'tbl_sample';

    protected $fillable = [
        'serial_number',
        'created_by',
        'vendor',
        'description',
        'neft_amount',
        'pan_number',
        'pan_upload',
        'account_number',
        'ifsc_code',
        'invoice_amount',
        'invoice_number',
        'invoice_upload',
        'bank_upload',
        'aressio_paid',
        'already_paid'  
    ];

}
