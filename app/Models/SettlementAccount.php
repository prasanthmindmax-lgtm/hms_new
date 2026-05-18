<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SettlementAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'upload_id',
        'mid',
        'tid',
        'merchant_name',
        'trading_name',
        'branch',
        'transaction_count',
        'total_transaction_amount',
        'total_charges',
        'total_taxes',
        'total_net_settlement_amount',
        'transaction_date',
        'settlement_date',
        'currency',
    ];

    protected $casts = [
        'transaction_date'            => 'date',
        'settlement_date'             => 'date',
        'total_transaction_amount'    => 'decimal:2',
        'total_charges'               => 'decimal:2',
        'total_taxes'                 => 'decimal:2',
        'total_net_settlement_amount' => 'decimal:2',
    ];

    public function upload(): BelongsTo
    {
        return $this->belongsTo(SettlementUpload::class, 'upload_id');
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(SettlementTransaction::class, 'account_id');
    }
}
