<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class doctordetails extends Model
{
    use HasFactory;

    protected $table = 'ref_doctor_details';
    protected $fillable = [
      'doctor_name',
     'empolyee_name',
      'special',
       'hopsital_name',
        'address',
         'city',
         'doc_contact',
         'hpl_contact',
         'image_paths',
         'hospital_link',
	'map_link',
        'userfullname',

        ];


}
