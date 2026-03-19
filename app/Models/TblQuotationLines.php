<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class TblQuotationLines extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'quotation_lines_tbl';
      protected $fillable = [
        'quotation_id',
        'item_details',
        'account',
        'account_id',
        'quantity',
        'rate',
        'customer',
        'gst_type',
        'gst_name',
        'gst_rate',
        'gst_tax_id',
        'cgst_amount',
        'sgst_amount',
        'gst_amount',
        'amount',
        'created_at'
    ];
}
