<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BranchLicenceDocument extends Model
{
    protected $table = 'licence_documents';

    protected $fillable = [
        'branch_id',
        'level',
        'document_key',
        'file_path',
        'original_filename',
        'renewal_date',
        'updated_by',
    ];

    protected $casts = [
        'renewal_date' => 'date',
        'level' => 'integer',
        'branch_id' => 'integer',
    ];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(TblLocationModel::class, 'branch_id');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public static function catalogForLevel(int $level): array
    {
        return LicenceDocumentCatalog::catalogRowsForLevel($level);
    }

    public static function validKeysForLevel(int $level): array
    {
        return LicenceDocumentCatalog::keysForLevel($level);
    }

    public static function documentLabelForKey(int $level, string $key): ?string
    {
        return LicenceDocumentCatalog::labelForKey($level, $key);
    }

    public static function requiredDocumentCountForLevel(int $level): int
    {
        return LicenceDocumentCatalog::requiredCountForLevel($level);
    }

    public static function publicFileUrl(?string $filePath): string
    {
        if ($filePath === null || $filePath === '') {
            return '';
        }

        $p = str_replace('\\', '/', (string) $filePath);
        $p = trim($p, '/');
        if (str_starts_with($p, 'public/')) {
            return asset($p);
        }

        return asset('public/'.$p);
    }
}
