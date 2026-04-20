<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IndentHistory extends Model
{
    public $timestamps = false;

    protected $table = 'indent_histories';

    protected $fillable = [
        'indent_id',
        'user_id',
        'action',
        'payload',
        'created_at',
    ];

    protected $casts = [
        'payload'    => 'array',
        'created_at' => 'datetime',
    ];

    public function indent(): BelongsTo
    {
        return $this->belongsTo(Indent::class, 'indent_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(usermanagementdetails::class, 'user_id');
    }
}
