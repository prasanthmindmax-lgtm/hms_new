<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SettlementUpload extends Model
{
    use HasFactory;

    protected $fillable = [
        'original_filename',
        'stored_filename',
        'file_path',
        'file_size',
        'mime_type',
        'total_rows',
        'total_accounts',
        'duplicate_accounts_skipped',
        'total_transaction_amount',
        'total_net_settlement_amount',
        'status',
        'error_message',
        'uploaded_by',
        'uploaded_by_name',
        'uploaded_by_email',
        'uploaded_by_username',
        'uploaded_ip',
        'upload_user_agent',
    ];

    protected $casts = [
        'total_transaction_amount'    => 'decimal:2',
        'total_net_settlement_amount' => 'decimal:2',
    ];

    public function uploadedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function accounts(): HasMany
    {
        return $this->hasMany(SettlementAccount::class, 'upload_id');
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(SettlementTransaction::class, 'upload_id');
    }

    public function getFileSizeFormattedAttribute(): string
    {
        $bytes = (int) $this->file_size;
        if ($bytes >= 1048576) return round($bytes / 1048576, 2) . ' MB';
        if ($bytes >= 1024)    return round($bytes / 1024, 2) . ' KB';
        return $bytes . ' B';
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'completed'  => 'success',
            'processing' => 'warning',
            'failed'     => 'danger',
            default      => 'secondary',
        };
    }
}