<?php

namespace App\Services;

class LandlordAdvanceVendorDashboard
{
    public const NATURE_RENT_ADVANCE = 'rent_advance';

    public const NATURE_RENT_EXPENSES = 'rent_expenses';

    public const NATURE_MAINTENANCE = 'maintenance_charges';

    public const NATURE_EB_CHARGES = 'eb_charges';

    /** @var array<string, string> */
    public const NATURE_LABELS = [
        self::NATURE_RENT_ADVANCE => 'RENT ADVANCE',
        self::NATURE_RENT_EXPENSES => 'RENT EXPENSES',
        self::NATURE_MAINTENANCE => 'MAINTENANCE CHARGES',
        self::NATURE_EB_CHARGES => 'ELECTRICITY / EB',
    ];

    /** @var list<string> */
    public const LEDGER_SECTION_ORDER = [
        self::NATURE_RENT_EXPENSES,
        self::NATURE_RENT_ADVANCE,
        self::NATURE_MAINTENANCE,
        self::NATURE_EB_CHARGES,
    ];

    /** @var array<string, array{icon: string, description: string, title?: string}> */
    public const LEDGER_SECTION_META = [
        self::NATURE_RENT_EXPENSES => [
            'icon' => 'bi-cash-stack',
            'title' => 'Rent expense',
            'description' => 'Monthly rent due on bills or NEFT — what you still owe for rent.',
        ],
        self::NATURE_RENT_ADVANCE => [
            'icon' => 'bi-piggy-bank',
            'title' => 'Rent advance',
            'description' => 'Refundable advance paid to the landlord — balance not yet adjusted.',
        ],
        self::NATURE_MAINTENANCE => [
            'icon' => 'bi-tools',
            'title' => 'Maintenance',
            'description' => 'Building upkeep, repairs, and maintenance charges from bills.',
        ],
        self::NATURE_EB_CHARGES => [
            'icon' => 'bi-lightning-charge-fill',
            'title' => 'Electricity (EB)',
            'description' => 'Electricity / EB charges from landlord bills.',
        ],
    ];

    /**
     * @param  list<array<string, mixed>>  $rows
     * @return array<string, array{key: string, title: string, icon: string, description: string, rows: list<array<string, mixed>>, summary: array<string, float|int>}>
     */
    public static function buildLedgerSections(array $rows): array
    {
        $collection = collect($rows);
        $sections = [];

        foreach (self::LEDGER_SECTION_ORDER as $key) {
            $sectionRows = $collection
                ->where('nature_key', $key)
                ->sortByDesc(fn (array $row) => (int) ($row['sort_key'] ?? 0))
                ->values()
                ->all();

            $meta = self::LEDGER_SECTION_META[$key] ?? ['icon' => 'bi-receipt', 'description' => ''];

            $sections[$key] = [
                'key' => $key,
                'title' => $meta['title'] ?? (self::NATURE_LABELS[$key] ?? $key),
                'icon' => $meta['icon'],
                'description' => $meta['description'],
                'rows' => $sectionRows,
                'summary' => self::summarizeLedgerRows($sectionRows),
            ];
        }

        return $sections;
    }

    /**
     * @param  list<array<string, mixed>>  $rows
     * @return array{line_count: int, amount_sent: float, pending_balance: float, completed_count: int}
     */
    public static function summarizeLedgerRows(array $rows): array
    {
        $collection = collect($rows);

        return [
            'line_count' => $collection->count(),
            'amount_sent' => round((float) $collection->sum('amount_sent'), 2),
            'pending_balance' => round((float) $collection->sum('pending_balance'), 2),
            'completed_count' => $collection->where('status', 'completed')->count(),
        ];
    }
}
