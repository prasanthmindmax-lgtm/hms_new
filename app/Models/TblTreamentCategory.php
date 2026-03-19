<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TblTreamentCategory extends Model
{
    use HasFactory;

    protected $table = 'tbl_treatment_category';
    protected $fillable = [
        'id',
        'name',
    ];
}
