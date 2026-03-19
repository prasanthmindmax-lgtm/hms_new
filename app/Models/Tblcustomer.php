<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\TblBilling;
use App\Models\TblShipping;
use App\Models\TblContact;

class Tblcustomer extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'customer_tbl';

    protected $fillable = [
        'user_id',
        'customer_type',
        'customer_salutation',
        'customer_first_name',
        'customer_last_name',
        'company_name',
        'display_name',
        'email',
        'mobile',
        'work_phone',
        'pan_number',
        'opening_balance',
        'payment_terms',
        'portal_language',
        'website',
        'department',
        'designation',
        'twitter',
        'skype',
        'facebook',
        'remarks',
        'documents',
    ];
    public function billingAddress()
{
    return $this->hasOne(TblBilling::class, 'customer_id');
}

public function shippingAddress()
{
    return $this->hasOne(TblShipping::class, 'customer_id');
}

public function contacts()
{
    return $this->hasMany(TblContact::class, 'customer_id');
}

}
