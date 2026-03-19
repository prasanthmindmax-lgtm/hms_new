<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class TblgrnLines extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'grn_lines_tbl';
      protected $fillable = [
        'grn_id',
        'item_details',
        'quantity',
        'receivable_quantity',
        'acceptable_quantity',
        'reject_quantity',
        'balance_quantity',
        'created_at'
    ];
}
