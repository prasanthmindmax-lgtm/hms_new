<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PharmacyAuditItem extends Model
{
    protected $fillable = [
        'pharmacy_audit_id',
        'line_no',
        'item_name',
        'batch_no',
        'expiry',
        'mrp',
        'system_qty',
        'manual_qty',
        'diff_qty',
        'val',
    ];

    protected $casts = [
        'mrp' => 'decimal:2',
        'val' => 'decimal:2',
    ];

    public function audit(): BelongsTo
    {
        return $this->belongsTo(PharmacyAudit::class, 'pharmacy_audit_id');
    }
}
