<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\DB;

class PettyCashHistory extends Model
{
    protected $table = 'petty_cash_histories';

    protected $fillable = [
        'historyable_type',
        'historyable_id',
        'action',
        'message',
        'created_by',
        'updated_by',
    ];

    public function historyable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Petty cash report timeline (polymorphic: {@see ExpenseReport}).
     */
    public static function recordForReport(int $reportId, string $action, string $message, ?int $userId = null): void
    {
        self::insertFor(ExpenseReport::class, $reportId, $action, $message, $userId);
    }

    /**
     * Advance timeline (polymorphic: {@see Advance}).
     */
    public static function recordForAdvance(int $advanceId, string $action, string $message, ?int $userId = null): void
    {
        self::insertFor(Advance::class, $advanceId, $action, $message, $userId);
    }

    /**
     * @deprecated Use {@see recordForReport()}; kept for existing call sites.
     */
    public static function record(int $reportId, string $action, string $message, ?int $userId = null): void
    {
        self::recordForReport($reportId, $action, $message, $userId);
    }

    protected static function insertFor(string $historyableType, int $historyableId, string $action, string $message, ?int $userId = null): void
    {
        $uid = $userId ?? (auth()->check() ? (int) auth()->id() : null);

        static::query()->create([
            'historyable_type' => $historyableType,
            'historyable_id'   => $historyableId,
            'action'           => $action,
            'message'          => $message,
            'created_by'       => $uid,
            'updated_by'       => $uid,
        ]);
    }
}
