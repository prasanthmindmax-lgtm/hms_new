<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IssueCategory extends Model
{
    protected $table = 'issue_categories';

    protected $fillable = [
        'ticket_category_id',
        'department_id',
        'name',
        'sla_time',
        'is_active',
        'description',
        'created_by',
    ];

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function ticketCategory(): BelongsTo
    {
        return $this->belongsTo(TicketCategory::class, 'ticket_category_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(usermanagementdetails::class, 'created_by');
    }
}
