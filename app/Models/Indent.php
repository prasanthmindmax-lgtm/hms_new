<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Indent extends Model
{
    protected $table = 'indents';

    public const STATUSES = ['pending', 'approved', 'issued', 'partially_issued', 'rejected'];

    public const FULFIL_ACCESS_LEVELS = [1, 2, 3, 6];

    public const OWN_ROW_ACCESS_LEVELS = [4, 5];

    protected $fillable = [
        'indent_no',
        'company_id',
        'zone_id',
        'branch_id',
        'from_department_id',
        'to_department_id',
        'purpose',
        'required_date',
        'remarks',
        'status',
        'created_by',
        'approved_by',
        'approved_at',
        'rejected_by',
        'rejected_at',
        'rejection_reason',
        'last_status_by',
    ];

    protected $casts = [
        'required_date' => 'date',
        'approved_at'   => 'datetime',
        'rejected_at'   => 'datetime',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Tblcompany::class, 'company_id');
    }

    public function zone(): BelongsTo
    {
        return $this->belongsTo(TblZonesModel::class, 'zone_id');
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(TblLocationModel::class, 'branch_id');
    }

    public function fromDepartment(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'from_department_id');
    }

    public function toDepartment(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'to_department_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(usermanagementdetails::class, 'created_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(usermanagementdetails::class, 'approved_by');
    }

    public function rejector(): BelongsTo
    {
        return $this->belongsTo(usermanagementdetails::class, 'rejected_by');
    }

    public function lastStatusBy(): BelongsTo
    {
        return $this->belongsTo(usermanagementdetails::class, 'last_status_by');
    }

    public function lines(): HasMany
    {
        return $this->hasMany(IndentLine::class, 'indent_id');
    }

    public function histories(): HasMany
    {
        return $this->hasMany(IndentHistory::class, 'indent_id')->orderByDesc('id');
    }

    public function indentMayFulfil(?object $user): bool
    {
        if (! $user) {
            return false;
        }

        return in_array((int) ($user->access_limits ?? 0), self::FULFIL_ACCESS_LEVELS, true);
    }
}
