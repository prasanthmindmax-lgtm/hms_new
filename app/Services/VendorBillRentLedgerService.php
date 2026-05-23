<?php

namespace App\Services;

use App\Models\RentalAgreement;
use App\Models\Tblaccount;
use App\Models\Tblbill;
use App\Models\Tblbillpay;
use App\Models\TblBillLines;
use App\Models\Tblneft;
use App\Models\Tblvendor;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

/**
 * Vendor rent ledger from bill module (bill_tbl, bill_pay_tbl, tbl_neft_payment).
 */
class VendorBillRentLedgerService
{
    /** @var list<int>|null */
    private ?array $ledgerAccountIds = null;

    /** @var array<int, string> */
    private array $accountNameCache = [];

    /** @var Collection<int, RentalAgreement>|null */
    private ?Collection $agreementsForRows = null;

    public function buildForVendor(
        Tblvendor $vendor,
        ?string $category = null,
        ?array $locationFilter = null,
        ?Collection $scopedAgreements = null,
        bool $applyBillLocationFilter = true,
    ): array {
        $agreements = ($scopedAgreements !== null && $scopedAgreements->isNotEmpty())
            ? $scopedAgreements->values()
            : $this->agreementsForVendor($vendor, $category);

        if ($applyBillLocationFilter && $locationFilter === null && $agreements->isNotEmpty()) {
            $locationFilter = $this->locationFilterFromAgreements($agreements);
        }

        if (! $applyBillLocationFilter) {
            $locationFilter = null;
        }

        $this->agreementsForRows = $agreements;
        try {
            $rows = $this->collectBillModuleRows((int) $vendor->id, $locationFilter);
        } finally {
            $this->agreementsForRows = null;
        }

        return $this->package(
            scope: $agreements->count() > 1 ? 'vendor' : 'agreement',
            ownerName: $this->vendorDisplayName($vendor),
            vendorId: (int) $vendor->id,
            agreementNumber: $agreements->count() === 1 ? (string) $agreements->first()->agreement_number : '',
            agreementId: $agreements->count() === 1 ? (int) $agreements->first()->id : null,
            agreements: $agreements->map(fn (RentalAgreement $a) => [
                'id' => (int) $a->id,
                'agreement_number' => (string) $a->agreement_number,
                'agreement_type' => RentalAgreement::normalizeType((string) $a->agreement_type),
            ])->values()->all(),
            rows: $rows,
        );
    }

    /**
     * @return array<string, mixed>|null
     */
    public function buildForAgreement(RentalAgreement $agreement, ?Tblvendor $vendor = null): ?array
    {
        $vendor = $vendor ?? $this->resolveVendorByOwnerName(trim((string) $agreement->owner_name));
        if ($vendor === null) {
            return null;
        }

        $locationFilter = [
            'zone_id' => (int) $agreement->zone_id,
            'branch_id' => (int) $agreement->branch_id,
            'company_id' => (int) $agreement->company_id,
        ];

        $this->agreementsForRows = collect([$agreement]);
        try {
            $rows = $this->collectBillModuleRows((int) $vendor->id, $locationFilter);
        } finally {
            $this->agreementsForRows = null;
        }

        return $this->package(
            scope: 'agreement',
            ownerName: trim((string) $agreement->owner_name) ?: $this->vendorDisplayName($vendor),
            vendorId: (int) $vendor->id,
            agreementNumber: (string) $agreement->agreement_number,
            agreementId: (int) $agreement->id,
            agreements: [[
                'id' => (int) $agreement->id,
                'agreement_number' => (string) $agreement->agreement_number,
                'agreement_type' => RentalAgreement::normalizeType((string) $agreement->agreement_type),
            ]],
            rows: $rows,
        );
    }

    public function resolveVendorByOwnerName(string $ownerName): ?Tblvendor
    {
        $key = mb_strtolower(trim($ownerName));
        if ($key === '') {
            return null;
        }

        return Tblvendor::query()
            ->activeLandlords()
            ->where(function (Builder $q) use ($key, $ownerName) {
                $q->whereRaw('LOWER(TRIM(COALESCE(display_name, ""))) = ?', [$key])
                    ->orWhereRaw('LOWER(TRIM(COALESCE(company_name, ""))) = ?', [$key])
                    ->orWhereRaw('LOWER(TRIM(COALESCE(display_name, company_name, ""))) = ?', [$key]);
            })
            ->first();
    }

    protected function collectBillModuleRows(int $vendorId, ?array $locationFilter): array
    {
        $rows = collect();

        $bills = $this->billQuery($vendorId, $locationFilter)
            ->with(['BillLines', 'TblTDSsection'])
            ->orderByDesc('id')
            ->get();

        foreach ($bills as $bill) {
            $rows->push($this->rowFromBill($bill));
        }

        $nefts = $this->neftQuery($vendorId, $locationFilter)
            ->with(['BillLines.Bill.BillLines', 'BillLines.Bill.TblTDSsection', 'Tblbillpay'])
            ->orderByDesc('id')
            ->get();

        foreach ($nefts as $neft) {
            $natureKey = $this->natureKeyFromLabel((string) $neft->nature_payment);
            if ($natureKey === LandlordAdvanceVendorDashboard::NATURE_RENT_EXPENSES) {
                continue;
            }
            $rows->push($this->rowFromNeft($neft));
        }

        $billPays = $this->billPayQuery($vendorId, $locationFilter)
            ->with(['BillLines.Bill.BillLines', 'BillLines.Bill.TblTDSsection'])
            ->orderByDesc('id')
            ->get();

        foreach ($billPays as $billPay) {
            if (! $this->billPayHasRentNature($billPay)) {
                continue;
            }
            $natureKey = $this->natureKeyFromBillPay($billPay);
            if ($natureKey === LandlordAdvanceVendorDashboard::NATURE_RENT_EXPENSES) {
                continue;
            }
            $rows->push($this->rowFromBillPay($billPay));
        }

        return $rows
            ->sortByDesc(fn (array $row) => (int) ($row['sort_key'] ?? 0))
            ->values()
            ->all();
    }

