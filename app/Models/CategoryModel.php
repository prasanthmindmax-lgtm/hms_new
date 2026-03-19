<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoryModel extends Model
{
    use HasFactory;

    protected $table = 'tbl_user_departments';
    protected $fillable = [
        'depart_name',
        'dept_status'
    ];
}
