<?php

namespace App\Support;

/**
 * Income tag supporting documents (bank_recon_match_attachment_types, scope income or both).
 *
 * - **Cash only** (sole selected mode): MOCDOC screenshot + Radiant slip + collection ledger.
 * - **Any other mode selection**: MOCDOC collection screenshot only.
 *
 * Matching is tolerant of spacing/case. Intended labels:
 * - MOCDOC COLLECTION SCREEN SHOT
 * - RADIANT SLIP
 * - COLLECTION LEDGER (or Branch ledger)
 */
final class BankReconIncomeRequiredAttachments
{
    /** MOCDOC + Radiant slip + collection ledger (cash-only income tag). */
    public const SLOTS = ['mocdoc', 'radiant', 'ledger'];

    /**
     * @param  list<string>  $modes  Normalized lowercase: cash, card, upi, neft, other
     * @return list<string>  Slot keys: mocdoc, and optionally radiant, ledger
     */
    public static function requiredSlotsForIncomeModes(array $modes): array
    {
        $norm = [];
        foreach ($modes as $m) {
            $m = strtolower(trim((string) $m));
            if ($m !== '') {
                $norm[] = $m;
            }
        }
        $norm = array_values(array_unique($norm));

        if (count($norm) === 1 && $norm[0] === 'cash') {
            return self::SLOTS;
        }

        return ['mocdoc'];
    }

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