    /**
     * Vendor-wide bill totals (matches vendor Transactions tab).
     *
     * @return array{bill_count: int, bill_gross_total: float, bill_paid_total: float, bill_due_total: float, payment_count: int, payment_total: float}
     */
    public function vendorBillSummary(int $vendorId): array
    {
        $billBase = Tblbill::query()
            ->where('vendor_id', $vendorId)
            ->where('delete_status', 0);

        $billGross = round((float) (clone $billBase)->sum('grand_total_amount'), 2);
        $billPaid = round((float) (clone $billBase)->sum('partially_payment'), 2);

        $payBase = Tblbillpay::query()
            ->where('vendor_id', $vendorId)
            ->where('delete_status', 0);

        return [
            'bill_count' => (int) (clone $billBase)->count(),
            'bill_gross_total' => $billGross,
            'bill_paid_total' => $billPaid,
            'bill_due_total' => round($billGross - $billPaid, 2),
            'payment_count' => (int) (clone $payBase)->count(),
            'payment_total' => round((float) (clone $payBase)->sum('amount_used'), 2),
        ];
    }

    protected function rowFromBill(Tblbill $bill): array
    {
        $billDate = $this->parseBillModuleDate($bill->bill_date) ?? $bill->created_at;
        $billRef = trim((string) ($bill->bill_gen_number ?: $bill->bill_number ?: '—'));
        $grand = round((float) $bill->grand_total_amount, 2);
        $paid = round((float) ($bill->partially_payment ?? 0), 2);
        $due = round(max(0.0, $grand - $paid), 2);
        $natureKey = $this->natureKeyFromBill($bill);
        $isPaid = $due <= 0.009;
        $statusLabel = trim((string) ($bill->bill_status ?? ''));
        if ($statusLabel === '') {
            $statusLabel = $isPaid ? 'Paid' : 'Due to pay';
        }

        return array_merge($this->agreementMetaFromLocation(
            (int) $bill->zone_id,
            (int) $bill->branch_id,
            (int) $bill->company_id,
        ), [
            'sort_key' => $billDate instanceof Carbon ? $billDate->timestamp : 0,
            'line_type' => 'bill',
            'payment_month' => $billDate instanceof Carbon ? $billDate->format('M Y') : '—',
            'payment_purpose' => $this->natureLabel($natureKey).' (Bill)',
            'nature_key' => $natureKey,
            'amount_sent' => $paid,
            'bill_payment_made' => $paid > 0.009 && $billDate instanceof Carbon ? $billDate->format('d M Y') : '—',
            'pending_balance' => $due,
            'status' => $isPaid ? 'completed' : 'pending',
            'status_label' => $statusLabel,
            'payment_mode' => '—',
            'utr' => '—',
            'detail_url' => route('superadmin.getbillprint', ['id' => $bill->id]),
            'bill_ref' => $billRef,
            'bill_id' => (int) $bill->id,
            'data_source' => 'bill',
            'due_date' => $this->formatBillDueDate($bill),
        ], $this->financialsForBill($bill, $grand));
    }

    protected function financialsForBill(Tblbill $bill, float $netPayable): array
    {
        $scopedLines = $bill->BillLines->filter(
            fn (TblBillLines $line) => $this->lineMatchesLedgerScope($line)
        );

        if ($scopedLines->isEmpty()) {
            $fin = $this->emptyFinancials();
            $fin['net_payable'] = $netPayable;

            return $fin;
        }

        $lineSub = round((float) $scopedLines->sum('amount'), 2);
        $gst = round((float) $scopedLines->sum(fn (TblBillLines $line) => $this->lineGstAmount($line)), 2);
        $cgst = round((float) $scopedLines->sum('cgst_amount'), 2);
        $sgst = round((float) $scopedLines->sum('sgst_amount'), 2);
        $tds = round((float) ($bill->tax_amount ?? 0), 2);
        $esi = round((float) ($bill->esi_amount ?? 0), 2);
        $pf = round((float) ($bill->pf_amount ?? 0), 2);
        $other = round((float) ($bill->other_amount ?? 0), 2);
        $gross = round($lineSub + $gst, 2);
        $igst = max(0.0, round($gst - $cgst - $sgst, 2));

        return [
            'sub_total' => $lineSub,
            'gst_amount' => $gst,
            'cgst_amount' => $cgst,
            'sgst_amount' => $sgst,
            'igst_amount' => $igst,
            'tds_amount' => $tds,
            'tds_label' => $this->tdsLabelFromBill($bill),
            'tax_rate' => (float) ($bill->tax_rate ?? 0),
            'gross_amount' => $gross,
            'net_payable' => $netPayable,
            'other_deductions' => round($other + $esi + $pf, 2),
            'esi_amount' => $esi,
            'pf_amount' => $pf,
        ];
    }

