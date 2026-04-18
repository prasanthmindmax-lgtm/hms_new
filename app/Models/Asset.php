<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Asset extends Model
{
    use HasFactory;

    protected $table = 'assets';

    public const STATUS_AVAILABLE   = 'available';

    public const STATUS_ASSIGNED    = 'assigned';

    public const STATUS_MAINTENANCE = 'maintenance';

    public const STATUS_RETIRED     = 'stock';

    /** @var list<string> */
    public const STATUSES = [
        self::STATUS_AVAILABLE,
        self::STATUS_ASSIGNED,
        self::STATUS_MAINTENANCE,
        self::STATUS_RETIRED,
    ];

    protected $fillable = [
        'company_id',
        'zone_id',
        'branch_id',
        'department_id',
        'category_id',
        'asset_code',
        'model',
        'serial_number',
        'purchase_date',
        'warranty_expiry',
        'status',
        'responsible_person',
        'assigned_user_id',
        'assigned_hrm_employment_id',
        'consumable_store_id',
        'remarks',
        'type_attributes',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'purchase_date'   => 'date',
        'warranty_expiry' => 'date',
        'type_attributes' => 'array',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(AssetCategory::class, 'category_id');
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(usermanagementdetails::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(usermanagementdetails::class, 'updated_by');
    }

    public function primaryCompany(): BelongsTo
    {
        return $this->belongsTo(Tblcompany::class, 'company_id');
    }

    public function primaryZone(): BelongsTo
    {
        return $this->belongsTo(TblZonesModel::class, 'zone_id');
    }

    public function primaryBranch(): BelongsTo
    {
        return $this->belongsTo(TblLocationModel::class, 'branch_id');
    }

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(usermanagementdetails::class, 'assigned_user_id');
    }

    public function consumableStoreLine(): BelongsTo
    {
        return $this->belongsTo(ConsumableStore::class, 'consumable_store_id');
    }

    /**
     * @param  mixed  $default
     * @return mixed
     */
    public function typeAttr(string $key, $default = null)
    {
        $ta = $this->type_attributes;

        return is_array($ta) && array_key_exists($key, $ta) ? $ta[$key] : $default;
    }

    public function systemModelDisplay(): string
    {
        $fromJson = (string) ($this->typeAttr('system_model') ?? '');

        return $fromJson !== '' ? $fromJson : (string) ($this->model ?? '');
    }

    public function locationOneLabel(): string
    {
        $parts = array_filter([
            $this->primaryCompany->company_name ?? null,
            $this->primaryZone->name ?? null,
            $this->primaryBranch->name ?? null,
        ]);
        if ($parts !== []) {
            return implode(' / ', $parts);
        }

        return '—';
    }

    public function locationTwoLabel(): string
    {
        return '—';
    }
}
