<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VehicleActivities extends Model
{
    use HasFactory;

    protected $table = 'vehicle_activities';
    protected $primaryKey = 'id';

}