    protected function neftQuery(int $vendorId, ?array $locationFilter): Builder
    {
        $q = Tblneft::query()
            ->where('vendor_id', $vendorId)
            ->where('delete_status', 0)
            ->where(function (Builder $wrap) {
                $wrap->where(function (Builder $n) {
                    $n->whereRaw('UPPER(COALESCE(nature_payment, "")) LIKE ?', ['%RENT ADVANCE%'])
                        ->orWhereRaw('UPPER(COALESCE(nature_payment, "")) LIKE ?', ['%RENT EXPENSE%'])
                        ->orWhereRaw('UPPER(COALESCE(nature_payment, "")) LIKE ?', ['%MAINTENANCE%'])
                        ->orWhereRaw('UPPER(COALESCE(nature_payment, "")) LIKE ?', ['%ELECTRIC%'])
                        ->orWhereRaw('UPPER(COALESCE(nature_payment, "")) LIKE ?', ['%EB%']);
                })->orWhereHas('BillLines', function (Builder $lines) {
                    $lines->whereHas('Tblbilllines', fn (Builder $bl) => $this->applyLandlordLedgerAccountScope($bl));
                });
            });

        return $this->applyLocationFilter($q, $locationFilter);
    }

    protected function billPayQuery(int $vendorId, ?array $locationFilter): Builder
    {
        $q = Tblbillpay::query()
            ->where('vendor_id', $vendorId)
            ->where('delete_status', 0)
            ->whereHas('BillLines', function (Builder $lines) {
                $lines->whereHas('Bill', function (Builder $bill) {
                    $bill->where('delete_status', 0)
                        ->whereHas('BillLines', fn (Builder $bl) => $this->applyLandlordLedgerAccountScope($bl));
                });
            });

        return $this->applyLocationFilter($q, $locationFilter);
    }

    protected function billQuery(int $vendorId, ?array $locationFilter): Builder
    {
        $q = Tblbill::query()
            ->where('vendor_id', $vendorId)
            ->where('delete_status', 0)
            ->whereHas('BillLines', fn (Builder $bl) => $this->applyLandlordLedgerAccountScope($bl));

        return $this->applyLocationFilter($q, $locationFilter);
    }

    protected function applyLocationFilter(Builder $q, ?array $locationFilter): Builder
    {
        if ($locationFilter === null) {
            return $q;
        }
        if (! empty($locationFilter['zone_id'])) {
            $q->where('zone_id', (int) $locationFilter['zone_id']);
        }
        if (! empty($locationFilter['branch_id'])) {
            $q->where('branch_id', (int) $locationFilter['branch_id']);
        }
        if (! empty($locationFilter['company_id'])) {
            $q->where('company_id', (int) $locationFilter['company_id']);
        }

        return $q;
    }

    protected function applyLandlordLedgerAccountScope(Builder $q): void
    {
        $ids = $this->ledgerAccountIds();
        $q->where(function (Builder $sub) use ($ids) {
            if ($ids !== []) {
                $sub->whereIn('account_id', $ids);
            }
            $sub->orWhereRaw('UPPER(COALESCE(account, "")) LIKE ?', ['%RENT ADVANCE%'])
                ->orWhereRaw('UPPER(COALESCE(account, "")) LIKE ?', ['%RENT EXPENSE%'])
                ->orWhereRaw('UPPER(COALESCE(account, "")) LIKE ?', ['%MAINTENANCE%'])
                ->orWhereRaw('UPPER(COALESCE(account, "")) LIKE ?', ['%ELECTRIC%'])
                ->orWhereRaw('UPPER(COALESCE(account, "")) LIKE ?', ['%EB BILL%'])
                ->orWhereRaw('UPPER(COALESCE(account, "")) LIKE ?', ['%EB %']);
        });
    }

    protected function ledgerAccountIds(): array
    {
        if ($this->ledgerAccountIds !== null) {
            return $this->ledgerAccountIds;
        }

        $this->ledgerAccountIds = Tblaccount::query()
            ->where(function (Builder $q) {
                $q->whereRaw('UPPER(name) LIKE ?', ['%RENT ADVANCE%'])
                    ->orWhereRaw('UPPER(name) LIKE ?', ['%RENT EXPENSE%'])
                    ->orWhereRaw('UPPER(name) LIKE ?', ['%MAINTENANCE%'])
                    ->orWhereRaw('UPPER(name) LIKE ?', ['%ELECTRIC%'])
                    ->orWhereRaw('UPPER(name) LIKE ?', ['%EB%']);
            })
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->all();

        return $this->ledgerAccountIds;
    }

