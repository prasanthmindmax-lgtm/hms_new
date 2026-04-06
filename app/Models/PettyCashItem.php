<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PettyCashItem extends Model
{
    use HasFactory;

    protected $table = 'petty_cash_items';

    protected $fillable = [
        'petty_cash_id',
        'expense_category_id',
        'description',
        'amount',
    ];

    public function pettyCash()
    {
        return $this->belongsTo(PettyCash::class, 'petty_cash_id');
    }

    public function category()
    {
        return $this->belongsTo(ExpenseCategory::class, 'expense_category_id');
    }
}
