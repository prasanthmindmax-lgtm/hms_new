<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserActivityLog extends Model
{
    public $timestamps = false;

    protected $table = 'activity_logs';

    protected $fillable = [
        'user_id',
        'username',
        'user_fullname',
        'user_email',
        'activity_module',
        'user_activity_session_id',
        'type',
        'http_method',
        'route_name',
        'path',
        'label',
        'records_count',
        'action_duration_ms',
        'server_duration_ms',
        'url_query',
        'request_snapshot',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'request_snapshot' => 'array',
    ];

    public function activitySession(): BelongsTo
    {
        return $this->belongsTo(UserActivitySession::class, 'user_activity_session_id');
    }
}
