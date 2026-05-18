<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SettlementTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'upload_id', 'account_id', 'mid', 'tid', 'merchant_name', 'trading_name',
        'transaction_date', 'currency', 'mode_of_payment', 'card_number',
        'card_scheme', 'card_program', 'card_type', 'card_category',
        'transaction_id', 'invoice_number', 'batch_number', 'rrn', 'arn',
        'transaction_type', 'transaction_amount', 'charges', 'taxes',
        'net_settlement_amount', 'auth_code', 'utr_reference',
        'transaction_datetime', 'settlement_date', 'status',
    ];

    protected $casts = [
        'transaction_date'      => 'date',
        'settlement_date'       => 'date',
        'transaction_amount'    => 'decimal:2',
        'charges'               => 'decimal:2',
        'taxes'                 => 'decimal:2',
        'net_settlement_amount' => 'decimal:2',
    ];

    public function upload(): BelongsTo
    {
        return $this->belongsTo(SettlementUpload::class, 'upload_id');
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(SettlementAccount::class, 'account_id');
    }
}