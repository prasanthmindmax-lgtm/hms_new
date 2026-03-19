<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class securitydetailsModel extends Model
{
    use HasFactory;

    protected $table = 'security_details';
    protected $fillable = [
    'zone_id',
    'sec_name',
    'sec_phone',
    'sec_address',
    'sec_shift',
    'sec_joining_date',
    'sec_id_proof',
    'status'
 ];

}
