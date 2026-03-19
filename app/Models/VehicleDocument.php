<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VehicleDocument extends Model
{
    use HasFactory;

    protected $table = 'vehicle_document_details';
    protected $fillable = [
      'branch',
     'vehicle_type',
     'vehicle_id',
      'make',
       'expire_date',
        'registration_number',
         'engine_number',
         'chassis_number',
         'document_type',
         'document_name',
         'fuel_type',
         'created_at',
         'updated_at',
        
        ];
}
