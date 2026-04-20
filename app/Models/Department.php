<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Department extends Model
{
    use HasFactory;

    protected $table ='departments';

    protected $fillable = [
        'name',
        'is_active',
        'description',
        'created_by',
    ];

    public function assignedUsers(): BelongsToMany
    {
        return $this->belongsToMany(
            usermanagementdetails::class,
            'department_user',
            'department_id',
            'user_id'
        )->withTimestamps();
    }
}
