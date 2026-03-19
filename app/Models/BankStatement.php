<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BankStatement extends Model
{
    use HasFactory;

    protected $table = 'bank_statements';

    protected $fillable = [
        'user_id',
        'upload_batch_id',
        'file_name',
        'transaction_date',
        'transaction_id',
        'transaction_posted_date',
        'value_date',
        'description',
        'reference_number',
        'cheque_number',
        'withdrawal',
        'deposit',
        'balance',
        'category',
        'match_status',
        'matched_bill_id',
        'matched_amount',
        'matched_date',
        'matched_by',
        'notes',
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'transaction_posted_date' => 'date',
        'value_date' => 'date',
        'matched_date' => 'datetime',
        'withdrawal' => 'decimal:2',
        'deposit' => 'decimal:2',
        'balance' => 'decimal:2',
        'matched_amount' => 'decimal:2',
    ];

    /**
     * User who uploaded the statement (batch)
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * User who matched this statement to a bill
     */
    public function matchedByUser()
    {
        return $this->belongsTo(User::class, 'matched_by');
    }

    /**
     * Bill matched to this statement (if any)
     */
    public function matchedBill()
    {
        return $this->belongsTo(Tblbill::class, 'matched_bill_id');
    }

    /**
     * Bill pay (bill made) record created from this statement
     */
    public function billPay()
    {
        return $this->hasOne(Tblbillpay::class, 'bank_statement_id');
    }

   
}