    protected function openBillRowsByNature(Tblbill $bill, float $due): array
    {
        $billDate = $this->parseBillModuleDate($bill->bill_date) ?? $bill->created_at;
        $billRef = trim((string) ($bill->bill_gen_number ?: $bill->bill_number ?: '—'));
        $groups = [];

        foreach ($bill->BillLines as $line) {
            if (! $this->lineMatchesLedgerScope($line)) {
                continue;
            }
            $key = $this->lineNatureKey($line);
            $groups[$key] = ($groups[$key] ?? 0.0) + (float) ($line->amount ?? 0);
        }

        if ($groups === []) {
            return [];
        }

        $lineTotal = array_sum($groups);
        $rows = [];

        foreach ($groups as $natureKey => $lineSum) {
            $pending = $lineTotal > 0.009
                ? round($due * ($lineSum / $lineTotal), 2)
                : 0.0;
            if ($pending <= 0.009) {
                continue;
            }

            $rows[] = array_merge($this->agreementMetaFromLocation(
                (int) $bill->zone_id,
                (int) $bill->branch_id,
                (int) $bill->company_id,
            ), [
                'sort_key' => $billDate instanceof Carbon ? $billDate->timestamp : 0,
                'line_type' => 'bill',
                'payment_month' => $billDate instanceof Carbon ? $billDate->format('M Y') : '—',
                'payment_purpose' => $this->natureLabel($natureKey).' (Bill — due)',
                'nature_key' => $natureKey,
                'amount_sent' => 0.0,
                'bill_payment_made' => '—',
                'pending_balance' => $pending,
                'status' => 'pending',
                'status_label' => trim((string) ($bill->bill_status ?? '')) ?: 'Due',
                'payment_mode' => '—',
                'utr' => '—',
                'detail_url' => route('superadmin.getbillprint', ['id' => $bill->id]),
                'bill_ref' => $billRef,
                'bill_id' => (int) $bill->id,
                'data_source' => 'bill',
                'due_date' => $this->formatBillDueDate($bill),
            ], $this->financialsForBillNature($bill, $natureKey, $pending));
        }

        return $rows;
    }

    protected function agreementMetaFromLocation(int $zoneId, int $branchId, int $companyId): array
    {
        $agreements = $this->agreementsForRows;
        if ($agreements === null || $agreements->isEmpty()) {
            return ['rental_agreement_id' => null, 'agreement_number' => '—'];
        }

        if ($agreements->count() === 1) {
            $agreement = $agreements->first();

            return [
                'rental_agreement_id' => (int) $agreement->id,
                'agreement_number' => (string) $agreement->agreement_number,
            ];
        }

        $match = $this->matchAgreementByLocation($agreements, $zoneId, $branchId, $companyId);
        if ($match === null) {
            return ['rental_agreement_id' => null, 'agreement_number' => '—'];
        }

        return [
            'rental_agreement_id' => (int) $match->id,
            'agreement_number' => (string) $match->agreement_number,
        ];
    }

    protected function matchAgreementByLocation(
        Collection $agreements,
        int $zoneId,
        int $branchId,
        int $companyId,
    ): ?RentalAgreement {
        $best = null;
        $bestScore = 0;

        foreach ($agreements as $agreement) {
            $score = 0;
            if ($zoneId > 0 && (int) $agreement->zone_id === $zoneId) {
                $score++;
            }
            if ($branchId > 0 && (int) $agreement->branch_id === $branchId) {
                $score++;
            }
            if ($companyId > 0 && (int) $agreement->company_id === $companyId) {
                $score++;
            }
            if ($score > $bestScore) {
                $bestScore = $score;
                $best = $agreement;
            }
        }

        return $bestScore > 0 ? $best : null;
    }

    protected function rowFromNeft(Tblneft $neft): array
    {
        $payable = round((float) $neft->BillLines->sum('only_payable'), 2);
        $isSuccess = strtolower(trim((string) $neft->payment_status)) === 'success';
        $natureKey = $this->natureKeyFromLabel((string) $neft->nature_payment);
        $billDate = $this->parseBillModuleDate($neft->bill_date) ?? $neft->created_at;
        $billPay = $neft->Tblbillpay;
        $billPaidAt = $billPay ? ($this->parseBillModuleDate($billPay->payment_date) ?? $billPay->created_at) : null;
        $billNumbers = $this->billNumbersFromNeft($neft);

        return array_merge($this->agreementMetaFromLocation(
            (int) $neft->zone_id,
            (int) $neft->branch_id,
            (int) $neft->company_id,
        ), [
            'sort_key' => $billPaidAt instanceof Carbon ? $billPaidAt->timestamp : ($billDate instanceof Carbon ? $billDate->timestamp : 0),
            'line_type' => 'neft',
            'payment_month' => $billDate instanceof Carbon ? $billDate->format('M Y') : '—',
            'payment_purpose' => $this->natureLabel($natureKey).' (NEFT)',
            'nature_key' => $natureKey,
            'amount_sent' => $isSuccess ? $payable : 0.0,
            'bill_payment_made' => $isSuccess && $billPaidAt instanceof Carbon ? $billPaidAt->format('d M Y') : '—',
            'pending_balance' => $isSuccess ? 0.0 : $payable,
            'status' => $isSuccess ? 'completed' : 'pending',
            'status_label' => $isSuccess ? 'Paid' : (trim((string) $neft->payment_status) ?: 'Pending'),
            'payment_mode' => strtoupper(trim((string) ($neft->payment_method ?: 'NEFT'))),
            'utr' => trim((string) ($neft->utr_number ?? '')) ?: '—',
            'detail_url' => $neft->bill_pay_id
                ? route('superadmin.getbillmadeprint', ['id' => $neft->bill_pay_id])
                : route('superadmin.getneftdashboard'),
            'bill_ref' => trim((string) ($neft->serial_number ?? '')),
            'data_source' => 'bill_neft',
            'due_date' => $billDate instanceof Carbon ? $billDate->format('d M Y') : '—',
        ], $this->financialsFromNeft($neft, $natureKey, $payable));
    }

