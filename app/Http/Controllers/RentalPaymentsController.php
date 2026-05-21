<?php

namespace App\Http\Controllers;

use App\Exports\RentalPaymentsExport;
use App\Http\Controllers\Controller;
use App\Services\LandlordBillPaymentsListingService;
use App\Models\RentalAgreement;
use App\Models\Tblcompany;
use App\Models\TblLocationModel;
use App\Models\Tblvendor;
use App\Models\TblZonesModel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\View\View;
use Maatwebsite\Excel\Excel as ExcelWriter;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class RentalPaymentsController extends Controller
{
    protected function userId(): int
    {
        $u = auth()->user();
        if (! $u) {
            abort(403);
        }

        return (int) $u->id;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    protected function paymentsView(string $path, array $data = []): View
    {
        $this->userId();

        return view($path, array_merge($data, [
            'admin' => auth()->user(),
        ]));
    }

    protected function segmentFrom(Request $request): string
    {
        $raw = $request->query('segment')
            ?? $request->query('agreementType')
            ?? $request->input('segment', '');
        $s = strtolower(trim((string) $raw));
        if (! in_array($s, RentalAgreement::TYPES, true)) {
            $s = RentalAgreement::TYPE_HOSPITAL;
        }

        return $s;
    }

    protected function resolveAgreementType(Request $request, ?RentalAgreement $agreement = null): string
    {
        if ($agreement) {
            return RentalAgreement::normalizeType($agreement->agreement_type);
        }

        return $this->segmentFrom($request);
    }

    /**
     * @return array<string, mixed>
     */
    protected function moduleContext(string $agreementType): array
    {
        return [
            'agreementType' => $agreementType,
            'typeLabel' => RentalAgreement::typeLabel($agreementType),
        ];
    }

    public function index(Request $request, LandlordBillPaymentsListingService $listing): View|Response
    {
        $this->userId();
        $agreementType = $this->resolveAgreementType($request);
        $paymentsBaseUrl = route('rental-payments', ['segment' => $agreementType]);

        $listingResult = $listing->build($request, $agreementType);
        $records = $listingResult['records'];
        $activeTab = $listingResult['activeTab'];

        $exportQs = array_merge(
            $request->except(['page']),
            ['segment' => $agreementType, 'tab' => $activeTab]
        );

        $viewData = array_merge(
            $this->moduleContext($agreementType),
            $this->paymentsFilterPresentation(
                $request,
                $agreementType,
                $activeTab,
                $records,
                $paymentsBaseUrl
            ),
            [
                'records' => $records,
                'activeTab' => $activeTab,
                'paymentsTabLabels' => $this->paymentsTabLabels(),
                'overviewStats' => $listingResult['overviewStats'],
                'dataSource' => $listingResult['dataSource'],
                'stateOptions' => $this->billStateFilterOptions(),
                'companies' => $this->reportCompanyOptions(),
                'zones' => $this->reportZoneOptions(),
                'branches' => $this->reportBranchOptions(),
                'vendors' => $this->reportVendorOptions(),
                'paymentsBaseUrl' => $paymentsBaseUrl,
                'exportExcelUrl' => route('rental-payments.export', array_merge($exportQs, ['format' => 'xlsx'])),
                'exportCsvUrl' => route('rental-payments.export', array_merge($exportQs, ['format' => 'csv'])),
                'chartReportUrl' => route('rental-payments.charts', $exportQs),
            ]
        );

        if ($request->ajax()) {
            return response(
                view('superadmin.landlord_payments.payments.stats', $viewData)->render()
                .view('superadmin.landlord_payments.payments.panel', $viewData)->render()
            );
        }

        return $this->paymentsView('superadmin.landlord_payments.payments.index', $viewData);
    }

    public function chart(Request $request, LandlordBillPaymentsListingService $listing): View
    {
        $this->userId();
        $agreementType = $this->resolveAgreementType($request);
        $paymentsBaseUrl = route('rental-payments', ['segment' => $agreementType]);

        $activeTab = $listing->tabFrom($request);
        $tabLabels = $this->paymentsTabLabels();
        $chartRows = $listing->collectAllRows($request, $agreementType);
        $listingResult = $listing->build($request, $agreementType);

        $exportQs = array_merge(
            $request->except(['page']),
            ['segment' => $agreementType, 'tab' => $activeTab]
        );

        return $this->paymentsView('superadmin.landlord_payments.payments.chart', array_merge(
            $this->moduleContext($agreementType),
            $this->paymentsFilterPresentation(
                $request,
                $agreementType,
                $activeTab,
                $listingResult['records'],
                $paymentsBaseUrl
            ),
            [
                'activeTab' => $activeTab,
                'paymentsTabLabels' => $tabLabels,
                'chartReportScope' => true,
                'chartScopeLabel' => 'All charge types',
                'overviewStats' => $listing->summarizeChartOverview($chartRows),
                'dataSource' => $listingResult['dataSource'],
                'chartData' => $listing->chartReportDataFromRows($chartRows, $activeTab, $tabLabels, true),
                'paymentsBaseUrl' => $paymentsBaseUrl,
                'exportExcelUrl' => route('rental-payments.export', array_merge($exportQs, ['format' => 'xlsx'])),
                'exportCsvUrl' => route('rental-payments.export', array_merge($exportQs, ['format' => 'csv'])),
            ]
        ));
    }

    public function export(Request $request, LandlordBillPaymentsListingService $listing): BinaryFileResponse
    {
        $this->userId();
        $agreementType = $this->resolveAgreementType($request);

        $format = strtolower(trim((string) $request->query('format', 'xlsx')));
        if (! in_array($format, ['csv', 'xlsx'], true)) {
            $format = 'xlsx';
        }

        $tabLabel = str_replace(' ', '_', $this->paymentsTabLabels()[$listing->tabFrom($request)] ?? 'payments');
        $fileName = 'Rental_Payments_'.$tabLabel.'_'.now()->format('Y_m_d_His').'.'.$format;

        $writerType = $format === 'csv' ? ExcelWriter::CSV : ExcelWriter::XLSX;

        return Excel::download(
            new RentalPaymentsExport($request, $agreementType),
            $fileName,
            $writerType,
            [
                'Content-Type' => $format === 'csv'
                    ? 'text/csv'
                    : 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ]
        );
    }

    /**
     * @return array<string, string>
     */
    private function paymentsTabLabels(): array
    {
        return [
            'all' => 'All',
            'rent_expense' => 'Rental expense',
            'rent_advance' => 'Rental advance',
            'maintenance' => 'Maintenance',
            'eb_bill' => 'Electricity charges',
        ];
    }

    /**
     * @return array<string, string>
     */
    private function reportChargeLabels(): array
    {
        return [
            'all' => 'All charge types',
            'rent_expense' => 'Rent expense',
            'rent_advance' => 'Rent advance',
            'maintenance' => 'Maintenance',
            'eb_bill' => 'EB charges',
        ];
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
            array_map(static fn ($name): string => trim((string) $name),
            (array) $raw),
            static fn (string $name): bool => $name !== ''
        )));
    }

    /**
     * @return \Illuminate\Support\Collection<int, string>
     */
    private function reportLandlordOptions(string $agreementType)
    {
        return RentalAgreement::query()
            ->where('agreement_type', $agreementType)
            ->whereNotNull('owner_name')
            ->where('owner_name', '!=', '')
            ->orderBy('owner_name')
            ->distinct()
            ->pluck('owner_name');
    }

    private function reportDateFromInput(Request $request): ?string
    {
        if ($request->filled('date_from')) {
            return $request->date('date_from')->toDateString();
        }
        if ($request->filled('from')) {
            return $request->date('from')->toDateString();
        }

        return null;
    }

    private function reportDateToInput(Request $request): ?string
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
     * @return array<string, mixed>
     */
    private function paymentsFilterPresentation(
        Request $request,
        string $agreementType,
        string $chargeFilter,
        LengthAwarePaginator $rows,
        ?string $filterBaseUrl = null
    ): array {
        $chargeLabels = $this->reportChargeLabels();
        $stateOptions = $this->billStateFilterOptions();
        $zones = $this->reportZoneOptions();
        $branches = $this->reportBranchOptions();
        $companies = $this->reportCompanyOptions();
        $vendors = $this->reportVendorOptions();

        $selCompanyIds = $this->filterIntIds($request, 'company_id');
        $selZoneIds = $this->filterIntIds($request, 'zone_id');
        $selBranchIds = $this->filterIntIds($request, 'branch_id');
        $selStateIds = array_values(array_intersect(
            $this->filterIntIds($request, 'state_id'),
            [1, 2, 3, 4, 5]
        ));
        $selVendorIds = $this->filterIntIds($request, 'vendor_id');

        $joinLabels = static function (array $ids, $collection, string $idKey, string $labelKey): string {
            if ($ids === []) {
                return '';
            }
            $labels = $collection->whereIn($idKey, $ids)->pluck($labelKey)->filter()->values();

            return $labels->isNotEmpty() ? $labels->implode(', ') : '';
        };

        $selectedCompanyName = $joinLabels($selCompanyIds, $companies, 'id', 'company_name');
        $selectedZoneName = $joinLabels($selZoneIds, $zones, 'id', 'name');
        $selectedBranchName = $joinLabels($selBranchIds, $branches, 'id', 'name');
        $selectedStateLabel = $selStateIds === []
            ? ''
            : collect($selStateIds)->map(fn (int $id) => $stateOptions[(string) $id] ?? '')->filter()->implode(', ');
        $vendorLabelById = $vendors->mapWithKeys(fn (Tblvendor $vendor): array => [
            (int) $vendor->id => $this->vendorDisplayLabel($vendor),
        ]);
        $selectedVendorLabel = $selVendorIds === []
            ? ''
            : collect($selVendorIds)
                ->map(fn (int $id): string => (string) ($vendorLabelById[$id] ?? ''))
                ->filter()
                ->implode(', ');

        $dateFrom = $this->reportDateFromInput($request);
        $dateTo = $this->reportDateToInput($request);
        $dateLabel = 'All dates';
        if ($dateFrom && $dateTo) {
            try {
                $dateLabel = Carbon::parse($dateFrom)->format('M j, Y').' – '.Carbon::parse($dateTo)->format('M j, Y');
            } catch (\Throwable $e) {
                $dateLabel = trim(($dateFrom ?: '…').' – '.($dateTo ?: '…'));
            }
        }

        $billingMonth = trim((string) $request->input('billing_month', ''));
        $billingMonthLabel = '';
        if ($billingMonth !== '') {
            try {
                $billingMonthLabel = Carbon::parse($billingMonth.'-01')->format('M Y');
            } catch (\Throwable $e) {
                $billingMonthLabel = $billingMonth;
            }
        }

        $chargeLabel = $chargeFilter !== 'all' ? ($chargeLabels[$chargeFilter] ?? $chargeFilter) : '';
        $activeTaxFilter = $this->activeTaxFilterFrom($request);
        $taxFilterLabel = match ($activeTaxFilter) {
            'gst' => 'With GST',
            'tds' => 'With TDS',
            default => '',
        };

        $rowFrom = $rows->firstItem();
        $rowTo = $rows->lastItem();
        $rowRangeLabel = ($rowFrom && $rowTo) ? ($rowFrom.'–'.$rowTo) : '0';

        $reportBaseUrl = $filterBaseUrl ?? route('rental-payments', ['segment' => $agreementType]);
        $chipUrl = function (array $without) use ($reportBaseUrl, $request, $agreementType): string {
            $strip = array_merge($without, ['page', 'segment']);

            return $reportBaseUrl.'?'.http_build_query(
                array_merge(['segment' => $agreementType], $request->except($strip))
            );
        };

        $vendorDisp = $selectedVendorLabel !== '' ? $selectedVendorLabel : 'All vendors';

        $hasFilterChips = ($dateFrom && $dateTo)
            || $selCompanyIds !== []
            || $selZoneIds !== []
            || $selBranchIds !== []
            || $selStateIds !== []
            || $selVendorIds !== []
            || $chargeLabel !== ''
            || $billingMonthLabel !== ''
            || $taxFilterLabel !== '';

        return [
            'reportFilterBaseUrl' => $reportBaseUrl,
            'clearReportFiltersUrl' => $reportBaseUrl.'?'.http_build_query([
                'segment' => $agreementType,
                'tab' => 'all',
            ]),
            'activeTaxFilter' => $activeTaxFilter,
            'taxFilterLabel' => $taxFilterLabel,
            'chipUrl' => $chipUrl,
            'hasFilterChips' => $hasFilterChips,
            'selCompanyIds' => $selCompanyIds,
            'selZoneIds' => $selZoneIds,
            'selBranchIds' => $selBranchIds,
            'selStateIds' => $selStateIds,
            'selVendorIds' => $selVendorIds,
            'companyDisp' => $selectedCompanyName !== '' ? $selectedCompanyName : 'All companies',
            'zoneDisp' => $selectedZoneName !== '' ? $selectedZoneName : 'All zones',
            'branchDisp' => $selectedBranchName !== '' ? $selectedBranchName : 'All branches',
            'stateDisp' => $selectedStateLabel !== '' ? $selectedStateLabel : 'All states',
            'vendorDisp' => $vendorDisp,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'dateLabel' => $dateLabel,
            'billingMonth' => $billingMonth,
            'billingMonthLabel' => $billingMonthLabel,
            'chargeLabelChip' => $chargeLabel,
            'chargeDisp' => $chargeLabels[$chargeFilter] ?? 'All charge types',
            'rowRangeLabel' => $rowRangeLabel,
        ];
    }

    /**
     * @return \Illuminate\Support\Collection<int, Tblcompany>
     */
    private function reportCompanyOptions()
    {
        return Tblcompany::query()
            ->orderBy('company_name')
            ->get(['id', 'company_name']);
    }

    /**
     * @return \Illuminate\Support\Collection<int, TblZonesModel>
     */
    private function reportZoneOptions()
    {
        return TblZonesModel::query()
            ->orderBy('name')
            ->get(['id', 'name']);
    }

    /**
     * @return \Illuminate\Support\Collection<int, TblLocationModel>
     */
    private function reportBranchOptions()
    {
        return TblLocationModel::query()
            ->active()
            ->orderBy('name')
            ->get(['id', 'name', 'zone_id']);
    }

    /**
     * @return \Illuminate\Support\Collection<int, Tblvendor>
     */
    private function reportVendorOptions()
    {
        return Tblvendor::query()
            ->activeLandlords()
            ->orderBy('display_name')
            ->orderBy('company_name')
            ->get(['id', 'display_name', 'company_name']);
    }

    private function vendorDisplayLabel(Tblvendor $vendor): string
    {
        $label = trim((string) ($vendor->display_name ?? ''));
        if ($label === '') {
            $label = trim((string) ($vendor->company_name ?? ''));
        }

        return $label;
    }

    /**
     * @return list<int>
     */
    private function filterIntIds(Request $request, string $key): array
    {
        $raw = $request->input($key, []);
        if (is_string($raw)) {
            $raw = $raw === '' ? [] : preg_split('/\s*,\s*/', $raw);
        }

        return array_values(array_unique(array_filter(
            array_map('intval', (array) $raw),
            static fn (int $id): bool => $id > 0
        )));
    }

    private function activeTaxFilterFrom(Request $request): ?string
    {
        $tax = strtolower(trim((string) $request->input('tax_filter', '')));

        return in_array($tax, ['gst', 'tds'], true) ? $tax : null;
    }

    /**
     * @return array<string, string>
     */
    private function billStateFilterOptions(): array
    {
        return [
            '1' => 'Tamil Nadu',
            '2' => 'Karnataka',
            '3' => 'Kerala',
            '4' => 'Andra Pradesh',
            '5' => 'International',
        ];
    }
}
