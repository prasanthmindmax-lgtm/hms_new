<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VehicleInsurance extends Model
{
    use HasFactory;

    protected $table = 'vehicle_insurance_details';
    protected $primaryKey = 'id';
    protected $fillable = ['vehicle_id', 'company_name', 'expiry_date', 'renewal_date', 'policy_details','payment','image_paths'];
   
}
