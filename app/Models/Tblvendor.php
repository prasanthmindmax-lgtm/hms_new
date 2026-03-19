<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Tblvendor extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;


    protected $table = 'vendor_tbl';

    protected $fillable = [
        'user_id',
        'active_status',
        'vendor_id',
        'vendor_salutation',
        'vendor_first_name',
        'vendor_last_name',
        'company_name',
        'display_name',
        'email',
        'mobile',
        'work_phone',
        'pan_number',
        'pan_upload',
        'gst_number',
        'vendor_type_id',
        'vendor_type_name',
        'reference',
        'opening_balance',
        'payment_terms',
        'portal_language',
        'website',
        'department',
        'designation',
        'twitter',
        'skype',
        'facebook',
        'tds_tax_id',
        'tds_tax_name',
        'tds_amount',
        'remarks',
        'documents',
    ];
    public function billingAddress()
{
    return $this->hasOne(TblBilling::class, 'vendor_id');
}

public function shippingAddress()
{
    return $this->hasOne(TblShipping::class, 'vendor_id');
}

public function contacts()
{
    return $this->hasMany(TblContact::class, 'vendor_id');
}
public function bankdetails()
{
    return $this->hasMany(Tblbankdetails::class, 'vendor_id');
}
public function tdstax()
{
    return $this->belongsTo(Tbltdstax::class, 'tds_tax_id');
}
public function history()
{
    return $this->hasMany(TblVendorHistory::class, 'vendor_id');
}


}
