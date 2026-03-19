<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TblBranch extends Model
{
    use HasFactory;

    protected $table = 'tbl_branch';
    protected $fillable = [
        'name',
        'location_id',
    ];
}
