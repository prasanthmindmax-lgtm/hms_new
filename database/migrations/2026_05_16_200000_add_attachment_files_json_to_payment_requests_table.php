<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Payment request attachments: multiple files stored as JSON in *_path columns.
 * - Widens po_attachment_path, document_attachment_path, bank_document_path (VARCHAR 500 → TEXT)
 * - Normalizes existing single paths to [{"path":"..."}, ...]
 */
return new class extends Migration
{
    private const PATH_COLUMNS = [
        'po_attachment_path',
        'document_attachment_path',
        'bank_document_path',
    ];

    public function up(): void
    {
        $driver = Schema::getConnection()->getDriverName();
        if ($driver === 'mysql') {
            foreach (self::PATH_COLUMNS as $column) {
                DB::statement("ALTER TABLE payment_requests MODIFY {$column} TEXT NULL");
            }
        } else {
            Schema::table('payment_requests', function (Blueprint $table) {
                $table->text('po_attachment_path')->nullable()->change();
                $table->text('document_attachment_path')->nullable()->change();
                $table->text('bank_document_path')->nullable()->change();
            });
        }

        DB::table('payment_requests')
            ->select(array_merge(['id'], self::PATH_COLUMNS))
            ->orderBy('id')
            ->chunkById(100, function ($rows) {
                foreach ($rows as $row) {
                    $updates = [];

                    foreach (self::PATH_COLUMNS as $column) {
                        $raw = $row->{$column};
                        $encoded = $this->encodePathList($raw);
                        if ($encoded !== null && $encoded !== (string) $raw) {
                            $updates[$column] = $encoded;
                        }
                    }

                    if ($updates !== []) {
                        DB::table('payment_requests')->where('id', $row->id)->update($updates);
                    }
                }
            });
    }

    public function down(): void
    {
        $driver = Schema::getConnection()->getDriverName();
        if ($driver === 'mysql') {
            foreach (self::PATH_COLUMNS as $column) {
                DB::statement("ALTER TABLE payment_requests MODIFY {$column} VARCHAR(500) NULL");
            }
        } else {
            Schema::table('payment_requests', function (Blueprint $table) {
                $table->string('po_attachment_path', 500)->nullable()->change();
                $table->string('document_attachment_path', 500)->nullable()->change();
                $table->string('bank_document_path', 500)->nullable()->change();
            });
        }
    }

    private function encodePathList(mixed $raw): ?string
    {
        if ($raw === null || $raw === '') {
            return null;
        }

        if (is_array($raw)) {
            $paths = $this->pathsFromDecodedList($raw);
        } else {
            $trim = trim((string) $raw);
            if ($trim === '') {
                return null;
            }

            if ($trim[0] === '[') {
                $decoded = json_decode($trim, true);
                $paths = is_array($decoded) ? $this->pathsFromDecodedList($decoded) : [];
            } else {
                $paths = [$trim];
            }
        }

        if ($paths === []) {
            return null;
        }

        return json_encode(
            array_map(static fn (string $path) => ['path' => $path], $paths),
            JSON_UNESCAPED_SLASHES,
        );
    }

    /**
     * @return list<string>
     */
    private function pathsFromDecodedList(array $items): array
    {
        $paths = [];

        foreach ($items as $item) {
            if (is_string($item)) {
                $path = trim($item);
            } elseif (is_array($item)) {
                $path = trim((string) ($item['path'] ?? $item['stored_path'] ?? ''));
            } else {
                $path = '';
            }

            if ($path !== '') {
                $paths[] = $path;
            }
        }

        return array_values(array_unique($paths));
    }
};
