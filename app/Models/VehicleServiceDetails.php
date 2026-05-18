<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VehicleServiceDetails extends Model
{
    use HasFactory;

    protected $table = 'vehicle_service_details';
    protected $primaryKey = 'id';
    protected $fillable = ['vehicle_id', 'last_service', 'last_tyre_change'];
   
}
