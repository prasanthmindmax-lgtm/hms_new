<?php

namespace App\Support;

/**
 * Income tag requires three supporting documents identified by attachment type *names*
 * (rows in bank_recon_match_attachment_types, scope income or both).
 *
 * Matching is tolerant of spacing/case. Intended labels:
 * - MOCDOC COLLECTION SCREEN SHOT
 * - RADIANT SLIP
 * - COLLECTION LEDGER (or Branch ledger)
 */
final class BankReconIncomeRequiredAttachments
{
    public const SLOTS = ['mocdoc', 'radiant', 'ledger'];

    public static function normalizeCompact(string $name): string
    {
        return strtoupper(preg_replace('/\s+/u', '', trim($name)));
    }

    /**
     * Map a single type name to mocdoc | radiant | ledger, or null if it does not qualify.
     */
    public static function slotForTypeName(string $name): ?string
    {
        $n = self::normalizeCompact($name);
        if ($n === '') {
            return null;
        }

        if (str_contains($n, 'MOCDOC')
            && (str_contains($n, 'SCREEN') || str_contains($n, 'SHOT'))) {
            return 'mocdoc';
        }

        if (str_contains($n, 'RADIANT')) {
            return 'radiant';
        }

        if (str_contains($n, 'LEDGER')
            && (str_contains($n, 'COLLECTION') || str_contains($n, 'BRANCH'))) {
            return 'ledger';
        }

        return null;
    }

    public static function slotLabel(string $slot): string
    {
        return match ($slot) {
            'mocdoc' => 'MOCDOC COLLECTION SCREEN SHOT',
            'radiant' => 'RADIANT SLIP',
            'ledger' => 'COLLECTION LEDGER',
            default => $slot,
        };
    }
}
