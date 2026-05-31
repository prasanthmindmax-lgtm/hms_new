<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

/**
 * File upload, URL resolution, and delete — files live under public/{folder}/.
 */
class FileUploadService
{
    public const MAX_KB = 10240;

    public const MIMES = 'pdf,jpg,jpeg,png,doc,docx,xls,xlsx,webp';

    public function validationRule(): string
    {
        return 'file|max:'.self::MAX_KB.'|mimes:'.self::MIMES;
    }

    public function normalizeFolder(string $folder): string
    {
        $folder = trim(str_replace('\\', '/', $folder), '/');
        $folder = preg_replace('/[^a-zA-Z0-9_\-\/]/', '_', $folder) ?? '';

        return $folder !== '' ? $folder : 'uploads';
    }

    public function publicDirectory(string $folder): string
    {
        return public_path($this->normalizeFolder($folder));
    }

    /**
     * @return array{path: string, name: string, file: string}|null
     */
    public function upload(?UploadedFile $file, string $folder = 'uploads'): ?array
    {
        if ($file === null || ! $file->isValid()) {
            return null;
        }

        $folder = $this->normalizeFolder($folder);
        $uploadDir = $this->publicDirectory($folder);

        if (! File::isDirectory($uploadDir)) {
            File::makeDirectory($uploadDir, 0755, true);
        }

        $originalName = (string) $file->getClientOriginalName();
        $extension = strtolower($file->getClientOriginalExtension() ?: pathinfo($originalName, PATHINFO_EXTENSION));
        $fileName = time().'_'.uniqid().($extension !== '' ? '.'.$extension : '');
        $fileName = basename(str_replace(["\0", '/', '\\'], '', $fileName));

        $file->move($uploadDir, $fileName);

        $storedPath = $folder.'/'.$fileName;

        return [
            'path' => $storedPath,
            'name' => $originalName,
            'file' => $fileName,
        ];
    }

    /**
     * @param  UploadedFile|list<UploadedFile>|null  $files
     * @return list<array{path: string, name: string, file: string}>
     */
    public function uploadMultiple($files, string $folder = 'uploads'): array
    {
        $uploaded = [];

        if ($files === null) {
            return $uploaded;
        }

        if ($files instanceof UploadedFile) {
            $files = [$files];
        }

        if (! is_array($files)) {
            return $uploaded;
        }

        foreach ($files as $file) {
            if (! $file instanceof UploadedFile) {
                continue;
            }
            $meta = $this->upload($file, $folder);
            if ($meta !== null) {
                $uploaded[] = $meta;
            }
        }

        return $uploaded;
    }

    public function getFileUrl(?string $stored, string $folder = 'uploads', ?string $placeholder = null): ?string
    {
        if ($stored === null || trim($stored) === '') {
            return $placeholder !== null ? asset($placeholder) : null;
        }

        $stored = str_replace('\\', '/', trim($stored));

        if (str_starts_with($stored, 'http://') || str_starts_with($stored, 'https://')) {
            return $stored;
        }

        if (str_starts_with($stored, 'uploads/') || str_starts_with($stored, 'public/')) {
            return asset($stored);
        }

        $relative = $this->resolveDiskPath($stored, $folder);

        if (File::exists(public_path($relative))) {
            return asset($relative);
        }

        if (File::exists(public_path('public/'.$relative))) {
            return asset('public/'.$relative);
        }

        $storageRelative = $this->normalizeFolder($folder).'/'.basename($relative);
        if (Storage::disk('public')->exists($storageRelative)) {
            $legacyPublic = $this->publicDirectory($folder).'/'.basename($relative);
            if (! File::exists($legacyPublic)) {
                try {
                    File::copy(
                        Storage::disk('public')->path($storageRelative),
                        $legacyPublic
                    );
                } catch (\Throwable $e) {
                    // Best-effort only.
                }
            }
            if (File::exists($legacyPublic)) {
                return asset($folder.'/'.basename($relative));
            }
        }

        $basename = basename($relative);
        if ($basename !== '' && File::exists($this->publicDirectory($folder).'/'.$basename)) {
            return asset($this->normalizeFolder($folder).'/'.$basename);
        }

        return $placeholder !== null ? asset($placeholder) : null;
    }

    public function delete(?string $stored, string $folder = 'uploads'): void
    {
        if ($stored === null || trim($stored) === '') {
            return;
        }

        $stored = str_replace('\\', '/', trim($stored));
        $relative = $this->resolveDiskPath($stored, $folder);

        $paths = [
            public_path($relative),
            public_path('public/'.$relative),
            $this->publicDirectory($folder).'/'.basename($relative),
        ];

        foreach ($paths as $absolute) {
            if (File::exists($absolute)) {
                try {
                    File::delete($absolute);
                } catch (\Throwable $e) {
                    // Best-effort only.
                }
            }
        }

        $storageRelative = $this->normalizeFolder($folder).'/'.basename($relative);
        if (Storage::disk('public')->exists($storageRelative)) {
            try {
                Storage::disk('public')->delete($storageRelative);
            } catch (\Throwable $e) {
                // Best-effort only.
            }
        }
    }

