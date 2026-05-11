<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class VmsBlacklist extends Model
{
    use HasFactory;

    protected $table = 'vms_blacklist';

    protected $fillable = [
        'visitor_name', 'visitor_phone', 'company_name', 'visitor_type',
        'reason', 'incidents', 'is_active', 'blacklisted_by', 'blacklisted_at',
    ];

    protected $casts = [
        'is_active'      => 'boolean',
        'blacklisted_at' => 'datetime',
    ];

    public function blacklistedBy()
    {
        return $this->belongsTo(\App\Models\usermanagementdetails::class, 'blacklisted_by');
    }
}