    protected function rowFromBillPay(Tblbillpay $billPay): array
    {
        $amount = round((float) ($billPay->amount_used ?: $billPay->amount_paid), 2);
        $paidAt = $this->parseBillModuleDate($billPay->payment_date) ?? $billPay->created_at;
        $natureKey = $this->natureKeyFromBillPay($billPay);
        $billNumbers = $billPay->BillLines->pluck('bill_number')->filter()->unique()->implode(', ');

        return array_merge($this->agreementMetaFromLocation(
            (int) $billPay->zone_id,
            (int) $billPay->branch_id,
            (int) $billPay->company_id,
        ), [
            'sort_key' => $paidAt instanceof Carbon ? $paidAt->timestamp : 0,
            'line_type' => 'bill_pay',
            'payment_month' => $paidAt instanceof Carbon ? $paidAt->format('M Y') : '—',
            'payment_purpose' => $this->natureLabel($natureKey).' (Bill payment)',
            'nature_key' => $natureKey,
            'amount_sent' => $amount,
            'bill_payment_made' => $paidAt instanceof Carbon ? $paidAt->format('d M Y') : '—',
            'pending_balance' => 0.0,
            'status' => 'completed',
            'status_label' => trim((string) ($billPay->save_status ?? '')) ?: 'Paid',
            'payment_mode' => strtoupper(trim((string) ($billPay->payment_mode ?: '—'))),
            'utr' => trim((string) ($billPay->reference ?? '')) ?: '—',
            'detail_url' => route('superadmin.getbillmadeprint', ['id' => $billPay->id]),
            'bill_ref' => trim((string) ($billPay->payment_gen_order ?? '')),
            'data_source' => 'bill_pay',
            'due_date' => $paidAt instanceof Carbon ? $paidAt->format('d M Y') : '—',
        ], $this->financialsFromBillPay($billPay, $natureKey, $amount));
    }

    protected function billNumbersFromNeft(Tblneft $neft): string
    {
        $numbers = [];
        foreach ($neft->BillLines as $line) {
            if ($line->relationLoaded('Bill') && $line->Bill) {
                $n = trim((string) ($line->Bill->bill_gen_number ?? $line->Bill->bill_number ?? ''));
                if ($n !== '') {
                    $numbers[] = $n;
                }
            }
        }

        return implode(', ', array_unique($numbers));
    }

    protected function billPayHasRentNature(Tblbillpay $billPay): bool
    {
        foreach ($billPay->BillLines as $line) {
            $bill = $line->Bill;
            if ($bill && $this->billHasRentNature($bill)) {
                return true;
            }
        }

        return false;
    }

    protected function billHasRentNature(Tblbill $bill): bool
    {
        foreach ($bill->BillLines as $line) {
            if ($this->lineMatchesLedgerScope($line)) {
                return true;
            }
        }

        return false;
    }

    protected function lineMatchesLedgerScope(TblBillLines $line): bool
    {
        $ids = $this->ledgerAccountIds();
        if ($ids !== [] && in_array((int) $line->account_id, $ids, true)) {
            return true;
        }

        return $this->accountLabelMatchesLedgerScope($this->normalizedAccountLabel($line));
    }

    protected function normalizedAccountLabel(TblBillLines $line): string
    {
        $account = strtoupper(trim((string) ($line->account ?? '')));
        if ($account !== '') {
            return $account;
        }

        $accountId = (int) ($line->account_id ?? 0);
        if ($accountId <= 0) {
            return '';
        }

        if (! array_key_exists($accountId, $this->accountNameCache)) {
            $this->accountNameCache[$accountId] = strtoupper(trim((string) Tblaccount::query()
                ->where('id', $accountId)
                ->value('name')));
        }

        return $this->accountNameCache[$accountId];
    }

    protected function accountLabelMatchesLedgerScope(string $account): bool
    {
        if ($account === '') {
            return false;
        }

        return str_contains($account, 'RENT ADVANCE')
            || str_contains($account, 'RENT EXPENSE')
            || str_contains($account, 'MAINTENANCE')
            || str_contains($account, 'ELECTRIC')
            || str_contains($account, 'EB');
    }

    protected function lineNatureKey(TblBillLines $line): string
    {
        $account = $this->normalizedAccountLabel($line);

        if (str_contains($account, 'RENT ADVANCE')) {
            return LandlordAdvanceVendorDashboard::NATURE_RENT_ADVANCE;
        }
        if (str_contains($account, 'RENT EXPENSE')) {
            return LandlordAdvanceVendorDashboard::NATURE_RENT_EXPENSES;
        }
        if (str_contains($account, 'MAINTENANCE')) {
            return LandlordAdvanceVendorDashboard::NATURE_MAINTENANCE;
        }
        if (str_contains($account, 'ELECTRIC') || str_contains($account, 'EB')) {
            return LandlordAdvanceVendorDashboard::NATURE_EB_CHARGES;
        }

        return LandlordAdvanceVendorDashboard::NATURE_RENT_EXPENSES;
    }

    protected function natureKeyFromBillPay(Tblbillpay $billPay): string
    {
        foreach ($billPay->BillLines as $line) {
            $bill = $line->Bill;
            if ($bill) {
                $key = $this->natureKeyFromBill($bill);
                if ($key !== '') {
                    return $key;
                }
            }
        }

        return LandlordAdvanceVendorDashboard::NATURE_RENT_EXPENSES;
    }

