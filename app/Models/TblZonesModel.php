<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TblZonesModel extends Model
{
    use HasFactory;

    protected $table = 'tblzones';
    protected $fillable = [
        'id',
        'name',
    ];
}
