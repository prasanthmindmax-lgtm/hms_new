<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TblZonesModel extends Model
{
    use HasFactory;

    protected $table = 'tblzones';

    public $timestamps = false;

    protected $fillable = [
        'id',
        'name',
    ];

    public function locations()
    {
        return $this->hasMany(TblLocationModel::class, 'zone_id');
    }
}