    protected function natureKeyFromBill(Tblbill $bill): string
    {
        foreach ($bill->BillLines as $line) {
            if ($this->lineMatchesLedgerScope($line)) {
                return $this->lineNatureKey($line);
            }
        }

        return LandlordAdvanceVendorDashboard::NATURE_RENT_EXPENSES;
    }

    protected function natureKeyFromLabel(string $label): string
    {
        $u = strtoupper(trim($label));
        if (str_contains($u, 'RENT ADVANCE')) {
            return LandlordAdvanceVendorDashboard::NATURE_RENT_ADVANCE;
        }
        if (str_contains($u, 'MAINTENANCE')) {
            return LandlordAdvanceVendorDashboard::NATURE_MAINTENANCE;
        }
        if (str_contains($u, 'ELECTRIC') || preg_match('/\bEB\b/', $u)) {
            return LandlordAdvanceVendorDashboard::NATURE_EB_CHARGES;
        }
        if (str_contains($u, 'RENT EXPENSE')) {
            return LandlordAdvanceVendorDashboard::NATURE_RENT_EXPENSES;
        }

        return LandlordAdvanceVendorDashboard::NATURE_RENT_EXPENSES;
    }

    protected function natureLabel(string $natureKey): string
    {
        return LandlordAdvanceVendorDashboard::NATURE_LABELS[$natureKey]
            ?? strtoupper($natureKey);
    }

    protected function parseBillModuleDate(mixed $raw): ?Carbon
    {
        if ($raw instanceof Carbon) {
            return $raw;
        }
        $s = trim((string) $raw);
        if ($s === '') {
            return null;
        }
        foreach (['d/m/Y', 'd-m-Y', 'Y-m-d'] as $fmt) {
            try {
                return Carbon::createFromFormat($fmt, $s)->startOfDay();
            } catch (\Throwable $e) {
                continue;
            }
        }
        try {
            return Carbon::parse($s);
        } catch (\Throwable $e) {
            return null;
        }
    }

    protected function locationFilterFromAgreements(Collection $agreements): ?array
    {
        if ($agreements->count() !== 1) {
            return null;
        }

        $a = $agreements->first();

        return [
            'zone_id' => (int) $a->zone_id,
            'branch_id' => (int) $a->branch_id,
            'company_id' => (int) $a->company_id,
        ];
    }

    public function agreementsForVendor(Tblvendor $vendor, ?string $category): Collection
    {
        $labels = $this->vendorMatchLabels($vendor);
        if ($labels === []) {
            return collect();
        }

        $vendorId = (int) $vendor->id;

        $query = RentalAgreement::query()->where(function ($q) use ($labels, $vendorId) {
            $q->where('vendor_id', $vendorId);
            foreach ($labels as $label) {
                $q->orWhereRaw('LOWER(TRIM(owner_name)) = ?', [mb_strtolower($label)]);
            }
        });

        if ($category !== null && in_array($category, RentalAgreement::TYPES, true)) {
            $query->where('agreement_type', $category);
        }

        return $query->orderBy('agreement_number')->get();
    }

    protected function vendorMatchLabels(Tblvendor $vendor): array
    {
        $labels = [];
        foreach ([$vendor->display_name, $vendor->company_name] as $name) {
            $t = trim((string) $name);
            if ($t !== '') {
                $labels[] = $t;
            }
        }

        return array_values(array_unique($labels));
    }

    protected function vendorDisplayName(Tblvendor $vendor): string
    {
        $label = trim((string) ($vendor->display_name ?? ''));
        if ($label === '') {
            $label = trim((string) ($vendor->company_name ?? ''));
        }

        return $label !== '' ? $label : 'Vendor #'.$vendor->id;
    }

    protected function emptyFinancials(): array
    {
        return [
            'sub_total' => 0.0,
            'gst_amount' => 0.0,
            'cgst_amount' => 0.0,
            'sgst_amount' => 0.0,
            'igst_amount' => 0.0,
            'tds_amount' => 0.0,
            'tds_label' => '—',
            'tax_rate' => 0.0,
            'gross_amount' => 0.0,
            'net_payable' => 0.0,
            'other_deductions' => 0.0,
            'esi_amount' => 0.0,
            'pf_amount' => 0.0,
        ];
    }

    protected function lineGstAmount(TblBillLines $line): float
    {
        $gst = (float) ($line->gst_amount ?? 0);
        if ($gst > 0.009) {
            return $gst;
        }

        return (float) ($line->cgst_amount ?? 0) + (float) ($line->sgst_amount ?? 0);
    }

    protected function formatBillDueDate(Tblbill $bill): string
    {
        $due = $this->parseBillModuleDate($bill->due_date);
        if ($due instanceof Carbon) {
            return $due->format('d M Y');
        }
        $billDate = $this->parseBillModuleDate($bill->bill_date);

        return $billDate instanceof Carbon ? $billDate->format('d M Y') : '—';
    }

    protected function tdsLabelFromBill(Tblbill $bill): string
    {
        $type = trim((string) ($bill->tax_type ?? ''));
        if ($type !== '') {
            return $type;
        }
        $name = trim((string) ($bill->tax_name ?? ''));
        if ($name !== '') {
            return $name;
        }
        if ($bill->relationLoaded('TblTDSsection') && $bill->TblTDSsection) {
            $section = trim((string) ($bill->TblTDSsection->section_name ?? ''));

            return $section !== '' ? $section : 'TDS';
        }

        return 'TDS';
    }

