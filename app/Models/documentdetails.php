<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class documentdetails extends Model
{
    use HasFactory;

    protected $table = 'hms_document_manage';
    protected $fillable = [
      'zone_id',
     'document_name',
      'document_type',
       'update_document',
        'expire_date',

        ];


}
