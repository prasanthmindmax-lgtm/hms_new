<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PharmacyAudit extends Model
{
    protected $fillable = [
        'audit_number',
        'company_id',
        'zone_id',
        'branch_id',
        'audit_date',
        'notes',
        'total_lines',
        'total_val',
        'created_by',
    ];

    protected $casts = [
        'audit_date' => 'date',
        'total_val' => 'decimal:2',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Tblcompany::class, 'company_id');
    }

    public function zone(): BelongsTo
    {
        return $this->belongsTo(TblZonesModel::class, 'zone_id');
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(TblLocationModel::class, 'branch_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(PharmacyAuditItem::class)->orderBy('line_no');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(usermanagementdetails::class, 'created_by');
    }
}
