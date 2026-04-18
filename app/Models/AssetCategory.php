<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssetCategory extends Model
{
    use HasFactory;

    protected $table ='asset_categories';

    protected $fillable = [
        'name',
        'is_active',
        'description',
        'created_by',
        'updated_by',
    ];

    public function createdBy()
    {
        return $this->belongsTo(usermanagementdetails::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(usermanagementdetails::class, 'updated_by');
    }

    public function assets()
    {
        return $this->hasMany(Asset::class, 'category_id');
    }
}
