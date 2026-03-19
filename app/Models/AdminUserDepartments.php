<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminUserDepartments extends Model
{
    use HasFactory;

    protected $table = 'admin_user_departments';

    protected $fillable = [
        'user_id',
        'depart_id'
    ];
}
