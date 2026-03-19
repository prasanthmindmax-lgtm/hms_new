<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VehicleType extends Model
{
    use HasFactory;

    protected $table = 'vehicle_type';
    protected $fillable = [
      'type',
     'status',
         'created_at',
         'updated_at',
        
        ];
}
