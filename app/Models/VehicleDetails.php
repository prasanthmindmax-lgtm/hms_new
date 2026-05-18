<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VehicleDetails extends Model
{
  use HasFactory;

  protected $table = 'vehicle_details';
  protected $fillable = [
    'branch',
    'vehicle_no',
    'zone_id',
    'vehicle_type',
    'make',
    'year_of_manufacture',
    'registration_number',
    'registration_owner',
    'rto_location',
    'engine_number',
    'chassis_number',
    'fuel_type',
    'cluster_name',
    'vehicle_number',
    'vehicle_incharge',
    'vehicle_incharge_admin',
    'gts_installed',
    'gts_status',
    'insurance_expiry_date',
    'created_at',
    'updated_at',

  ];

  // public function insuranceDetails(){
  //   return $this->hasOne(VehicleInsurance::class, 'vehicle_id','id');
  // }
  public function insuranceDetails(){
    return $this->hasOne(VehicleInsurance::class, 'vehicle_id', 'id')
                ->latestOfMany('updated_at');
  }




  public function vehicleType(){
    return $this->hasOne(VehicleType::class, 'id','vehicle_type');
  }
  public function location(){
    return $this->hasOne(TblLocationModel::class, 'id', 'branch'); // assuming primary key in tbl_locations is zone_id
  }

  public function serviceDetails(){
    return $this->hasOne(VehicleServiceDetails::class, 'vehicle_id','id');
  }
}