    protected function financialsForBillNature(Tblbill $bill, string $natureKey, float $netPayable): array
    {
        $scopedLines = $bill->BillLines->filter(
            fn (TblBillLines $line) => $this->lineMatchesLedgerScope($line) && $this->lineNatureKey($line) === $natureKey
        );
        $allScoped = $bill->BillLines->filter(fn (TblBillLines $line) => $this->lineMatchesLedgerScope($line));

        if ($scopedLines->isEmpty()) {
            return $this->emptyFinancials();
        }

        $lineSub = round((float) $scopedLines->sum('amount'), 2);
        $totalSub = round((float) $allScoped->sum('amount'), 2);
        $ratio = $totalSub > 0.009 ? ($lineSub / $totalSub) : 1.0;

        $gst = round((float) $scopedLines->sum(fn (TblBillLines $line) => $this->lineGstAmount($line)), 2);
        $cgst = round((float) $scopedLines->sum('cgst_amount'), 2);
        $sgst = round((float) $scopedLines->sum('sgst_amount'), 2);
        $tds = round((float) ($bill->tax_amount ?? 0) * $ratio, 2);
        $esi = round((float) ($bill->esi_amount ?? 0) * $ratio, 2);
        $pf = round((float) ($bill->pf_amount ?? 0) * $ratio, 2);
        $other = round((float) ($bill->other_amount ?? 0) * $ratio, 2);
        $gross = round($lineSub + $gst, 2);
        $net = $netPayable > 0.009 ? $netPayable : round((float) ($bill->grand_total_amount ?? 0) * $ratio, 2);
        $igst = max(0.0, round($gst - $cgst - $sgst, 2));

        return [
            'sub_total' => $lineSub,
            'gst_amount' => $gst,
            'cgst_amount' => $cgst,
            'sgst_amount' => $sgst,
            'igst_amount' => $igst,
            'tds_amount' => $tds,
            'tds_label' => $this->tdsLabelFromBill($bill),
            'tax_rate' => (float) ($bill->tax_rate ?? 0),
            'gross_amount' => $gross,
            'net_payable' => $net,
            'other_deductions' => round($other + $esi + $pf, 2),
            'esi_amount' => $esi,
            'pf_amount' => $pf,
        ];
    }

    /**
     * @return array<string, float|string>
     */
    protected function financialsFromNeft(Tblneft $neft, string $natureKey, float $netPayable): array
    {
        $lines = $neft->BillLines;
        if ($lines->isEmpty()) {
            $fin = $this->emptyFinancials();
            $fin['net_payable'] = $netPayable;

            return $fin;
        }

        $sub = round((float) $lines->sum(fn ($line) => (float) ($line->invoice_amount ?? 0) - (float) ($line->gst_amount ?? 0) - (float) ($line->tax_amount ?? 0)), 2);
        if ($sub < 0) {
            $sub = round((float) $lines->sum('invoice_amount'), 2);
        }
        $gst = round((float) $lines->sum('gst_amount'), 2);
        $tds = round((float) $lines->sum('tax_amount'), 2);
        $cgst = 0.0;
        $sgst = 0.0;
        $igst = 0.0;
        $other = 0.0;
        $esi = 0.0;
        $pf = 0.0;
        $taxRate = 0.0;

        foreach ($lines as $nLine) {
            if (! $nLine->relationLoaded('Bill') || ! $nLine->Bill) {
                continue;
            }
            $bill = $nLine->Bill;
            $billFin = $this->financialsForBillNature($bill, $natureKey, 0.0);
            $cgst += (float) ($billFin['cgst_amount'] ?? 0);
            $sgst += (float) ($billFin['sgst_amount'] ?? 0);
            $igst += (float) ($billFin['igst_amount'] ?? 0);
            $other += (float) ($billFin['other_deductions'] ?? 0);
            $esi += (float) ($billFin['esi_amount'] ?? 0);
            $pf += (float) ($billFin['pf_amount'] ?? 0);
            if ($taxRate <= 0.009) {
                $taxRate = (float) ($billFin['tax_rate'] ?? 0);
            }
        }

        $gross = round($sub + $gst, 2);
        $net = $netPayable > 0.009 ? $netPayable : round((float) $lines->sum('only_payable'), 2);
        $tdsLabel = 'TDS';
        $firstBill = $lines->first(fn ($l) => $l->relationLoaded('Bill') && $l->Bill);
        if ($firstBill && $firstBill->Bill) {
            $tdsLabel = $this->tdsLabelFromBill($firstBill->Bill);
        }
        if ($igst <= 0.009 && $gst > 0.009) {
            $igst = max(0.0, round($gst - $cgst - $sgst, 2));
        }

        return [
            'sub_total' => $sub,
            'gst_amount' => $gst,
            'cgst_amount' => round($cgst, 2),
            'sgst_amount' => round($sgst, 2),
            'igst_amount' => round($igst, 2),
            'tds_amount' => $tds,
            'tds_label' => $tdsLabel,
            'tax_rate' => $taxRate,
            'gross_amount' => $gross,
            'net_payable' => $net,
            'other_deductions' => round($other, 2),
            'esi_amount' => round($esi, 2),
            'pf_amount' => round($pf, 2),
        ];
    }