    /**
     * @param  list<array{path?: string}|string>  $files
     */
    public function deleteMultiple(array $files, string $folder = 'uploads'): void
    {
        foreach ($files as $file) {
            $path = is_string($file) ? $file : (string) ($file['path'] ?? '');
            $this->delete($path !== '' ? $path : null, $folder);
        }
    }

    /**
     * @param  list<array{path?: string, name?: string}|string>  $files
     */
    public function encodeFiles(array $files): ?string
    {
        $payload = [];
        foreach ($files as $file) {
            $path = is_string($file)
                ? trim($file)
                : trim((string) ($file['path'] ?? $file['stored_path'] ?? ''));
            if ($path === '') {
                continue;
            }
            $name = is_array($file) ? trim((string) ($file['name'] ?? '')) : '';
            $entry = ['path' => $path];
            if ($name !== '') {
                $entry['name'] = $name;
            }
            $payload[] = $entry;
        }

        if ($payload === []) {
            return null;
        }

        return json_encode($payload, JSON_UNESCAPED_SLASHES);
    }

    /**
     * @return list<array{path: string, name: string}>
     */
    public function decodeFiles(mixed $raw, ?string $legacyOriginalName = null): array
    {
        if ($raw === null || $raw === '') {
            return [];
        }

        if (is_array($raw)) {
            $items = $raw;
        } else {
            $trim = trim((string) $raw);
            if ($trim === '') {
                return [];
            }

            if ($trim[0] === '[') {
                $decoded = json_decode($trim, true);
                if (is_array($decoded) && $decoded !== []) {
                    $items = $decoded;
                } else {
                    return $this->decodeFilesFromBrokenJson($trim);
                }
            } else {
                $name = $legacyOriginalName !== null && trim($legacyOriginalName) !== ''
                    ? trim($legacyOriginalName)
                    : basename(str_replace('\\', '/', $trim));

                return [['path' => $trim, 'name' => $name]];
            }
        }

        $files = [];
        foreach ($items as $item) {
            if (is_string($item)) {
                $path = trim($item);
                $name = basename(str_replace('\\', '/', $path));
            } elseif (is_array($item)) {
                $path = trim((string) ($item['path'] ?? $item['stored_path'] ?? ''));
                $name = trim((string) ($item['name'] ?? ''));
                if ($name === '' && $path !== '') {
                    $name = basename(str_replace('\\', '/', $path));
                }
            } else {
                continue;
            }
            if ($path !== '') {
                $files[] = ['path' => $path, 'name' => $name];
            }
        }

        return $files;
    }

    /**
     * Recover file entries when JSON was truncated in a VARCHAR column.
     *
     * @return list<array{path: string, name: string}>
     */
    private function decodeFilesFromBrokenJson(string $trim): array
    {
        $files = [];

        if (preg_match_all('/"path"\s*:\s*"((?:\\\\.|[^"\\\\])*)"/', $trim, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                $path = stripcslashes((string) ($match[1] ?? ''));
                $path = trim(str_replace('\\', '/', $path));
                if ($path !== '') {
                    $files[] = [
                        'path' => $path,
                        'name' => basename($path),
                    ];
                }
            }
        }

        if ($files !== []) {
            return $files;
        }

        if (preg_match('/"path"\s*:\s*"([^"]*)/', $trim, $partial)) {
            $path = trim(str_replace('\\', '/', (string) ($partial[1] ?? '')));
            if ($path !== '') {
                return [['path' => $path, 'name' => basename($path)]];
            }
        }

        return [];
    }

    /**
     * @param  list<array{path: string, name: string}>  $existing
     * @return list<array{path: string, name: string}>
     */
    public function syncMultiple(
        Request $request,
        string $folder,
        string $inputName,
        ?string $keepInputName,
        array $existing,
        bool $forUpdate
    ): array {
        $folder = $this->normalizeFolder($folder);
        $files = [];

        if ($forUpdate && $keepInputName !== null) {
            $keepPaths = array_values(array_filter(array_map(
                static fn ($v) => trim((string) $v),
                (array) $request->input($keepInputName, [])
            ), static fn ($v) => $v !== ''));

            foreach ($existing as $file) {
                $path = (string) ($file['path'] ?? '');
                if ($path !== '' && in_array($path, $keepPaths, true)) {
                    $files[] = $file;

                    continue;
                }
                $this->delete($path !== '' ? $path : null, $folder);
            }
        }

        if ($request->hasFile($inputName)) {
            $uploaded = $this->uploadMultiple($request->file($inputName), $folder);
            $files = array_merge($files, $uploaded);
        }

        return $files;
    }

    public function resolveDiskPath(string $stored, string $folder): string
    {
        $stored = trim(str_replace('\\', '/', $stored), '/');
        $folder = $this->normalizeFolder($folder);

        if (str_starts_with($stored, 'storage/')) {
            $stored = substr($stored, strlen('storage/'));
        }

        if (str_contains($stored, '/')) {
            return $stored;
        }

        return $folder.'/'.$stored;
    }
}
