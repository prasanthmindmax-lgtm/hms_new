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
         'engine_number',
         'chassis_number',
         'fuel_type',
         'created_at',
         'updated_at',
        
        ];
}