    /**
     * @return array<string, float|string>
     */
    protected function financialsFromBillPay(Tblbillpay $billPay, string $natureKey, float $amountPaid): array
    {
        $sub = 0.0;
        $gst = 0.0;
        $cgst = 0.0;
        $sgst = 0.0;
        $igst = 0.0;
        $tds = 0.0;
        $gross = 0.0;
        $other = 0.0;
        $esi = 0.0;
        $pf = 0.0;
        $taxRate = 0.0;
        $tdsLabel = 'TDS';
        $seenBills = [];

        foreach ($billPay->BillLines as $payLine) {
            $bill = $payLine->Bill;
            if ($bill === null || isset($seenBills[(int) $bill->id])) {
                continue;
            }
            $seenBills[(int) $bill->id] = true;
            $portion = (float) ($payLine->amount ?? 0);
            $billFin = $this->financialsForBillNature($bill, $natureKey, $portion);
            if ((float) ($billFin['sub_total'] ?? 0) <= 0.009) {
                continue;
            }
            $sub += (float) $billFin['sub_total'];
            $gst += (float) $billFin['gst_amount'];
            $cgst += (float) $billFin['cgst_amount'];
            $sgst += (float) $billFin['sgst_amount'];
            $igst += (float) ($billFin['igst_amount'] ?? 0);
            $tds += (float) $billFin['tds_amount'];
            $gross += (float) $billFin['gross_amount'];
            $other += (float) $billFin['other_deductions'];
            $esi += (float) $billFin['esi_amount'];
            $pf += (float) $billFin['pf_amount'];
            $tdsLabel = (string) ($billFin['tds_label'] ?? $tdsLabel);
            if ($taxRate <= 0.009) {
                $taxRate = (float) ($billFin['tax_rate'] ?? 0);
            }
        }

        if ($sub <= 0.009) {
            $fin = $this->emptyFinancials();
            $fin['net_payable'] = $amountPaid;

            return $fin;
        }

        if ($igst <= 0.009 && $gst > 0.009) {
            $igst = max(0.0, round($gst - $cgst - $sgst, 2));
        }

        return [
            'sub_total' => round($sub, 2),
            'gst_amount' => round($gst, 2),
            'cgst_amount' => round($cgst, 2),
            'sgst_amount' => round($sgst, 2),
            'igst_amount' => round($igst, 2),
            'tds_amount' => round($tds, 2),
            'tds_label' => $tdsLabel,
            'tax_rate' => $taxRate,
            'gross_amount' => round($gross, 2),
            'net_payable' => round($amountPaid, 2),
            'other_deductions' => round($other, 2),
            'esi_amount' => round($esi, 2),
            'pf_amount' => round($pf, 2),
        ];
    }

    protected function package(
        string $scope,
        string $ownerName,
        int $vendorId,
        string $agreementNumber,
        ?int $agreementId,
        array $agreements,
        array $rows,
    ): array {
        $sections = LandlordAdvanceVendorDashboard::buildLedgerSections($rows);
        $rentExpense = $sections[LandlordAdvanceVendorDashboard::NATURE_RENT_EXPENSES]['summary'] ?? [];
        $rentAdvance = $sections[LandlordAdvanceVendorDashboard::NATURE_RENT_ADVANCE]['summary'] ?? [];
        $maintenance = $sections[LandlordAdvanceVendorDashboard::NATURE_MAINTENANCE]['summary'] ?? [];
        $rentSectionRows = collect($rows)->where('nature_key', LandlordAdvanceVendorDashboard::NATURE_RENT_EXPENSES);
        $rentBillRows = $rentSectionRows->where('line_type', 'bill');

        return [
            'scope' => $scope,
            'owner_name' => $ownerName,
            'vendor_id' => $vendorId,
            'agreement_number' => $agreementNumber,
            'agreement_id' => $agreementId,
            'agreements' => $agreements,
            'data_source' => 'bill_module',
            'sections' => $sections,
            'summary' => array_merge([
                'advance_balance' => (float) ($rentAdvance['pending_balance'] ?? 0),
                'rent_expense_pending' => (float) ($rentExpense['pending_balance'] ?? 0),
                'maintenance_pending' => (float) ($maintenance['pending_balance'] ?? 0),
                'total_amount_sent' => round((float) collect($rows)->sum('amount_sent'), 2),
                'total_pending' => round((float) collect($rows)->sum('pending_balance'), 2),
                'completed_payments' => (int) ($rentExpense['completed_count'] ?? 0),
                'pending_lines' => collect($rows)->filter(fn (array $row) => (float) ($row['pending_balance'] ?? 0) > 0.009)->count(),
                'agreement_count' => count($agreements),
                'rent_ledger' => [
                    'line_count' => (int) $rentSectionRows->count(),
                    'bill_count' => (int) $rentBillRows->count(),
                    'bill_gross_total' => round((float) $rentBillRows->sum(fn (array $row) => (float) ($row['pending_balance'] ?? 0) + (float) ($row['amount_sent'] ?? 0)), 2),
                    'bill_paid_total' => round((float) $rentSectionRows->sum('amount_sent'), 2),
                    'pending_total' => round((float) $rentSectionRows->sum('pending_balance'), 2),
                ],
            ], $vendorId > 0 ? ['vendor_bills' => $this->vendorBillSummary($vendorId)] : []),
            'rows' => $rows,
        ];
    }
}
