<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TblLocationModel extends Model
{
    use HasFactory;

    protected $table = 'tbl_locations';

    public $timestamps = false;

    protected $fillable = [
        'id',
        'name',
        'zone_id',
        'level',
        'status',
    ];

    public function zone()
    {
        return $this->belongsTo(TblZonesModel::class, 'zone_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }
}
