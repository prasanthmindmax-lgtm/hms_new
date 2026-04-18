<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

/**
 * Configurable licence document slots per level (keys + labels + order).
 *
 * @property int $id
 * @property string $document_key
 * @property string $label
 * @property int $level
 * @property bool $is_active
 */
class LicenceDocumentCatalog extends Model
{
    protected $table = 'licence_document_catalog';

    protected $fillable = [
        'document_key',
        'label',
        'level',
        'is_active',
    ];

    protected $casts = [
        'level' => 'integer',
        'is_active' => 'boolean',
    ];

    public static function cacheKeyForLevel(int $level): string
    {
        return 'licence_document_catalog.level.'.($level === 2 ? 2 : 1);
    }

    public static function forgetCaches(): void
    {
        Cache::forget(self::cacheKeyForLevel(1));
        Cache::forget(self::cacheKeyForLevel(2));
    }

    protected static function booted(): void
    {
        static::saved(static function () {
            self::forgetCaches();
        });
        static::deleted(static function () {
            self::forgetCaches();
        });
    }

    /**
     * @return list<array{key: string, label: string}>
     */
    public static function catalogRowsForLevel(int $level): array
    {
        $lv = $level === 2 ? 2 : 1;

        return Cache::remember(self::cacheKeyForLevel($lv), 3600, function () use ($lv) {
            return self::query()
                ->where('level', $lv)
                ->where('is_active', true)
                ->orderBy('id')
                ->get()
                ->map(static fn (self $r) => [
                    'key' => $r->document_key,
                    'label' => $r->label,
                ])
                ->all();
        });
    }

    /**
     * @return list<string>
     */
    public static function keysForLevel(int $level): array
    {
        return array_column(self::catalogRowsForLevel($level), 'key');
    }

    public static function labelForKey(int $level, string $key): ?string
    {
        $lv = $level === 2 ? 2 : 1;
        $row = self::query()
            ->where('level', $lv)
            ->where('document_key', $key)
            ->where('is_active', true)
            ->first();

        return $row?->label;
    }

    public static function requiredCountForLevel(int $level): int
    {
        $lv = $level === 2 ? 2 : 1;

        return self::query()
            ->where('level', $lv)
            ->where('is_active', true)
            ->count();
    }
}
