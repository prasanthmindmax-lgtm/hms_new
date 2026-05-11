<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class VmsQrCode extends Model
{
    use HasFactory;

    protected $table = 'vms_qr_codes';

    protected $fillable = [
        'uuid', 'label', 'location', 'branch', 'is_active', 'scan_count', 'created_by',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }

    public function visitors()
    {
        return $this->hasMany(VmsVisitor::class, 'qr_code_id');
    }

    public function getRegisterUrlAttribute(): string
    {
        return route('vms.register', $this->uuid);
    }
}
