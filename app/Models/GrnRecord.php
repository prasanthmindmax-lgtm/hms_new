<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GrnRecord extends Model
{
    protected $table = 'grn_records';

    public const STATUS_PENDING = 'pending';

    public const STATUS_APPROVED = 'approved';

    public const STATUS_REJECTED = 'rejected';

    protected $fillable = [
        'grn_number',
        'company_id',
        'company_name',
        'zone_id',
        'zone_name',
        'branch_id',
        'branch_name',
        'vendor_id',
        'vendor_name',
        'invoice_number',
        'invoice_date',
        'received_date',
        'received_by',
        'invoice_copy_path',
        'gps_video_path',
        'gps_video_uploaded',
        'audit_approval_status',
        'remarks',
        'created_by',
        'reviewed_by',
        'reviewed_at',
        'rejection_reason',
    ];

    protected $casts = [
        'invoice_date' => 'date',
        'received_date' => 'date',
        'gps_video_uploaded' => 'boolean',
        'reviewed_at' => 'datetime',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(usermanagementdetails::class, 'created_by');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(usermanagementdetails::class, 'reviewed_by');
    }

    public function isPending(): bool
    {
        return $this->audit_approval_status === self::STATUS_PENDING;
    }

    public static function statusLabel(string $status): string
    {
        return match ($status) {
            self::STATUS_APPROVED => 'Approved',
            self::STATUS_REJECTED => 'Rejected',
            default => 'Pending',
        };
    }

    public function reviewerDisplayName(): string
    {
        return self::resolveUserDisplayName($this->reviewer, $this->reviewed_by);
    }

    public function creatorDisplayName(): string
    {
        return self::resolveUserDisplayName($this->creator, $this->created_by);
    }

    private static function resolveUserDisplayName(?Model $user, $fallbackId): string
    {
        if ($user) {
            foreach (['user_fullname', 'name', 'username', 'email'] as $col) {
                $val = trim((string) ($user->{$col} ?? ''));
                if ($val !== '') {
                    return $val;
                }
            }
        }

        return $fallbackId ? 'User #'.$fallbackId : '—';
    }
}
