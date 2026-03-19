<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TblUserDepartments extends Model
{
    use HasFactory;

    protected $table = 'tbl_department_user';

    protected $fillable = [
        'admin_user_departments_id',
        'user_id',
        'zone_id',
        'depart_id'
    ];
}
