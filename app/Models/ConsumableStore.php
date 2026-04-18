<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConsumableStore extends Model
{
    use HasFactory;

    protected $table = 'consumable_stores';

    protected $fillable = [
        'grn_id',
        'grn_number',
        'department_id',
        'item_name',
        'quantity',
        'unit_price',
    ];

    public function Grn()
    {
        return $this->belongsTo(Tblgrn::class, 'grn_id');
    }
    public function Department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function Zone()
    {
        return $this->belongsTo(TblZonesModel::class, 'zone_id');
    }

    public function Branch()
    {
        return $this->belongsTo(Tblbranch::class, 'branch_id');
    }

    public function Company()
    {
        return $this->belongsTo(Tblcompany::class, 'company_id');
    }
}
