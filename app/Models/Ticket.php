<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Carbon;

class Ticket extends Model
{
    protected $table = 'tickets';

    protected $fillable = [
        'ticket_no',
        'location_id',
        'from_department_id',
        'to_department_id',
        'issue_category_id',
        'priority',
        'subject',
        'description',
        'solution',
        'attachments',
        'status',
        'created_by',
        'status_updated_by',
        'status_updated_at',
        'solution',
    ];

    protected $casts = [
        'attachments' => 'array',
        'solution' => 'array',
        'status_updated_at' => 'datetime',
    ];

    public const STATUSES = ['open', 'in_progress', 'closed', 'cancelled'];

    public const PRIORITIES = ['low', 'medium', 'high'];

    public function location(): BelongsTo
    {
        return $this->belongsTo(TblLocationModel::class, 'location_id');
    }

    public function fromDepartment(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'from_department_id');
    }

    public function toDepartment(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'to_department_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(IssueCategory::class, 'issue_category_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(usermanagementdetails::class, 'created_by');
    }

    public function statusUpdater(): BelongsTo
    {
        return $this->belongsTo(usermanagementdetails::class, 'status_updated_by');
    }

    /** @return list<array<string, mixed>> */
    public function normalizedSolutionEntries(): array
    {
        $s = $this->solution;
        if (!is_array($s)) {
            return [];
        }

        return array_values($s);
    }

    /** First transition into closed (for TAT and resolution note). */
    public function firstClosedSolutionEntry(): ?array
    {
        foreach ($this->normalizedSolutionEntries() as $e) {
            if (($e['to_status'] ?? '') === 'closed') {
                return $e;
            }
        }

        return null;
    }

    /** When the ticket was closed (first closed entry or status_updated_at). */
    public function resolvedClosedAt(): ?Carbon
    {
        if ($this->status !== 'closed' || !$this->created_at) {
            return null;
        }

        $e = $this->firstClosedSolutionEntry();
        if ($e && !empty($e['updated_at'])) {
            return Carbon::parse($e['updated_at']);
        }
        if ($this->status_updated_at) {
            return $this->status_updated_at instanceof Carbon
                ? $this->status_updated_at
                : Carbon::parse($this->status_updated_at);
        }

        return null;
    }

    /** Minutes from raised to closed; null if not closed or times missing. */
    public function actualResolutionMinutes(): ?int
    {
        $closed = $this->resolvedClosedAt();
        if (!$closed || !$this->created_at) {
            return null;
        }

        return max(0, (int) floor($this->created_at->diffInMinutes($closed)));
    }

    /**
     * Category SLA stored as HH:MM (target duration from raise to close).
     */
    public static function parseSlaTimeToMinutes(?string $raw): ?int
    {
        if ($raw === null) {
            return null;
        }
        $s = trim((string) $raw);
        if ($s === '') {
            return null;
        }
        if (!preg_match('/^([01]?\d|2[0-3]):([0-5]\d)(?::[0-5]\d)?$/', $s, $m)) {
            return null;
        }

        return (int) $m[1] * 60 + (int) $m[2];
    }

    public static function formatMinuteDuration(int $totalMinutes): string
    {
        if ($totalMinutes <= 0) {
            return '< 1 min';
        }

        $days = intdiv($totalMinutes, 1440);
        $hours = intdiv($totalMinutes % 1440, 60);
        $mins = $totalMinutes % 60;

        $parts = [];
        if ($days > 0) {
            $parts[] = $days . 'd';
        }
        if ($hours > 0) {
            $parts[] = $hours . 'h';
        }
        if ($mins > 0 || $parts === []) {
            $parts[] = $mins . 'm';
        }

        return implode(' ', $parts);
    }

    /**
     * @return array{text: string, kind: string} kind: na|no_sla|within|over
     */
    public function slaVersusActualSummary(): array
    {
        if ($this->status !== 'closed') {
            return ['text' => '—', 'kind' => 'na'];
        }

        $actualMin = $this->actualResolutionMinutes();
        if ($actualMin === null) {
            return ['text' => '—', 'kind' => 'na'];
        }

        $slaMin = self::parseSlaTimeToMinutes($this->category?->sla_time);
        if ($slaMin === null) {
            return ['text' => 'No SLA set', 'kind' => 'no_sla'];
        }

        $diff = $actualMin - $slaMin;
        if ($diff <= 0) {
            $spare = abs($diff);

            return [
                'text' => $spare === 0 ? 'Met SLA' : (self::formatMinuteDuration($spare) . ' under SLA'),
                'kind' => 'within',
            ];
        }

        return [
            'text' => self::formatMinuteDuration($diff) . ' over SLA',
            'kind' => 'over',
        ];
    }

    public function timeToCloseDisplay(): string
    {
        if ($this->status !== 'closed' || !$this->created_at) {
            return '';
        }

        $closedAt = $this->resolvedClosedAt();
        if (!$closedAt) {
            return '';
        }

        return self::formatRaisedToClosedDuration($this->created_at, $closedAt);
    }

    public function closedStatusNote(): string
    {
        $e = $this->firstClosedSolutionEntry();
        $n = $e['note'] ?? null;

        return is_string($n) ? trim($n) : '';
    }

    /**
     * Human-readable log for export: each status change with from, to, who, when, note.
     */
    public function solutionExportText(): string
    {
        $lines = [];
        foreach ($this->normalizedSolutionEntries() as $e) {
            $from = (string) ($e['from_status'] ?? '');
            $to = (string) ($e['to_status'] ?? '');
            $by = (string) ($e['user_name'] ?? '—');
            $at = '';
            if (!empty($e['updated_at'])) {
                $at = Carbon::parse($e['updated_at'])->format('d M Y, g:i A');
            }
            $noteRaw = $e['note'] ?? null;
            $note = is_string($noteRaw) && trim($noteRaw) !== '' ? trim($noteRaw) : '—';
            $fromLabel = $from !== '' ? ucwords(str_replace('_', ' ', $from)) : '—';
            $toLabel = $to !== '' ? ucwords(str_replace('_', ' ', $to)) : '—';
            $lines[] = 'Previous status: ' . $fromLabel
                . ' | Updated status: ' . $toLabel
                . ' | By: ' . $by
                . ' | At: ' . $at
                . ' | Notes: ' . $note;
        }

        return implode("\n", $lines);
    }

    public static function formatRaisedToClosedDuration(Carbon $raised, Carbon $closed): string
    {
        if ($closed->lt($raised)) {
            return '';
        }

        $totalMinutes = (int) floor($raised->diffInMinutes($closed));
        if ($totalMinutes <= 0) {
            return '< 1 min';
        }

        $days = intdiv($totalMinutes, 1440);
        $hours = intdiv($totalMinutes % 1440, 60);
        $mins = $totalMinutes % 60;

        $parts = [];
        if ($days > 0) {
            $parts[] = $days . 'd';
        }
        if ($hours > 0) {
            $parts[] = $hours . 'h';
        }
        if ($mins > 0 || $parts === []) {
            $parts[] = $mins . 'm';
        }

        return implode(' ', $parts);
    }

    public function getClosedSolutionAttribute()
    {
        $history = $this->solution ?? [];

        // Convert JSON string → array
        if (!is_array($history)) {
            $history = json_decode($history, true) ?? [];
        }

        foreach (array_reverse($history) as $row) {
            if (($row['to_status'] ?? '') === 'closed') {
                return $row['note'] ?? '';
            }
        }

        return '';
    }
}
