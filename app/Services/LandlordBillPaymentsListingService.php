<?php

namespace App\Services;

use App\Models\RentalAgreement;
use App\Models\Tblvendor;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

/**
 * Rental payments index: unified rows from bill module (bills, bill payments, NEFT).
 */
class LandlordBillPaymentsListingService
{
    public function __construct(
        private readonly VendorBillRentLedgerService $billLedger,
    ) {}

    /**
     * @return array{
     *     records: LengthAwarePaginator,
     *     overviewStats: array<string, float|int>,
     *     activeTab: string,
     *     chargeFilter: string,
     *     dataSource: string
     * }
     */
    /**
     * Dashboard aggregates from bill-module rows (all natures).
     *
     * @return array{
     *     paidBreakdown: array{rental: float, maintenance: float, fixed: float, eb_bill: float},
     *     tdsBreakdown: array{rental: float, maintenance: float, fixed: float, eb_bill: float},
     *     totalRentPaid: float,
     *     totalTds: float,
     *     totalNeft: float,
     *     pendingPayments: int,
     *     advanceBalance: float,
     *     recentPayments: list<array<string, mixed>>
     * }
     */
    public function dashboardMetrics(Request $request, string $category, string $chargeFilter): array
    {
        $rows = $this->collectAllRows($request, $category);
        $completed = $rows->filter(fn (array $row) => ($row['status'] ?? '') === 'completed');

        $paidBreakdown = [
            'rental' => $this->sumCompletedByNature($completed, LandlordAdvanceVendorDashboard::NATURE_RENT_EXPENSES),
            'maintenance' => $this->sumCompletedByNature($completed, LandlordAdvanceVendorDashboard::NATURE_MAINTENANCE),
            'fixed' => 0.0,
            'eb_bill' => $this->sumCompletedByNature($completed, LandlordAdvanceVendorDashboard::NATURE_EB_CHARGES),
        ];

        $tdsBreakdown = [
            'rental' => $this->sumTdsByNature($completed, LandlordAdvanceVendorDashboard::NATURE_RENT_EXPENSES),
            'maintenance' => $this->sumTdsByNature($completed, LandlordAdvanceVendorDashboard::NATURE_MAINTENANCE),
            'fixed' => 0.0,
            'eb_bill' => $this->sumTdsByNature($completed, LandlordAdvanceVendorDashboard::NATURE_EB_CHARGES),
        ];

        $totalChargesPaid = array_sum($paidBreakdown);
        $totalRentPaid = $chargeFilter === 'all'
            ? $totalChargesPaid
            : ($paidBreakdown[$chargeFilter] ?? 0.0);

        $totalTds = $chargeFilter === 'all'
            ? array_sum($tdsBreakdown)
            : ($tdsBreakdown[$chargeFilter] ?? 0.0);

        $totalNeft = round((float) $completed->sum('display_neft'), 2);

        $pendingPayments = $rows->filter(fn (array $row) => ($row['status'] ?? '') !== 'completed')->count();

        $advanceBalance = round((float) $rows
            ->where('nature_key', LandlordAdvanceVendorDashboard::NATURE_RENT_ADVANCE)
            ->sum('pending_balance'), 2);

        $recentQuery = $rows;
        if ($chargeFilter !== 'all') {
            $nature = $this->chargeFilterToNatureKey($chargeFilter);
            if ($nature !== null) {
                $recentQuery = $recentQuery->where('nature_key', $nature);
            }
        }

        $recentPayments = $recentQuery
            ->sortByDesc(fn (array $row) => (int) ($row['sort_key'] ?? 0))
            ->take(8)
            ->values()
            ->map(fn (array $row) => $this->toDashboardRecentRow($row, $chargeFilter))
            ->all();

        return [
            'paidBreakdown' => $paidBreakdown,
            'tdsBreakdown' => $tdsBreakdown,
            'totalRentPaid' => $totalRentPaid,
            'totalTds' => $totalTds,
            'totalNeft' => $totalNeft,
            'pendingPayments' => $pendingPayments,
            'advanceBalance' => $advanceBalance,
            'recentPayments' => $recentPayments,
        ];
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    public function collectAllRows(Request $request, string $category): Collection
    {
        $types = $category === 'all'
            ? RentalAgreement::TYPES
            : [RentalAgreement::normalizeType($category)];

        $rows = collect();

        foreach ($types as $agreementType) {
            $agreements = $this->filteredAgreements($request, $agreementType);
            $agreementIds = $agreements->pluck('id')->map(fn ($id) => (int) $id)->all();
            $agreementById = $agreements->keyBy('id');
            $agreementNumbers = $agreements->pluck('agreement_number')->filter()->values()->all();
            $vendorIds = $this->resolveVendorIds($request, $agreements, $agreementType);
            $dateFrom = $this->dateFrom($request);
            $dateTo = $this->dateTo($request);

            foreach ($vendorIds as $vendorId) {
                $vendor = Tblvendor::query()->find($vendorId);
                if ($vendor === null) {
                    continue;
                }

                $vendorAgreements = $this->agreementsForVendorFromList($agreements, $vendor);
                $payload = $this->billLedger->buildForVendor(
                    $vendor,
                    $agreementType,
                    null,
                    $vendorAgreements,
                    applyBillLocationFilter: false,
                );

                foreach ($payload['rows'] as $row) {
                    if (! $this->rowMatchesScope($row, $agreementIds, $agreementNumbers)) {
                        continue;
                    }
                    if (! $this->rowMatchesDate($row, $dateFrom, $dateTo)) {
                        continue;
                    }

                    $rows->push($this->enrichRow($row, $agreementById, $vendor));
                }
            }
        }

        return $rows
            ->sortByDesc(fn (array $row) => (int) ($row['sort_key'] ?? 0))
            ->values();
    }

    /**
     * Rows for the payments table (tab + optional GST/TDS KPI filter).
     *
     * @return Collection<int, array<string, mixed>>
     */
    public function collectDisplayRows(Request $request, string $agreementType): Collection
    {
        $activeTab = $this->tabFrom($request);

        $rows = $activeTab === 'all'
            ? $this->collectAllRows($request, $agreementType)
            : $this->collectTabRows($request, $agreementType);

        return $this->applyTaxFilter($rows, $this->taxFilterFrom($request));
    }

    public function taxFilterFrom(Request $request): ?string
    {
        $tax = strtolower(trim((string) $request->input('tax_filter', '')));

        return in_array($tax, ['gst', 'tds'], true) ? $tax : null;
    }

    /**
     * @param  Collection<int, array<string, mixed>>  $rows
     * @return Collection<int, array<string, mixed>>
     */
    private function applyTaxFilter(Collection $rows, ?string $taxFilter): Collection
    {
        if ($taxFilter === null) {
            return $rows;
        }

        return $rows->filter(function (array $row) use ($taxFilter): bool {
            if ($taxFilter === 'gst') {
                $gst = (float) ($row['gst_amount'] ?? 0);
                $components = (float) ($row['sgst_display'] ?? 0)
                    + (float) ($row['cgst_display'] ?? 0)
                    + (float) ($row['igst_display'] ?? 0);

                return $gst > 0.009 || $components > 0.009;
            }

            if ($taxFilter === 'tds') {
                return (float) ($row['tds_amount'] ?? 0) > 0.009;
            }

            return true;
        })->values();
    }

    public function collectTabRows(Request $request, string $agreementType): Collection
    {
        $activeTab = $this->tabFrom($request);
        $agreements = $this->filteredAgreements($request, $agreementType);
        $agreementIds = $agreements->pluck('id')->map(fn ($id) => (int) $id)->all();
        $agreementById = $agreements->keyBy('id');
        $agreementNumbers = $agreements->pluck('agreement_number')->filter()->values()->all();

        $vendorIds = $this->resolveVendorIds($request, $agreements, $agreementType);
        $natureKey = $activeTab !== 'all' ? $this->tabToNatureKey($activeTab) : null;
        $dateFrom = $this->dateFrom($request);
        $dateTo = $this->dateTo($request);

        $rows = collect();

        foreach ($vendorIds as $vendorId) {
            $vendor = Tblvendor::query()->find($vendorId);
            if ($vendor === null) {
                continue;
            }

            $vendorAgreements = $this->agreementsForVendorFromList($agreements, $vendor);
            $payload = $this->billLedger->buildForVendor(
                $vendor,
                $agreementType,
                null,
                $vendorAgreements,
                applyBillLocationFilter: false,
            );

            foreach ($payload['rows'] as $row) {
                if (! $this->rowMatchesScope($row, $agreementIds, $agreementNumbers)) {
                    continue;
                }
                if ($natureKey !== null && ($row['nature_key'] ?? '') !== $natureKey) {
                    continue;
                }
                if (! $this->rowMatchesDate($row, $dateFrom, $dateTo)) {
                    continue;
                }

                $rows->push($this->enrichRow($row, $agreementById, $vendor));
            }
        }

        return $rows
            ->sortByDesc(fn (array $row) => (int) ($row['sort_key'] ?? 0))
            ->values();
    }

    /**
     * @param  Collection<int, array<string, mixed>>  $rows
     * @return array{
     *     statusBreakdown: array{paid: float, pending: float},
     *     statusLabels: array{paid: string, pending: string},
     *     topLandlords: list<array{owner: string, amount: float}>
     * }
     */
    public function chartDataFromRows(Collection $rows): array
    {
        $paid = 0.0;
        $pending = 0.0;

        foreach ($rows as $row) {
            $amount = (float) ($row['display_neft'] ?? 0);
            if (($row['status'] ?? '') === 'completed') {
                $paid += $amount;
            } else {
                $pending += $amount;
            }
        }

        $topLandlords = $rows
            ->groupBy(fn (array $row) => trim((string) ($row['owner_name'] ?? 'Unknown')))
            ->map(fn (Collection $group, string $owner) => [
                'owner' => $owner !== '' ? $owner : 'Unknown',
                'amount' => round((float) $group->sum('display_neft'), 2),
            ])
            ->sortByDesc('amount')
            ->take(8)
            ->values()
            ->all();

        return [
            'statusBreakdown' => [
                'paid' => round($paid, 2),
                'pending' => round($pending, 2),
            ],
            'statusLabels' => [
                'paid' => 'Paid',
                'pending' => 'Pending / due',
            ],
            'topLandlords' => $topLandlords,
        ];
    }

    /**
     * Extended datasets for the dedicated chart report page.
     *
     * @param  Collection<int, array<string, mixed>>  $rows
     * @param  array<string, string>  $tabLabels
     * @return array<string, mixed>
     */
    /**
     * Summary KPIs for the chart report (all charge types in current filters).
     *
     * @param  Collection<int, array<string, mixed>>  $rows
     * @return array<string, float|int>
     */
    public function summarizeChartOverview(Collection $rows): array
    {
        return $this->summarize($rows->all(), 'rent_expense');
    }

    /**
     * @param  Collection<int, array<string, mixed>>  $rows
     * @param  array<string, string>  $tabLabels
     */
    public function chartReportDataFromRows(
        Collection $rows,
        string $activeTab,
        array $tabLabels,
        bool $allChargeTypes = false,
    ): array {
        $base = $this->chartDataFromRows($rows);

        $sgst = round((float) $rows->sum(fn (array $row) => (float) ($row['sgst_display'] ?? 0)), 2);
        $cgst = round((float) $rows->sum(fn (array $row) => (float) ($row['cgst_display'] ?? 0)), 2);
        $igst = round((float) $rows->sum(fn (array $row) => (float) ($row['igst_display'] ?? 0)), 2);
        $gstTotal = round((float) $rows->sum(fn (array $row) => (float) ($row['gst_amount'] ?? 0)), 2);
        if ($gstTotal < 0.01) {
            $gstTotal = round($sgst + $cgst + $igst, 2);
        }

        $gstSplit = array_values(array_filter([
            ['label' => 'SGST', 'amount' => $sgst],
            ['label' => 'CGST', 'amount' => $cgst],
            ['label' => 'IGST', 'amount' => $igst],
        ], static fn (array $item): bool => (float) ($item['amount'] ?? 0) > 0.009));

        $natureBreakdown = $rows
            ->groupBy(fn (array $row) => (string) ($row['nature_key'] ?? ''))
            ->map(fn (Collection $group, string $key) => [
                'key' => $key,
                'label' => LandlordAdvanceVendorDashboard::NATURE_LABELS[$key] ?? $key,
                'amount' => round((float) $group->sum('display_neft'), 2),
                'count' => $group->count(),
            ])
            ->sortByDesc('amount')
            ->values()
            ->all();

        return array_merge($base, [
            'activeTab' => $activeTab,
            'activeTabLabel' => $allChargeTypes
                ? 'All charge types'
                : ($tabLabels[$activeTab] ?? 'Records'),
            'recordCount' => $rows->count(),
            'natureBreakdown' => $natureBreakdown,
            'financialTotals' => [
                ['label' => 'Sub total', 'amount' => round((float) $rows->sum(fn (array $row) => (float) ($row['sub_total'] ?? 0)), 2)],
                ['label' => 'Gross amount', 'amount' => round((float) $rows->sum(fn (array $row) => (float) ($row['gross_amount'] ?? 0)), 2)],
                ['label' => 'GST', 'amount' => $gstTotal],
                ['label' => 'TDS', 'amount' => round((float) $rows->sum(fn (array $row) => (float) ($row['tds_amount'] ?? 0)), 2)],
                ['label' => 'Final NEFT', 'amount' => round((float) $rows->sum(fn (array $row) => (float) ($row['display_neft'] ?? 0)), 2)],
            ],
            'gstSplit' => $gstSplit,
        ]);
    }

    public function build(Request $request, string $agreementType, int $perPage = 20): array
    {
        $activeTab = $this->tabFrom($request);
        $chargeFilter = $activeTab;

        $sorted = $this->collectDisplayRows($request, $agreementType);

        $page = max(1, (int) $request->input('page', 1));
        $total = $sorted->count();
        $items = $sorted->forPage($page, $perPage)->values()->all();

        $records = new LengthAwarePaginator(
            $items,
            $total,
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return [
            'records' => $records,
            'allRows' => $sorted,
            'overviewStats' => $this->summarize($sorted->all(), $activeTab),
            'activeTab' => $activeTab,
            'chargeFilter' => $chargeFilter,
            'dataSource' => 'bill_module',
        ];
    }

    public function tabFrom(Request $request): string
    {
        $tab = strtolower(trim((string) $request->input('tab', '')));
        if ($tab === '' && $request->filled('charge')) {
            $tab = strtolower(trim((string) $request->input('charge', 'all')));
        }
        if ($tab === '') {
            $tab = 'all';
        }

        $aliases = [
            'rental' => 'rent_expense',
            'rent' => 'rent_expense',
            'advance' => 'rent_advance',
            'eb' => 'eb_bill',
            'electricity' => 'eb_bill',
        ];
        if (isset($aliases[$tab])) {
            $tab = $aliases[$tab];
        }

        $valid = ['all', 'rent_expense', 'rent_advance', 'maintenance', 'eb_bill'];

        return in_array($tab, $valid, true) ? $tab : 'all';
    }

    private function tabToNatureKey(string $tab): string
    {
        return match ($tab) {
            'rent_expense' => LandlordAdvanceVendorDashboard::NATURE_RENT_EXPENSES,
            'rent_advance' => LandlordAdvanceVendorDashboard::NATURE_RENT_ADVANCE,
            'maintenance' => LandlordAdvanceVendorDashboard::NATURE_MAINTENANCE,
            'eb_bill' => LandlordAdvanceVendorDashboard::NATURE_EB_CHARGES,
            default => LandlordAdvanceVendorDashboard::NATURE_RENT_EXPENSES,
        };
    }

    /**
     * @return Collection<int, RentalAgreement>
     */
    private function filteredAgreements(Request $request, string $agreementType): Collection
    {
        $query = RentalAgreement::query()
            ->where('agreement_type', $agreementType)
            ->with(['zone:id,name', 'branch:id,name', 'company:id,company_name']);

        $companyIds = $this->filterIntIds($request, 'company_id');
        if ($companyIds !== []) {
            $query->whereIn('company_id', $companyIds);
        }

        $landlords = $this->filterLandlordNames($request);
        if ($landlords !== []) {
            $query->whereIn('owner_name', $landlords);
        }

        $vendorIds = $this->filterIntIds($request, 'vendor_id');
        if ($vendorIds !== []) {
            $query->whereIn('rental_agreements.vendor_id', $vendorIds);
        }

        $zoneIds = $this->filterIntIds($request, 'zone_id');
        if ($zoneIds !== []) {
            $query->whereIn('zone_id', $zoneIds);
        }

        $branchIds = $this->filterIntIds($request, 'branch_id');
        if ($branchIds !== []) {
            $query->whereIn('branch_id', $branchIds);
        }

        $stateIds = array_values(array_intersect(
            $this->filterIntIds($request, 'state_id'),
            [1, 2, 3, 4, 5]
        ));
        if ($stateIds !== []) {
            $this->applyBillStateGeoFilter($query, $stateIds);
        }

        return $query->orderBy('agreement_number')->get();
    }

    /**
     * @param  Collection<int, RentalAgreement>  $agreements
     * @return list<int>
     */
    private function resolveVendorIds(Request $request, Collection $agreements, string $agreementType): array
    {
        $selVendorIds = $this->filterIntIds($request, 'vendor_id');
        if ($selVendorIds !== []) {
            return $selVendorIds;
        }

        $ids = [];
        foreach ($agreements as $agreement) {
            $vendorId = (int) ($agreement->vendor_id ?? 0);
            if ($vendorId > 0) {
                $ids[] = $vendorId;
            }
        }

        foreach ($agreements->pluck('owner_name')->unique()->filter() as $ownerName) {
            $vendor = $this->billLedger->resolveVendorByOwnerName((string) $ownerName);
            if ($vendor !== null) {
                $ids[] = (int) $vendor->id;
            }
        }

        if ($ids === []) {
            $ids = RentalAgreement::query()
                ->where('agreement_type', $agreementType)
                ->whereNotNull('vendor_id')
                ->where('vendor_id', '>', 0)
                ->distinct()
                ->pluck('vendor_id')
                ->map(fn ($id) => (int) $id)
                ->all();
        }

        return array_values(array_unique($ids));
    }

    /**
     * Agreements from the register that belong to this landlord vendor.
     *
     * @param  Collection<int, RentalAgreement>  $agreements
     * @return Collection<int, RentalAgreement>
     */
    private function agreementsForVendorFromList(Collection $agreements, Tblvendor $vendor): Collection
    {
        $vendorId = (int) $vendor->id;
        $byId = $agreements->filter(
            fn (RentalAgreement $agreement) => (int) ($agreement->vendor_id ?? 0) === $vendorId
        );

        if ($byId->isNotEmpty()) {
            return $byId->values();
        }

        $labelKeys = [];
        foreach ([$vendor->display_name, $vendor->company_name] as $name) {
            $key = mb_strtolower(trim((string) $name));
            if ($key !== '') {
                $labelKeys[] = $key;
            }
        }

        if ($labelKeys === []) {
            return collect();
        }

        return $agreements->filter(function (RentalAgreement $agreement) use ($labelKeys): bool {
            $ownerKey = mb_strtolower(trim((string) $agreement->owner_name));

            return $ownerKey !== '' && in_array($ownerKey, $labelKeys, true);
        })->values();
    }

    /**
     * @param  list<int>  $agreementIds
     * @param  list<string>  $agreementNumbers
     */
    private function rowMatchesScope(array $row, array $agreementIds, array $agreementNumbers): bool
    {
        if ($agreementIds === []) {
            return false;
        }

        $aid = (int) ($row['rental_agreement_id'] ?? 0);
        if ($aid > 0) {
            return in_array($aid, $agreementIds, true);
        }

        $num = trim((string) ($row['agreement_number'] ?? ''));
        if ($num !== '' && $num !== '—') {
            return in_array($num, $agreementNumbers, true);
        }

        // Bill not linked to a specific agreement id — keep row when register has agreements for this vendor.
        return true;
    }

    private function rowMatchesDate(array $row, ?string $dateFrom, ?string $dateTo): bool
    {
        if ($dateFrom === null && $dateTo === null) {
            return true;
        }

        $sortKey = (int) ($row['sort_key'] ?? 0);
        if ($sortKey <= 0) {
            return true;
        }

        $rowDate = Carbon::createFromTimestamp($sortKey)->startOfDay();

        if ($dateFrom !== null && $rowDate->lt(Carbon::parse($dateFrom)->startOfDay())) {
            return false;
        }
        if ($dateTo !== null && $rowDate->gt(Carbon::parse($dateTo)->endOfDay())) {
            return false;
        }

        return true;
    }

    /**
     * @param  Collection<int|string, RentalAgreement>  $agreementById
     * @return array<string, mixed>
     */
    private function enrichRow(array $row, Collection $agreementById, Tblvendor $vendor): array
    {
        $aid = (int) ($row['rental_agreement_id'] ?? 0);
        $agreement = $aid > 0 ? $agreementById->get($aid) : null;

        if ($agreement === null) {
            $num = trim((string) ($row['agreement_number'] ?? ''));
            if ($num !== '' && $num !== '—') {
                $agreement = $agreementById->first(fn (RentalAgreement $a) => $a->agreement_number === $num);
            }
        }

        $ownerName = trim((string) ($agreement?->owner_name ?? ''));
        if ($ownerName === '') {
            $ownerName = trim((string) ($row['owner_name'] ?? ''));
        }
        if ($ownerName === '') {
            $ownerName = $this->vendorDisplayName($vendor);
        }

        $row['agreement'] = $agreement;
        $row['owner_name'] = $ownerName;
        $row['agreement_type'] = $agreement
            ? RentalAgreement::normalizeType((string) $agreement->agreement_type)
            : null;
        $row['vendor_id'] = (int) $vendor->id;
        $row['vendor_name'] = $this->vendorDisplayName($vendor);
        $natureKey = (string) ($row['nature_key'] ?? '');
        $natureMeta = LandlordAdvanceVendorDashboard::LEDGER_SECTION_META[$natureKey] ?? null;
        $row['nature_label'] = $natureMeta['title']
            ?? LandlordAdvanceVendorDashboard::NATURE_LABELS[$natureKey]
            ?? ($row['payment_purpose'] ?? '—');
        $row['display_neft'] = (float) ($row['status'] ?? '') === 'completed'
            ? (float) ($row['amount_sent'] ?? 0)
            : (float) ($row['pending_balance'] ?? 0);
        if ($row['display_neft'] <= 0.009) {
            $row['display_neft'] = (float) ($row['net_payable'] ?? 0);
        }

        $sg = (float) ($row['sgst_amount'] ?? 0);
        $cg = (float) ($row['cgst_amount'] ?? 0);
        $ig = (float) ($row['igst_amount'] ?? 0);
        $gstTot = (float) ($row['gst_amount'] ?? 0);
        if ($gstTot > 0.0001 && abs($sg + $cg + $ig) < 0.0001) {
            $sg = round($gstTot / 2, 2);
            $cg = round($gstTot - $sg, 2);
            $ig = 0.0;
        }

        $row['sgst_display'] = $sg;
        $row['cgst_display'] = $cg;
        $row['igst_display'] = $ig;
        $row['tds_section'] = $this->normalizeTdsSection((string) ($row['tds_label'] ?? ''));
        $row['tds_percent'] = $this->resolveTdsPercent($row);
        $row['utr_display'] = trim((string) ($row['utr'] ?? ''));
        if ($row['utr_display'] === '' || $row['utr_display'] === '—') {
            $row['utr_display'] = '—';
        }

        return $row;
    }

    private function normalizeTdsSection(string $label): string
    {
        if (preg_match('/\b(194\s*[ICD]?|206\s*C\w*)\b/i', $label, $matches)) {
            return strtoupper(preg_replace('/\s+/', '', $matches[1]));
        }

        $trimmed = trim($label);
        if ($trimmed !== '' && $trimmed !== '—' && $trimmed !== 'TDS') {
            return $trimmed;
        }

        return '194I';
    }

    /**
     * @param  array<string, mixed>  $row
     */
    private function resolveTdsPercent(array $row): ?float
    {
        $rate = (float) ($row['tax_rate'] ?? 0);
        if ($rate > 0.009) {
            return $rate > 1 ? round($rate, 2) : round($rate * 100, 2);
        }

        $tds = (float) ($row['tds_amount'] ?? 0);
        $sub = (float) ($row['sub_total'] ?? 0);
        if ($sub > 0.009 && $tds > 0.009) {
            return round($tds / $sub * 100, 2);
        }

        return null;
    }

    /**
     * @param  list<array<string, mixed>>  $rows
     * @return array<string, float|int>
     */
    /**
     * @param  list<array<string, mixed>>  $rows
     * @return array<string, float|int>
     */
    private function summarize(array $rows, string $activeTab): array
    {
        $collection = collect($rows);

        $sgstTotal = round((float) $collection->sum('sgst_display'), 2);
        $cgstTotal = round((float) $collection->sum('cgst_display'), 2);
        $igstTotal = round((float) $collection->sum('igst_display'), 2);
        $gstTotal = round((float) $collection->sum('gst_amount'), 2);
        if ($gstTotal <= 0.0001) {
            $gstTotal = round($sgstTotal + $cgstTotal + $igstTotal, 2);
        }

        return [
            'total_rows' => $collection->count(),
            'gross_total' => round((float) $collection->sum('gross_amount'), 2),
            'tds_total' => round((float) $collection->sum('tds_amount'), 2),
            'gst_total' => $gstTotal,
            'sgst_total' => $sgstTotal,
            'cgst_total' => $cgstTotal,
            'igst_total' => $igstTotal,
            'final_total' => round((float) $collection->sum('display_neft'), 2),
            'paid_total' => round((float) $collection->sum('amount_sent'), 2),
            'pending_total' => round((float) $collection->sum('pending_balance'), 2),
            'sub_total' => round((float) $collection->sum('sub_total'), 2),
            'active_tab' => $activeTab,
        ];
    }

    private function vendorDisplayName(Tblvendor $vendor): string
    {
        $label = trim((string) ($vendor->display_name ?? ''));
        if ($label === '') {
            $label = trim((string) ($vendor->company_name ?? ''));
        }

        return $label !== '' ? $label : 'Vendor #'.$vendor->id;
    }

    private function dateFrom(Request $request): ?string
    {
        if ($request->filled('date_from')) {
            return $request->date('date_from')->toDateString();
        }
        if ($request->filled('from')) {
            return $request->date('from')->toDateString();
        }

        return null;
    }

    private function dateTo(Request $request): ?string
    {
        if ($request->filled('date_to')) {
            return $request->date('date_to')->toDateString();
        }
        if ($request->filled('to')) {
            return $request->date('to')->toDateString();
        }

        return null;
    }

    /**
     * @return list<string>
     */
    private function filterLandlordNames(Request $request): array
    {
        $raw = $request->input('landlord', []);
        if (is_string($raw)) {
            $raw = $raw === '' ? [] : preg_split('/\s*,\s*/', $raw);
        }

        return array_values(array_unique(array_filter(
            array_map(static fn ($name): string => trim((string) $name), (array) $raw),
            static fn (string $name): bool => $name !== ''
        )));
    }

    /**
     * @return list<int>
     */
    private function filterIntIds(Request $request, string $key): array
    {
        $raw = $request->input($key, []);
        if (is_string($raw) || is_numeric($raw)) {
            $raw = [(string) $raw];
        }

        return array_values(array_unique(array_filter(
            array_map(static fn ($id): int => (int) $id, (array) $raw),
            static fn (int $id): bool => $id > 0
        )));
    }

    /**
     * @param  list<int>  $stateIds
     */
    /**
     * @param  Collection<int, array<string, mixed>>  $completed
     */
    private function sumCompletedByNature(Collection $completed, string $natureKey): float
    {
        return round((float) $completed
            ->where('nature_key', $natureKey)
            ->sum('display_neft'), 2);
    }

    /**
     * @param  Collection<int, array<string, mixed>>  $completed
     */
    private function sumTdsByNature(Collection $completed, string $natureKey): float
    {
        return round((float) $completed
            ->where('nature_key', $natureKey)
            ->sum('tds_amount'), 2);
    }

    private function chargeFilterToNatureKey(string $chargeFilter): ?string
    {
        return match ($chargeFilter) {
            'rental' => LandlordAdvanceVendorDashboard::NATURE_RENT_EXPENSES,
            'maintenance' => LandlordAdvanceVendorDashboard::NATURE_MAINTENANCE,
            'eb_bill' => LandlordAdvanceVendorDashboard::NATURE_EB_CHARGES,
            default => null,
        };
    }

    /**
     * @param  array<string, mixed>  $row
     * @return array<string, mixed>
     */
    private function toDashboardRecentRow(array $row, string $chargeFilter): array
    {
        $nature = (string) ($row['nature_key'] ?? '');
        $amount = (float) ($row['display_neft'] ?? 0);

        $rent = $nature === LandlordAdvanceVendorDashboard::NATURE_RENT_EXPENSES ? $amount : 0.0;
        $maint = $nature === LandlordAdvanceVendorDashboard::NATURE_MAINTENANCE ? $amount : 0.0;
        $eb = $nature === LandlordAdvanceVendorDashboard::NATURE_EB_CHARGES ? $amount : 0.0;

        if ($chargeFilter === 'rental') {
            $maint = $eb = 0.0;
        } elseif ($chargeFilter === 'maintenance') {
            $rent = $eb = 0.0;
        } elseif ($chargeFilter === 'eb_bill') {
            $rent = $maint = 0.0;
        } elseif ($chargeFilter === 'fixed') {
            $rent = $maint = $eb = 0.0;
        }

        return [
            'agreement' => $row['agreement'] ?? null,
            'agreement_type' => $row['agreement_type'] ?? null,
            'billing_month_label' => (string) ($row['payment_month'] ?? '—'),
            'rent_amount' => $rent,
            'maintenance_amount' => $maint,
            'fixed_amount' => 0.0,
            'eb_bill_amount' => $eb,
            'gross_amount' => (float) ($row['gross_amount'] ?? 0),
            'tds_amount' => (float) ($row['tds_amount'] ?? 0),
            'final_neft_amount' => $amount,
            'status_label' => (string) ($row['status_label'] ?? '—'),
        ];
    }

    private function applyBillStateGeoFilter(Builder $query, array $stateIds): void
    {
        $zoneIds = [];
        $branchIds = [];
        foreach ($stateIds as $sid) {
            match ($sid) {
                1 => $zoneIds = array_merge($zoneIds, [2, 4, 6, 7, 8, 9]),
                2 => $zoneIds[] = 3,
                3 => $zoneIds[] = 5,
                4 => $branchIds[] = 30,
                5 => $zoneIds[] = 10,
                default => null,
            };
        }
        $zoneIds = array_values(array_unique($zoneIds));
        $branchIds = array_values(array_unique($branchIds));

        if ($zoneIds === [] && $branchIds === []) {
            $query->whereRaw('1 = 0');

            return;
        }

        $query->where(function (Builder $q) use ($zoneIds, $branchIds) {
            if ($zoneIds !== []) {
                $q->whereIn('zone_id', $zoneIds);
            }
            if ($branchIds !== []) {
                if ($zoneIds !== []) {
                    $q->orWhereIn('branch_id', $branchIds);
                } else {
                    $q->whereIn('branch_id', $branchIds);
                }
            }
        });
    }
}
