<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

class UserActivitySession extends Model
{
    protected $table = 'user_activity_sessions';

    protected $fillable = [
        'user_id',
        'laravel_session_fingerprint',
        'started_at',
        'last_seen_at',
        'ended_at',
        'duration_seconds',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'last_seen_at' => 'datetime',
        'ended_at' => 'datetime',
    ];

    public function activityLogs(): HasMany
    {
        return $this->hasMany(UserActivityLog::class, 'user_activity_session_id');
    }

    /**
     * Human span from sign-in to sign-out, or to now if the session is still open (absolute, e.g. "2 hours").
     */
    public function displayWorkSessionSpan(): string
    {
        if (! $this->started_at) {
            return '—';
        }
        $end = $this->ended_at ?? Carbon::now();

        return (string) $this->started_at->diffForHumans($end, true);
    }

    public function isOpen(): bool
    {
        return $this->ended_at === null;
    }
}
