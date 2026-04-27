<?php

namespace App\Support;

use Illuminate\Http\Request;

/**
 * Validation + value helper for the shared "create form duration" POST field.
 */
final class CreateFormDuration
{
    public static function inputName(): string
    {
        return (string) config('create_form_duration.input_name', 'create_form_duration_ms');
    }

    public static function maxMs(): int
    {
        return (int) config('create_form_duration.max_ms', 172800000);
    }

    /**
     * @return array<string, list<string|int>>
     */
    public static function rules(): array
    {
        $k = self::inputName();
        $max = self::maxMs();

        return [
            $k => ['nullable', 'integer', 'min:0', "max:{$max}"],
        ];
    }

    /**
     * @param  array<string, mixed>  $rules
     * @return array<string, mixed>
     */
    public static function mergeRules(array $rules): array
    {
        return array_merge($rules, self::rules());
    }

    /**
     * @param  array<string, mixed>  $validated
     */
    public static function nullableIntFromValidated(array $validated): ?int
    {
        $k = self::inputName();
        if (! array_key_exists($k, $validated) || $validated[$k] === null || $validated[$k] === '') {
            return null;
        }

        return (int) $validated[$k];
    }

    /**
     * Raw POST/JSON (before controller validation) — used by activity logger middleware.
     *
     * Includes PUT and PATCH: Laravel resolves the verb from _method on HTML forms, so typical
     * edit saves are PUT/PATCH even though the browser uses an HTTP POST body.
     */
    public static function nullableIntFromRequest(?Request $request): ?int
    {
        if (! $request) {
            return null;
        }
        $m = strtoupper($request->getMethod());
        if (! in_array($m, ['POST', 'PUT', 'PATCH'], true)) {
            return null;
        }
        $k = self::inputName();
        $v = $request->input($k);
        // Do not use Request::has(): it returns false for empty/0, so a valid "0" (unset) is fine,
        // but the field can also be missing entirely — input() is enough; treat non-numeric as absent.
        if ($v === null || $v === '' || ! is_numeric($v)) {
            return null;
        }
        $i = (int) $v;
        // Default hidden value is 0; treat as "not set" so user-activity can use session (GET→POST) or other fallbacks.
        if ($i <= 0) {
            return null;
        }
        $max = self::maxMs();

        return $i > $max ? $max : $i;
    }

    /**
     * If the request carries a validated duration, set the model column (same name as {@see inputName()}) on $data.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public static function mergeIntoDataFromRequest(Request $request, array $data): array
    {
        $dms = self::nullableIntFromRequest($request);
        if ($dms !== null) {
            $data[self::inputName()] = $dms;
        }

        return $data;
    }
}
