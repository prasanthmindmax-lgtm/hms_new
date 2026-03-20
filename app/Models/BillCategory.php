<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BillCategory extends Model
{
    use HasFactory;

    protected $table = 'bill_categories';

    protected $fillable = [
        'name',
        'is_active',
        'created_by',
    ];
}
