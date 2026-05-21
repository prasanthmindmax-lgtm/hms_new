<?php

namespace App\Services;

use App\Models\RentalAgreement;
use App\Models\Tblvendor;

/**
 * Owner payment drill-down from bill module (bills, bill payments, NEFT).
 */
class RentalAgreementOwnerPaymentHistory
{
    public function __construct(
        private readonly VendorBillRentLedgerService $billLedger,
    ) {}

    public function buildForVendor(Tblvendor $vendor, ?string $category = null): array
    {
        $agreements = $this->billLedger->agreementsForVendor($vendor, $category);

        return $this->billLedger->buildForVendor(
            $vendor,
            $category,
            null,
            $agreements->isNotEmpty() ? $agreements : null,
            applyBillLocationFilter: false,
        );
    }

    public function build(RentalAgreement $agreement): array
    {
        $vendor = $this->resolveVendorForAgreement($agreement);
        if ($vendor !== null) {
            $payload = $this->billLedger->buildForVendor(
                $vendor,
                RentalAgreement::normalizeType((string) $agreement->agreement_type),
                null,
                collect([$agreement]),
                applyBillLocationFilter: false,
            );

            $agreementId = (int) $agreement->id;
            $agreementNumber = (string) $agreement->agreement_number;
            $filteredRows = collect($payload['rows'] ?? [])
                ->filter(function (array $row) use ($agreementId, $agreementNumber): bool {
                    $rowAgreementId = (int) ($row['rental_agreement_id'] ?? 0);
                    if ($rowAgreementId > 0) {
                        return $rowAgreementId === $agreementId;
                    }

                    $rowNumber = trim((string) ($row['agreement_number'] ?? ''));

                    return $rowNumber !== '' && $rowNumber !== '—' && $rowNumber === $agreementNumber;
                })
                ->values()
                ->all();

            if ($filteredRows !== []) {
                return $this->packageHistory(
                    scope: 'agreement',
                    ownerName: trim((string) $agreement->owner_name) ?: (string) ($payload['owner_name'] ?? ''),
                    agreementNumber: $agreementNumber,
                    agreementId: $agreementId,
                    vendorId: (int) $vendor->id,
                    agreements: [[
                        'id' => $agreementId,
                        'agreement_number' => $agreementNumber,
                        'agreement_type' => RentalAgreement::normalizeType((string) $agreement->agreement_type),
                    ]],
                    sortedRows: $filteredRows,
                    dataSource: (string) ($payload['data_source'] ?? 'bill_module'),
                );
            }
        }

        return $this->emptyAgreementHistory($agreement);
    }

    protected function resolveVendorForAgreement(RentalAgreement $agreement): ?Tblvendor
    {
        if ((int) ($agreement->vendor_id ?? 0) > 0) {
            $byId = Tblvendor::query()->find((int) $agreement->vendor_id);
            if ($byId !== null) {
                return $byId;
            }
        }

        return $this->billLedger->resolveVendorByOwnerName(trim((string) $agreement->owner_name));
    }

    /**
     * @return array<string, mixed>
     */
    protected function emptyAgreementHistory(RentalAgreement $agreement): array
    {
        $agreementId = (int) $agreement->id;
        $segment = RentalAgreement::normalizeType((string) $agreement->agreement_type);
        $agreementNumber = (string) $agreement->agreement_number;

        return $this->packageHistory(
            scope: 'agreement',
            ownerName: trim((string) $agreement->owner_name),
            agreementNumber: $agreementNumber,
            agreementId: $agreementId,
            vendorId: null,
            agreements: [[
                'id' => $agreementId,
                'agreement_number' => $agreementNumber,
                'agreement_type' => $segment,
            ]],
            sortedRows: [],
            advanceBalanceOverride: (float) $agreement->advance_amount,
            dataSource: 'none',
        );
    }

    /**
     * @param  list<array{id: int, agreement_number: string, agreement_type: string}>  $agreements
     * @param  list<array<string, mixed>>  $sortedRows
     * @return array<string, mixed>
     */
    protected function packageHistory(
        string $scope,
        string $ownerName,
        string $agreementNumber,
        ?int $agreementId,
        ?int $vendorId,
        array $agreements,
        array $sortedRows,
        ?float $advanceBalanceOverride = null,
        string $dataSource = 'bill_module',
    ): array {
        $sections = LandlordAdvanceVendorDashboard::buildLedgerSections($sortedRows);
        $rentExpense = $sections[LandlordAdvanceVendorDashboard::NATURE_RENT_EXPENSES]['summary'] ?? [];
        $maintenance = $sections[LandlordAdvanceVendorDashboard::NATURE_MAINTENANCE]['summary'] ?? [];
        $pendingLines = collect($sortedRows)->filter(fn (array $row) => (float) ($row['pending_balance'] ?? 0) > 0.009);

        $advanceBalance = $advanceBalanceOverride;
        if ($advanceBalance === null) {
            $advanceBalance = (float) collect($sortedRows)
                ->where('nature_key', LandlordAdvanceVendorDashboard::NATURE_RENT_ADVANCE)
                ->max('pending_balance');
        }

        return [
            'scope' => $scope,
            'owner_name' => $ownerName,
            'vendor_id' => $vendorId,
            'agreement_number' => $agreementNumber,
            'agreement_id' => $agreementId,
            'agreements' => $agreements,
            'data_source' => $dataSource,
            'sections' => $sections,
            'summary' => [
                'advance_balance' => round((float) $advanceBalance, 2),
                'rent_expense_pending' => (float) ($rentExpense['pending_balance'] ?? 0),
                'maintenance_pending' => (float) ($maintenance['pending_balance'] ?? 0),
                'total_amount_sent' => round((float) collect($sortedRows)->sum('amount_sent'), 2),
                'total_pending' => round((float) $pendingLines->sum('pending_balance'), 2),
                'completed_payments' => (int) ($rentExpense['completed_count'] ?? 0),
                'pending_lines' => $pendingLines->count(),
                'agreement_count' => count($agreements),
            ],
            'rows' => $sortedRows,
        ];
    }
}
