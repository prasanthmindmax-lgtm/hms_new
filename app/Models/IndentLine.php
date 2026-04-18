<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IndentLine extends Model
{
    protected $table = 'indent_lines';

    protected $fillable = [
        'indent_id',
        'consumable_store_id',
        'item_name',
        'item_category',
        'quantity_requested',
        'quantity_issued',
    ];

    protected $casts = [
        'quantity_requested' => 'decimal:2',
        'quantity_issued'    => 'decimal:2',
    ];

    public function indent(): BelongsTo
    {
        return $this->belongsTo(Indent::class, 'indent_id');
    }

    public function consumableStore(): BelongsTo
    {
        return $this->belongsTo(ConsumableStore::class, 'consumable_store_id');
    }
}
