<?php
// File: app/Models/RadiantCashPickup.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RadiantCashPickup extends Model
{
    protected $table = 'radiant_cash_pickups';

    protected $fillable = [
        'sno', 'state_name', 'pickup_date', 'pickup_date_parsed',
        'region', 'location', 'customer_name', 'pickup_address',
        'pickup_point_code', 'client_code', 'deposit_mode', 'frequency',
        'cash_limit', 'hci_slip_no', 'pickup_amount',
        'deposit_slip_no', 'seal_tag_no',
        'denom_2000', 'denom_1000', 'denom_500', 'denom_200',
        'denom_100', 'denom_50', 'denom_20', 'denom_10', 'denom_5',
        'coins', 'total', 'difference', 'remarks', 'ccv', 'point_id',
        'upload_batch_id', 'uploaded_file_name', 'uploaded_by',
    ];

    protected $casts = [
        'pickup_date_parsed' => 'date',
        'cash_limit'    => 'decimal:2',
        'pickup_amount' => 'decimal:2',
        'total'         => 'decimal:2',
        'difference'    => 'decimal:2',
    ];
}