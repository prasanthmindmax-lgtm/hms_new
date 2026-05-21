<?php

namespace App\Http\Controllers;

use App\Models\RentalAgreement;
use App\Services\RentalAgreementOwnerPaymentHistory;
use App\Models\Tblgsttax;
use App\Models\Tbltdstax;
use App\Models\Tblcompany;
use App\Models\TblLocationModel;
use App\Models\Tblvendor;
use App\Models\TblZonesModel;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class RentalAgreementController extends Controller
{
    private function userRow(): object
    {
        $u = auth()->user();
        if (! $u) {
            abort(403);
        }

        return is_object($u) ? $u : (object) (array) $u;
    }

    private function nextAgreementNumber(): string
    {
        $year = date('Y');
        $prefix = 'RA-'.$year.'-';

        $last = RentalAgreement::query()
            ->where('agreement_number', 'like', $prefix.'%')
            ->orderByDesc('id')
            ->value('agreement_number');

        $next = 1;
        if ($last && preg_match('/^'.preg_quote($prefix, '/').'(\d+)$/', (string) $last, $matches)) {
            $next = (int) $matches[1] + 1;
        }

        return $prefix.str_pad((string) $next, 5, '0', STR_PAD_LEFT);
    }

    private function resolveAgreementType(Request $request, ?RentalAgreement $rentalAgreement = null): string
    {
        if ($rentalAgreement) {
            return RentalAgreement::normalizeType($rentalAgreement->agreement_type);
        }

        $segment = strtolower(trim((string) ($request->query('segment') ?? $request->input('segment', ''))));
        if (! in_array($segment, RentalAgreement::TYPES, true)) {
            $segment = RentalAgreement::TYPE_HOSPITAL;
        }

        return $segment;
    }

    /**
     * @return array<string, mixed>
     */
    private function moduleViewData(string $agreementType): array
    {
        $moduleTitle = RentalAgreement::typeLabel($agreementType);

        return [
            'agreementType' => $agreementType,
            'moduleTitle' => $moduleTitle,
            'moduleTitleLower' => Str::lower($moduleTitle),
            'moduleRegisterTitle' => $moduleTitle.' Register',
            'routeNames' => [
                'index' => 'rental-agreements.index',
                'create' => 'rental-agreements.create',
                'store' => 'rental-agreements.store',
                'show' => 'rental-agreements.show',
                'edit' => 'rental-agreements.edit',
                'update' => 'rental-agreements.update',
                'ownerPayments' => 'rental-agreements.owner-payments',
                'vendorOwnerPayments' => 'rental-agreements.vendor-owner-payments',
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function moduleViewDataForAll(): array
    {
        return [
            'agreementType' => 'all',
            'moduleTitle' => 'Rental agreements',
            'moduleTitleLower' => 'rental agreement',
            'moduleRegisterTitle' => 'Rental agreements register',
            'routeNames' => [
                'index' => 'rental-agreements.index',
                'create' => 'rental-agreements.create',
                'store' => 'rental-agreements.store',
                'show' => 'rental-agreements.show',
                'edit' => 'rental-agreements.edit',
                'update' => 'rental-agreements.update',
                'ownerPayments' => 'rental-agreements.owner-payments',
                'vendorOwnerPayments' => 'rental-agreements.vendor-owner-payments',
            ],
        ];
    }

    /**
     * Lowercase owner name → vendor_tbl.id for register owner drill-down links.
     *
     * @return array<string, int>
     */
    private function vendorIdLookupByOwnerName(): array
    {
        $lookup = [];
        foreach (Tblvendor::query()->activeLandlords()->get(['id', 'display_name', 'company_name']) as $vendor) {
            foreach ([$vendor->display_name, $vendor->company_name] as $name) {
                $key = mb_strtolower(trim((string) $name));
                if ($key !== '' && ! isset($lookup[$key])) {
                    $lookup[$key] = (int) $vendor->id;
                }
            }
        }

        return $lookup;
    }

    /**
     * Index: ?category=all|hospital|hostel (default all). Legacy ?segment= honoured when category is absent.
     *
     * @return array{0: ?string, 1: string}
     */
    private function indexTypeFilterAndCategoryQuery(Request $request): array
    {
        $category = strtolower(trim((string) ($request->query('category') ?? $request->input('category', ''))));
        if ($category === 'all' || $category === '') {
            return [null, 'all'];
        }
        if (in_array($category, RentalAgreement::TYPES, true)) {
            return [$category, $category];
        }

        $legacy = strtolower(trim((string) ($request->query('segment') ?? $request->input('segment', ''))));
        if (in_array($legacy, RentalAgreement::TYPES, true)) {
            return [$legacy, $legacy];
        }

        return [null, 'all'];
    }

    /**
     * Query string for rental-agreements.index after create/update.
     *
     * @return array<string, string>
     */
    private function indexRedirectQuery(Request $request, string $agreementType): array
    {
        $category = strtolower(trim((string) ($request->query('category') ?? $request->input('category', ''))));
        if ($category === 'all' || $category === '') {
            return ['category' => 'all'];
        }
        if (in_array($category, RentalAgreement::TYPES, true)) {
            return ['category' => $category];
        }
        if (in_array($agreementType, RentalAgreement::TYPES, true)) {
            return ['category' => $agreementType];
        }

        return ['category' => 'all'];
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

    /**
     * @return list<string>
     */
    private function filterStringValues(Request $request, string $key): array
    {
        $raw = $request->input($key, []);
        if (is_string($raw)) {
            $raw = $raw === '' ? [] : preg_split('/\s*,\s*/', $raw);
        }

        return array_values(array_unique(array_filter(
            array_map(static fn ($value) => trim((string) $value), (array) $raw),
            static fn (string $value): bool => $value !== ''
        )));
    }

    private function filteredQuery(Request $request, ?string $agreementType): Builder
    {
        $query = RentalAgreement::query();
        if ($agreementType !== null && in_array($agreementType, RentalAgreement::TYPES, true)) {
            $query->where('agreement_type', $agreementType);
        }

        $companyIds = $this->filterIntIds($request, 'company_id');
        if ($companyIds !== []) {
            $query->whereIn('company_id', $companyIds);
        }

        $zoneIds = $this->filterIntIds($request, 'zone_id');
        if ($zoneIds !== []) {
            $query->whereIn('zone_id', $zoneIds);
        }

        $branchIds = $this->filterIntIds($request, 'branch_id');
        if ($branchIds !== []) {
            $query->whereIn('branch_id', $branchIds);
        }

        $landlordVendorIds = $this->filterIntIds($request, 'vendor_id');
        if ($landlordVendorIds !== []) {
            $query->whereIn('rental_agreements.vendor_id', $landlordVendorIds);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('agreement_date', '>=', $request->date('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->whereDate('agreement_date', '<=', $request->date('date_to'));
        }

        $vendorGstPartyDbNames = $this->vendorGstPartyNamesForFilter($request);
        if ($vendorGstPartyDbNames !== []) {
            $query->where(function (Builder $gstQuery) use ($vendorGstPartyDbNames) {
                $gstQuery->whereIn('rental_agreements.vendor_id', function ($vendorSub) use ($vendorGstPartyDbNames) {
                    $vendorSub->select('vendor_tbl.id')
                        ->from('vendor_tbl')
                        ->where('vendor_tbl.active_status', 0)
                        ->where('vendor_tbl.party_type', Tblvendor::PARTY_LANDLORD)
                        ->whereIn('vendor_tbl.vendor_type_name', $vendorGstPartyDbNames);
                })->orWhere(function (Builder $fallback) use ($vendorGstPartyDbNames) {
                    $fallback->whereNull('rental_agreements.vendor_id');
                    $this->applyVendorOwnerExistsFilter($fallback, function ($sub) use ($vendorGstPartyDbNames) {
                        $sub->whereIn('vendor_tbl.vendor_type_name', $vendorGstPartyDbNames);
                    });
                });
            });
        }

        $stateIds = array_values(array_intersect(
            $this->filterIntIds($request, 'state_id'),
            [1, 2, 3, 4, 5]
        ));
        if ($stateIds !== []) {
            $this->applyBillStateGeoFilter($query, $stateIds);
        }

        $rcmValues = array_values(array_intersect($this->filterStringValues($request, 'rcm_applicable'), ['0', '1']));
        if ($rcmValues !== [] && count($rcmValues) < 2) {
            $query->where('rcm_applicable', $rcmValues[0] === '1');
        }

        $search = Str::limit(trim((string) $request->input('search', '')), 200, '');
        if ($search !== '') {
            $like = '%'.addcslashes($search, '%_\\').'%';
            $query->where(function (Builder $sub) use ($like) {
                $sub->where('agreement_number', 'like', $like)
                    ->orWhere('owner_name', 'like', $like)
                    ->orWhere('address', 'like', $like)
                    ->orWhere('agreement_period', 'like', $like)
                    ->orWhere('eb_number', 'like', $like)
                    ->orWhere('pan_number', 'like', $like)
                    ->orWhere('contact_person_name', 'like', $like)
                    ->orWhere('contact_person_number', 'like', $like)
                    ->orWhereHas('company', function (Builder $companyQuery) use ($like) {
                        $companyQuery->where('company_name', 'like', $like);
                    })
                    ->orWhereHas('zone', function (Builder $zoneQuery) use ($like) {
                        $zoneQuery->where('name', 'like', $like);
                    })
                    ->orWhereHas('branch', function (Builder $branchQuery) use ($like) {
                        $branchQuery->where('name', 'like', $like);
                    })
                    ->orWhere('additional_party_names', 'like', $like);
            });
        }

        return $query;
    }

    private function applyVendorOwnerExistsFilter(Builder $query, callable $vendorConstraint): void
    {
        $collation = 'utf8mb4_unicode_ci';

        $query->whereExists(function ($sub) use ($vendorConstraint, $collation) {
            $sub->selectRaw('1')
                ->from('vendor_tbl')
                ->where('vendor_tbl.active_status', 0)
                ->where('vendor_tbl.party_type', Tblvendor::PARTY_LANDLORD)
                ->where(function ($link) use ($collation) {
                    $link->whereColumn('vendor_tbl.id', 'rental_agreements.vendor_id')
                        ->orWhere(function ($name) use ($collation) {
                            $name->whereRaw(
                                "vendor_tbl.display_name COLLATE {$collation} = rental_agreements.owner_name COLLATE {$collation}"
                            )->orWhereRaw(
                                "vendor_tbl.company_name COLLATE {$collation} = rental_agreements.owner_name COLLATE {$collation}"
                            );
                        });
                });
            $vendorConstraint($sub);
        });
    }

    private function normalizeAdditionalPartyNames(?string $raw): ?string
    {
        if ($raw === null) {
            return null;
        }
        $raw = trim($raw);
        if ($raw === '') {
            return null;
        }
        $lines = preg_split('/\r\n|\r|\n/', $raw);
        if (! is_array($lines)) {
            return null;
        }
        $out = [];
        foreach ($lines as $line) {
            $t = trim((string) $line);
            if ($t !== '') {
                $out[] = $t;
            }
        }

        return $out === [] ? null : implode("\n", $out);
    }

    private function resolveVendorIdFromRequest(Request $request): int
    {
        $vendorId = (int) $request->input('vendor_id', 0);
        if ($vendorId <= 0) {
            return 0;
        }

        return Tblvendor::query()->activeLandlords()->whereKey($vendorId)->exists()
            ? $vendorId
            : 0;
    }

    private function resolveOwnerNameFromVendor(Request $request, string $ownerName): string
    {
        $vendorId = $this->resolveVendorIdFromRequest($request);
        if ($vendorId > 0) {
            $vendor = Tblvendor::query()->activeLandlords()->find($vendorId);
            if ($vendor) {
                $label = trim((string) ($vendor->display_name ?? ''));
                if ($label === '') {
                    $label = trim((string) ($vendor->company_name ?? ''));
                }
                if ($label !== '') {
                    return $label;
                }
            }
        }

        return trim($ownerName);
    }

    private function formatAgreementPeriod(string $startDate, string $endDate): string
    {
        return Carbon::parse($startDate)->format('d-m-Y').' to '.Carbon::parse($endDate)->format('d-m-Y');
    }

    /**
     * @return array<string, \Illuminate\Support\Collection<int, mixed>>
     */
    private function locationDropdownData(): array
    {
        return [
            'companies' => Tblcompany::query()->orderBy('company_name')->get(['id', 'company_name']),
            'zones' => TblZonesModel::query()->orderBy('name')->get(['id', 'name']),
            'branches' => TblLocationModel::query()->active()->orderBy('name')->get(['id', 'name', 'zone_id']),
        ];
    }

    /**
     * @return array<string, \Illuminate\Support\Collection<int, Tblvendor>>
     */
    private function vendorDropdownData(): array
    {
        return [
            'vendors' => Tblvendor::query()
                ->activeLandlords()
                ->orderBy('display_name')
                ->orderBy('company_name')
                ->get(['id', 'display_name', 'company_name', 'vendor_id', 'pan_number', 'vendor_type_name', 'party_type']),
        ];
    }

    /**
     * GST type filter options (Registered / Unregistered party) from vendor master.
     *
     * @return array<string, string>
     */
    /**
     * @return \Illuminate\Support\Collection<int, Tblvendor>
     */
    private function landlordFilterOptions()
    {
        return Tblvendor::query()
            ->activeLandlords()
            ->orderBy('display_name')
            ->orderBy('company_name')
            ->get(['id', 'display_name', 'company_name']);
    }

    private function landlordDisplayLabel(Tblvendor $vendor): string
    {
        $label = trim((string) ($vendor->display_name ?? ''));
        if ($label === '') {
            $label = trim((string) ($vendor->company_name ?? ''));
        }

        return $label;
    }

    /**
     * GST type filter options keyed by canonical vendor_type_name (Registered / Unregistered party).
     *
     * @return array<string, string>
     */
    private function vendorGstPartyFilterOptions(): array
    {
        $options = [];
        foreach (RentalAgreement::VENDOR_GST_PARTY_TYPES as $canonical) {
            $options[$canonical] = RentalAgreement::vendorGstPartyTypeLabel($canonical);
        }

        $fromMaster = Tblvendor::query()
            ->activeLandlords()
            ->whereNotNull('vendor_type_name')
            ->where('vendor_type_name', '!=', '')
            ->distinct()
            ->orderBy('vendor_type_name')
            ->pluck('vendor_type_name');

        foreach ($fromMaster as $dbName) {
            $canonical = RentalAgreement::canonicalVendorGstPartyType((string) $dbName);
            if ($canonical !== null) {
                $options[$canonical] = RentalAgreement::vendorGstPartyTypeLabel($canonical);
            }
        }

        return $options;
    }

    /**
     * @return list<string>
     */
    private function vendorGstPartyNamesForFilter(Request $request): array
    {
        $gstOptions = $this->vendorGstPartyFilterOptions();
        $selected = array_values(array_intersect(
            $this->filterStringValues($request, 'vendor_type_name'),
            array_keys($gstOptions)
        ));

        $dbNames = [];
        foreach ($selected as $filterKey) {
            $dbNames = array_merge($dbNames, RentalAgreement::vendorTypeNamesForGstFilter($filterKey));
        }

        return array_values(array_unique($dbNames));
    }

    /**
     * Fixed state list (same IDs/labels as bill dashboard).
     *
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

    /**
     * Map bill-module state IDs to rental agreement zone_id / branch_id (same as getbill state_name).
     *
     * @param  list<int>  $stateIds
     */
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

    /**
     * @return array<string, string>
     */
    private function rcmFilterOptions(): array
    {
        return [
            '1' => 'Yes',
            '0' => 'No',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function rules(Request $request, bool $isEdit = false): array
    {
        $rules = [
            'company_id' => 'required|integer|exists:company_tbl,id',
            'zone_id' => 'required|integer|exists:tblzones,id',
            'branch_id' => [
                'required',
                'integer',
                Rule::exists('tbl_locations', 'id')->where(function ($query) use ($request) {
                    $query->where('zone_id', (int) $request->input('zone_id'));
                }),
            ],
            'agreement_date' => 'required|date',
            'owner_name' => 'required|string|max:255',
            'additional_party_names' => 'nullable|string|max:5000',
            'address' => 'required|string|max:5000',
            'agreement_period' => 'required|string|max:50',
            'agreement_period_start' => 'required|date',
            'agreement_period_end' => 'required|date|after_or_equal:agreement_period_start',
            'advance_amount' => 'required|numeric|min:0',
            'monthly_rent_amount' => 'required|numeric|min:0',
            'gst_applicable' => 'required|in:0,1',
            'gst_type' => 'nullable|required_if:gst_applicable,1|in:'.RentalAgreement::GST_INCLUDING.','.RentalAgreement::GST_EXCLUDING,
            'gst_tax_id' => 'nullable|required_if:gst_applicable,1|integer',
            'gst_tax_name' => 'nullable|required_if:gst_applicable,1|string|max:120',
            'gst_tax_type' => 'nullable|required_if:gst_applicable,1|string|in:GST,IGST',
            'gst_percentage' => 'nullable|required_if:gst_applicable,1|numeric|min:0|max:100',
            'gst_amount' => 'nullable|required_if:gst_applicable,1|numeric|min:0',
            'cgst_amount' => 'nullable|numeric|min:0',
            'sgst_amount' => 'nullable|numeric|min:0',
            'igst_amount' => 'nullable|numeric|min:0',
            'tds_tax_id' => 'required|integer',
            'tds_tax_name' => 'required|string|max:120',
            'tds_rate' => 'required|numeric|min:0|max:100',
            'tds_section_id' => 'nullable|integer',
            'tds_section' => 'nullable|string|max:40',
            'tds_amount' => 'nullable|numeric|min:0',
            'rcm_applicable' => 'required|in:0,1',
            'rcm_value' => 'nullable|required_if:rcm_applicable,1|numeric|min:0',
            'maintenance_amount' => 'nullable|numeric|min:0',
            'eb_number' => 'nullable|string|max:120',
            'sq_ft_area' => 'nullable|numeric|min:0',
            'rent_revision' => 'nullable|string|max:120',
            'rent_hike_percentage' => 'nullable|numeric|min:0|max:100',
            'end_of_agreement_date' => 'required|date|after_or_equal:agreement_date',
            'termination_period' => 'nullable|string|max:120',
            'date_of_rent_payment' => 'nullable|string|max:120',
            'pan_number' => 'nullable|string|max:30',
            'contact_person_name' => 'nullable|string|max:255',
            'contact_person_number' => 'nullable|string|max:30',
            'vendor_id' => [
                'required',
                'integer',
                'min:1',
                Rule::exists('vendor_tbl', 'id')->where(function ($q) {
                    $q->where('active_status', 0)
                        ->where('party_type', Tblvendor::PARTY_LANDLORD);
                }),
            ],
            'attachment' => ($isEdit ? 'nullable' : 'nullable').'|file|max:10240|mimes:pdf,jpg,jpeg,png,doc,docx,xls,xlsx',
            'building_photo' => 'nullable|file|max:5120|mimes:jpg,jpeg,png,webp',
        ];

        if (! $isEdit) {
            $rules['agreement_type'] = 'required|in:'.implode(',', RentalAgreement::TYPES);
        }

        return $rules;
    }

    /**
     * @return array{rcm_applicable: bool, rcm_value: ?float}
     */
    private function rcmPayloadFromValidated(array $validated): array
    {
        $applicable = (string) ($validated['rcm_applicable'] ?? '0') === '1';

        return [
            'rcm_applicable' => $applicable,
            'rcm_value' => $applicable ? round((float) ($validated['rcm_value'] ?? 0), 2) : null,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    /**
     * @param  array<string, mixed>  $validated
     * @return array<string, mixed>
     */
    private function normalizeGstValidated(array $validated): array
    {
        if ((string) ($validated['gst_applicable'] ?? '0') !== '1') {
            $validated['gst_type'] = RentalAgreement::GST_NONE;

            return $validated;
        }

        $type = (string) ($validated['gst_type'] ?? '');
        if (! in_array($type, [RentalAgreement::GST_INCLUDING, RentalAgreement::GST_EXCLUDING], true)) {
            $validated['gst_type'] = RentalAgreement::GST_EXCLUDING;
        }

        return $validated;
    }

    /**
     * @return array<string, mixed>
     */
    private function gstPayloadFromValidated(array $validated): array
    {
        $gstType = (string) ($validated['gst_type'] ?? '');

        if (! RentalAgreement::isGstApplicableType($gstType)) {
            return [
                'gst_percentage' => null,
                'gst_amount' => null,
                'gst_tax_id' => null,
                'gst_tax_name' => null,
                'gst_tax_type' => null,
                'cgst_amount' => null,
                'sgst_amount' => null,
                'igst_amount' => null,
            ];
        }

        $gstTaxType = strtoupper(trim((string) ($validated['gst_tax_type'] ?? 'GST'))) ?: 'GST';
        $breakdown = RentalAgreement::computeGstBreakdown(
            $gstType,
            (float) ($validated['monthly_rent_amount'] ?? 0),
            (float) ($validated['maintenance_amount'] ?? 0),
            (float) ($validated['gst_percentage'] ?? 0),
            $gstTaxType
        );

        return [
            'gst_percentage' => round((float) ($validated['gst_percentage'] ?? 0), 2),
            'gst_amount' => $breakdown['gst_amount'],
            'gst_tax_id' => isset($validated['gst_tax_id']) ? (int) $validated['gst_tax_id'] : null,
            'gst_tax_name' => trim((string) ($validated['gst_tax_name'] ?? '')) ?: null,
            'gst_tax_type' => $gstTaxType,
            'cgst_amount' => $breakdown['cgst_amount'],
            'sgst_amount' => $breakdown['sgst_amount'],
            'igst_amount' => $breakdown['igst_amount'],
        ];
    }

    /**
     * @return \Illuminate\Support\Collection<int, Tblgsttax>
     */
    private function activeGstTaxes()
    {
        return Tblgsttax::query()->orderBy('tax_name')->get();
    }

    /**
     * @return \Illuminate\Support\Collection<int, Tbltdstax>
     */
    private function activeTdsTaxes()
    {
        return Tbltdstax::query()
            ->with('section:id,name')
            ->orderBy('tax_name')
            ->get();
    }

    /**
     * @return array<string, mixed>
     */
    private function tdsPayloadFromValidated(array $validated): array
    {
        $rate = (float) ($validated['tds_rate'] ?? 0);
        if ($rate > 0 && $rate <= 1) {
            $rate = $rate * 100;
        }

        return [
            'tds_tax_id' => isset($validated['tds_tax_id']) ? (int) $validated['tds_tax_id'] : null,
            'tds_tax_name' => trim((string) ($validated['tds_tax_name'] ?? '')) ?: null,
            'tds_rate' => round($rate, 4),
            'tds_section_id' => isset($validated['tds_section_id']) && $validated['tds_section_id'] !== ''
                ? (int) $validated['tds_section_id']
                : null,
            'tds_section' => trim((string) ($validated['tds_section'] ?? '')) ?: null,
            'tds_amount' => round((float) ($validated['tds_amount'] ?? 0), 2),
        ];
    }

    private function messages(): array
    {
        return [
            'company_id.required' => 'Company is required.',
            'company_id.exists' => 'Selected company is not valid.',
            'zone_id.required' => 'Zone is required.',
            'zone_id.exists' => 'Selected zone is not valid.',
            'branch_id.required' => 'Branch is required.',
            'branch_id.exists' => 'Branch must belong to the selected zone.',
            'agreement_date.required' => 'Rental agreement date is required.',
            'owner_name.required' => 'Please select a landlord from the list.',
            'vendor_id.required' => 'Please select a landlord from the list.',
            'vendor_id.exists' => 'Selected landlord is invalid or inactive.',
            'address.required' => 'Address is required.',
            'agreement_period.required' => 'Agreement period is required.',
            'agreement_period_start.required' => 'Select the agreement period start date.',
            'agreement_period_end.required' => 'Select the agreement period end date.',
            'agreement_period_end.after_or_equal' => 'Agreement period end date must be the same as or after the start date.',
            'advance_amount.required' => 'Advance amount is required.',
            'monthly_rent_amount.required' => 'Monthly rent amount is required.',
            'gst_applicable.required' => 'Select whether GST is applicable (Yes or No).',
            'gst_type.required_if' => 'Select tax mode (Including or Excluding GST).',
            'gst_tax_id.required_if' => 'Select a GST rate from the list.',
            'gst_tax_name.required_if' => 'Select a GST rate from the list.',
            'gst_percentage.required_if' => 'Select a GST rate from the list.',
            'gst_amount.required_if' => 'GST amount is required when GST is applicable.',
            'tds_tax_id.required' => 'Select a TDS tax from the list.',
            'tds_tax_name.required' => 'Select a TDS tax from the list.',
            'tds_rate.required' => 'Select a TDS tax from the list.',
            'rcm_applicable.required' => 'Select whether RCM is applicable (Yes or No).',
            'rcm_applicable.in' => 'RCM must be Yes or No.',
            'rcm_value.required_if' => 'Enter the RCM value when RCM is Yes.',
            'end_of_agreement_date.required' => 'End of agreement date is required.',
            'end_of_agreement_date.after_or_equal' => 'End of agreement date must be the same as or after the agreement date.',
            'rent_hike_percentage.max' => 'Rent hike percentage may not be greater than 100.',
            'attachment.max' => 'Attachment must not be larger than 10 MB.',
            'building_photo.max' => 'Building photo must not be larger than 5 MB.',
            'building_photo.mimes' => 'Building photo must be a JPG, PNG, or WebP image.',
            'agreement_type.required' => 'Select a category (Hospital or Hostel).',
            'agreement_type.in' => 'Selected category is not valid.',
        ];
    }

    private function saveUploaded(UploadedFile $file): array
    {
        $uploadPath = public_path('rental_agreement_attachments');
        if (! File::isDirectory($uploadPath)) {
            File::makeDirectory($uploadPath, 0755, true);
        }

        $originalName = $file->getClientOriginalName();
        $safeName = time().'_'.mt_rand(1000, 9999).'_'.preg_replace('/[^a-zA-Z0-9._-]/', '_', $originalName);
        $safeName = basename(str_replace(["\0", '/', '\\'], '', $safeName));
        $file->move($uploadPath, $safeName);

        return [
            'path' => 'rental_agreement_attachments/'.$safeName,
            'name' => $originalName,
        ];
    }

    private function deleteAttachmentFile(?string $storedPath): void
    {
        if ($storedPath === null || $storedPath === '' || str_starts_with($storedPath, 'uploads/')) {
            return;
        }

        $name = basename(str_replace('\\', '/', $storedPath));
        if ($name === '' || $name === '.' || $name === '..') {
            return;
        }

        $absolutePath = public_path('rental_agreement_attachments/'.$name);
        if (! File::exists($absolutePath)) {
            return;
        }

        try {
            File::delete($absolutePath);
        } catch (\Throwable $e) {
            // Best-effort cleanup only.
        }
    }

    /**
     * @return array<string, mixed>
     */
    private function indexViewData(Request $request): array
    {
        $this->userRow();
        [$typeFilter, $indexCategoryQuery] = $this->indexTypeFilterAndCategoryQuery($request);

        $perPageChoices = [10, 15, 25, 50, 100];
        $perPage = (int) $request->input('per_page', 10);
        if (! in_array($perPage, $perPageChoices, true)) {
            $perPage = 10;
        }

        $base = $this->filteredQuery($request, $typeFilter);

        $stats = [
            'total' => (clone $base)->count(),
            'advance_total' => (float) (clone $base)->sum('advance_amount'),
            'monthly_rent_total' => (float) (clone $base)->sum('monthly_rent_amount'),
            'ending_soon' => (clone $base)->activeWithinDays(30)->count(),
        ];

        $records = (clone $base)
            ->with([
                'creator:id,user_fullname',
                'company:id,company_name',
                'zone:id,name',
                'branch:id,name,zone_id',
            ])
            ->orderByDesc('agreement_date')
            ->orderByDesc('id')
            ->paginate($perPage)
            ->withQueryString();

        $location = $this->locationDropdownData();
        $module = $typeFilter === null
            ? $this->moduleViewDataForAll()
            : $this->moduleViewData($typeFilter);

        return array_merge([
            'admin' => auth()->user(),
            'records' => $records,
            'stats' => $stats,
            'chartData' => $this->registerChartData($base),
            'perPageChoices' => $perPageChoices,
            'perPage' => $perPage,
            'gstOptions' => $this->vendorGstPartyFilterOptions(),
            'stateOptions' => $this->billStateFilterOptions(),
            'rcmOptions' => $this->rcmFilterOptions(),
            'indexCategoryQuery' => $indexCategoryQuery,
            'vendorIdByOwnerName' => $this->vendorIdLookupByOwnerName(),
        ], $location, $module, $this->registerListingPresentation(
            $request,
            $typeFilter,
            $indexCategoryQuery,
            $records,
            $location['companies'],
            $location['zones'],
            $location['branches'],
            $this->vendorGstPartyFilterOptions(),
            $this->billStateFilterOptions(),
            $this->rcmFilterOptions(),
            $this->landlordFilterOptions(),
        ));
    }

    /**
     * @return array<string, mixed>
     */
    private function registerChartData(Builder $base): array
    {
        $rows = (clone $base)
            ->selectRaw('agreement_type, SUM(monthly_rent_amount) as rent_sum, COUNT(*) as agreement_count')
            ->groupBy('agreement_type')
            ->get();

        $rentByType = [
            RentalAgreement::TYPE_HOSPITAL => 0.0,
            RentalAgreement::TYPE_HOSTEL => 0.0,
        ];
        $countByType = [
            RentalAgreement::TYPE_HOSPITAL => 0,
            RentalAgreement::TYPE_HOSTEL => 0,
        ];

        foreach ($rows as $row) {
            $type = RentalAgreement::normalizeType((string) $row->agreement_type);
            $rentByType[$type] = (float) $row->rent_sum;
            $countByType[$type] = (int) $row->agreement_count;
        }

        $topOwners = (clone $base)
            ->selectRaw('owner_name, SUM(monthly_rent_amount) as rent_sum')
            ->groupBy('owner_name')
            ->orderByDesc('rent_sum')
            ->limit(8)
            ->get()
            ->map(fn ($row) => [
                'owner' => (string) $row->owner_name,
                'rent' => (float) $row->rent_sum,
            ])
            ->values()
            ->all();

        return [
            'rentByType' => $rentByType,
            'countByType' => $countByType,
            'typeLabels' => [
                RentalAgreement::TYPE_HOSPITAL => 'Hospital',
                RentalAgreement::TYPE_HOSTEL => 'Hostel',
            ],
            'topOwners' => $topOwners,
        ];
    }

    /**
     * Register grid / filter bar presentation (must live in controller so sibling @includes share scope).
     *
     * @param  \Illuminate\Support\Collection<int, mixed>  $companies
     * @param  \Illuminate\Support\Collection<int, mixed>  $zones
     * @param  \Illuminate\Support\Collection<int, mixed>  $branches
     * @return array<string, mixed>
     */
    private function registerListingPresentation(
        Request $request,
        ?string $typeFilter,
        string $indexCategoryQuery,
        LengthAwarePaginator $records,
        $companies,
        $zones,
        $branches,
        array $gstOptions,
        array $stateOptions,
        array $rcmOptions,
        $landlords,
    ): array {
        $formatAgreementPeriod = static function ($value): string {
            $raw = trim((string) $value);
            if ($raw === '') {
                return '—';
            }
            if (stripos($raw, ' to ') !== false) {
                return $raw;
            }

            try {
                return Carbon::parse($raw)->format('d M Y');
            } catch (\Throwable $e) {
                return $raw;
            }
        };

        $selCompanyIds = $this->filterIntIds($request, 'company_id');
        $selZoneIds = $this->filterIntIds($request, 'zone_id');
        $selBranchIds = $this->filterIntIds($request, 'branch_id');
        $selGstTypes = array_values(array_intersect(
            $this->filterStringValues($request, 'vendor_type_name'),
            array_keys($gstOptions)
        ));
        $selStateIds = array_values(array_intersect(
            $this->filterIntIds($request, 'state_id'),
            array_map('intval', array_keys($stateOptions))
        ));
        $selRcms = array_values(array_intersect(
            $this->filterStringValues($request, 'rcm_applicable'),
            array_keys($rcmOptions)
        ));
        $selVendorIds = $this->filterIntIds($request, 'vendor_id');

        $joinLabels = static function (array $ids, $collection, string $idKey, string $labelKey, string $emptyLabel): string {
            if ($ids === []) {
                return $emptyLabel;
            }
            $labels = $collection->whereIn($idKey, $ids)->pluck($labelKey)->filter()->values();

            return $labels->isNotEmpty() ? $labels->implode(', ') : $emptyLabel;
        };

        $selectedCompanyName = $joinLabels($selCompanyIds, $companies, 'id', 'company_name', '');
        $selectedZoneName = $joinLabels($selZoneIds, $zones, 'id', 'name', '');
        $selectedBranchName = $joinLabels($selBranchIds, $branches, 'id', 'name', '');
        $selectedGstLabel = $selGstTypes === []
            ? ''
            : collect($selGstTypes)->map(fn (string $key) => $gstOptions[$key] ?? $key)->implode(', ');
        $selectedStateLabel = $selStateIds === []
            ? ''
            : collect($selStateIds)->map(fn (int $id) => $stateOptions[(string) $id] ?? '')->filter()->implode(', ');
        $selectedRcmLabel = $selRcms === []
            ? ''
            : collect($selRcms)->map(fn (string $key) => $rcmOptions[$key] ?? $key)->implode(', ');
        $selectedLandlordLabel = $selVendorIds === []
            ? ''
            : $landlords->whereIn('id', $selVendorIds)
                ->map(fn (Tblvendor $vendor) => $this->landlordDisplayLabel($vendor))
                ->filter()
                ->values()
                ->implode(', ');

        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');
        $dateLabel = 'All dates';
        if ($dateFrom && $dateTo) {
            try {
                $dateLabel = Carbon::parse($dateFrom)->format('M j, Y').' – '.Carbon::parse($dateTo)->format('M j, Y');
            } catch (\Throwable $e) {
                $dateLabel = trim(($dateFrom ?: '…').' – '.($dateTo ?: '…'));
            }
        } elseif ($dateFrom || $dateTo) {
            $dateLabel = trim(($dateFrom ?: '…').' – '.($dateTo ?: '…'));
        }

        $rowFrom = $records->firstItem();
        $rowTo = $records->lastItem();
        $rowRangeLabel = ($rowFrom && $rowTo) ? ($rowFrom.'–'.$rowTo) : '0';
        $searchTrim = trim((string) $request->input('search', ''));
        $hasFilterChips = ($dateFrom && $dateTo)
            || $selCompanyIds !== []
            || $selZoneIds !== []
            || $selBranchIds !== []
            || $selGstTypes !== []
            || $selStateIds !== []
            || $selRcms !== []
            || $selVendorIds !== []
            || $searchTrim !== ''
            || $indexCategoryQuery !== 'all';

        $raListQs = ['category' => $indexCategoryQuery];
        $raListingBaseUrl = route('rental-agreements.index');

        $raCreateUrl = route('rental-agreements.create');
        if ($indexCategoryQuery !== 'all') {
            $raCreateUrl .= '?'.http_build_query(['category' => $indexCategoryQuery]);
        }

        $chipUrl = function (array $without) use ($raListingBaseUrl, $raListQs, $request): string {
            $strip = array_merge($without, ['page', 'segment', 'category']);

            return $raListingBaseUrl.'?'.http_build_query(
                array_merge($raListQs, $request->except($strip))
            );
        };
        $clearRegisterFiltersUrl = route('rental-agreements.index');

        $categoryChipLabel = '';
        if ($indexCategoryQuery !== 'all') {
            $categoryChipLabel = match ($indexCategoryQuery) {
                RentalAgreement::TYPE_HOSPITAL => 'Category: Hospital',
                RentalAgreement::TYPE_HOSTEL => 'Category: Hostel',
                default => '',
            };
        }

        return [
            'formatAgreementPeriod' => $formatAgreementPeriod,
            'selCompanyIds' => $selCompanyIds,
            'selZoneIds' => $selZoneIds,
            'selBranchIds' => $selBranchIds,
            'selGstTypes' => $selGstTypes,
            'selStateIds' => $selStateIds,
            'selRcms' => $selRcms,
            'selVendorIds' => $selVendorIds,
            'landlords' => $landlords,
            'companyDisp' => $selectedCompanyName !== '' ? $selectedCompanyName : 'All companies',
            'zoneDisp' => $selectedZoneName !== '' ? $selectedZoneName : 'All zones',
            'branchDisp' => $selectedBranchName !== '' ? $selectedBranchName : 'All branches',
            'gstDisp' => $selectedGstLabel !== '' ? $selectedGstLabel : 'All GST types',
            'stateDisp' => $selectedStateLabel !== '' ? $selectedStateLabel : 'All states',
            'rcmDisp' => $selectedRcmLabel !== '' ? $selectedRcmLabel : 'All RCM',
            'landlordDisp' => $selectedLandlordLabel !== '' ? $selectedLandlordLabel : 'All landlords',
            'selectedCompanyName' => $selectedCompanyName,
            'selectedZoneName' => $selectedZoneName,
            'selectedBranchName' => $selectedBranchName,
            'selectedGstLabel' => $selectedGstLabel,
            'selectedStateLabel' => $selectedStateLabel,
            'selectedRcmLabel' => $selectedRcmLabel,
            'selectedLandlordLabel' => $selectedLandlordLabel,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'dateLabel' => $dateLabel,
            'rowRangeLabel' => $rowRangeLabel,
            'searchTrim' => $searchTrim,
            'hasFilterChips' => $hasFilterChips,
            'raListingBaseUrl' => $raListingBaseUrl,
            'raCreateUrl' => $raCreateUrl,
            'chipUrl' => $chipUrl,
            'clearRegisterFiltersUrl' => $clearRegisterFiltersUrl,
            'categoryChipLabel' => $categoryChipLabel,
        ];
    }

    public function index(Request $request): View
    {
        return view('superadmin.landlord_payments.rental_agreements.index', $this->indexViewData($request));
    }

    public function ownerPayments(Request $request, RentalAgreement $rentalAgreement): View|JsonResponse
    {
        $this->userRow();
        $agreementType = $this->resolveAgreementType($request, $rentalAgreement);
        $rentalAgreement->load([
            'company:id,company_name',
            'zone:id,name',
            'branch:id,name,zone_id',
        ]);

        $history = app(RentalAgreementOwnerPaymentHistory::class)->build($rentalAgreement);

        if ($request->wantsJson()) {
            return response()->json($history);
        }

        return view('superadmin.landlord_payments.rental_agreements.owner_payments', $this->ownerPaymentsViewData(
            $request,
            $history,
            $agreementType,
            $rentalAgreement,
            null,
        ));
    }

    public function vendorOwnerPayments(Request $request, Tblvendor $vendor): View|JsonResponse
    {
        $this->userRow();

        $category = strtolower(trim((string) $request->query('category', '')));
        if ($category === 'all' || $category === '') {
            $category = null;
        } elseif (! in_array($category, RentalAgreement::TYPES, true)) {
            $category = null;
        }

        $history = app(RentalAgreementOwnerPaymentHistory::class)->buildForVendor($vendor, $category);

        if ($request->wantsJson()) {
            return response()->json($history);
        }

        $agreementType = $category ?? RentalAgreement::TYPE_HOSPITAL;

        return view('superadmin.landlord_payments.rental_agreements.owner_payments', $this->ownerPaymentsViewData(
            $request,
            $history,
            $agreementType,
            null,
            $vendor,
        ));
    }

    /**
     * @param  array<string, mixed>  $history
     * @return array<string, mixed>
     */
    private function ownerPaymentsViewData(
        Request $request,
        array $history,
        string $agreementType,
        ?RentalAgreement $rentalAgreement,
        ?Tblvendor $vendor,
    ): array {
        $indexBackQs = array_filter([
            'category' => $request->query('category'),
        ]);
        $backUrl = route($this->moduleViewData($agreementType)['routeNames']['index'], $indexBackQs);

        $module = $this->moduleViewData($agreementType);

        return array_merge([
            'admin' => auth()->user(),
            'record' => $rentalAgreement,
            'vendor' => $vendor,
            'history' => $history,
            'paymentsScope' => $history['scope'] ?? 'agreement',
            'summary' => $history['summary'],
            'sections' => $history['sections'] ?? [],
            'rows' => $history['rows'],
            'agreementsList' => $history['agreements'] ?? [],
            'indexBackQs' => $indexBackQs,
            'backUrl' => $backUrl,
        ], $module);
    }

    public function create(Request $request): View
    {
        $this->userRow();
        $pre = strtolower(trim((string) ($request->query('category') ?? $request->query('segment') ?? '')));
        if (! in_array($pre, RentalAgreement::TYPES, true)) {
            $pre = RentalAgreement::TYPE_HOSPITAL;
        }

        return view('superadmin.landlord_payments.rental_agreements.create', array_merge([
            'admin' => auth()->user(),
            'record' => null,
            'isEdit' => false,
            'gstOptions' => RentalAgreement::GST_TAX_MODE_LABELS,
            'gstTaxes' => $this->activeGstTaxes(),
            'tdsTaxes' => $this->activeTdsTaxes(),
            'defaultAgreementType' => $pre,
        ], $this->locationDropdownData(), $this->vendorDropdownData(), $this->moduleViewDataForAll()));
    }

    public function store(Request $request): RedirectResponse
    {
        $this->userRow();
        $validated = $this->normalizeGstValidated($request->validate($this->rules($request), $this->messages()));
        $agreementType = RentalAgreement::normalizeType((string) $validated['agreement_type']);
        $vendorId = $this->resolveVendorIdFromRequest($request);
        if ($vendorId <= 0) {
            return back()->withInput()->withErrors(['vendor_id' => 'Please select a landlord from the list.']);
        }

        $ownerName = $this->resolveOwnerNameFromVendor($request, (string) $validated['owner_name']);
        if ($ownerName === '') {
            return back()->withInput()->withErrors(['owner_name' => 'Please select a landlord from the list.']);
        }

        $payload = [
            'agreement_type' => $agreementType,
            'agreement_number' => $this->nextAgreementNumber(),
            'company_id' => $validated['company_id'],
            'zone_id' => $validated['zone_id'],
            'branch_id' => $validated['branch_id'],
            'agreement_date' => $validated['agreement_date'],
            'vendor_id' => $vendorId,
            'owner_name' => $ownerName,
            'additional_party_names' => $this->normalizeAdditionalPartyNames($request->input('additional_party_names')),
            'address' => $validated['address'],
            'agreement_period' => $this->formatAgreementPeriod($validated['agreement_period_start'], $validated['agreement_period_end']),
            'advance_amount' => $validated['advance_amount'],
            'monthly_rent_amount' => $validated['monthly_rent_amount'],
            'gst_type' => $validated['gst_type'],
            ...$this->gstPayloadFromValidated($validated),
            ...$this->tdsPayloadFromValidated($validated),
            ...$this->rcmPayloadFromValidated($validated),
            'maintenance_amount' => $validated['maintenance_amount'] ?? null,
            'eb_number' => $validated['eb_number'] ?? null,
            'sq_ft_area' => $validated['sq_ft_area'] ?? null,
            'rent_revision' => $validated['rent_revision'] ?? null,
            'rent_hike_percentage' => $validated['rent_hike_percentage'] ?? null,
            'end_of_agreement_date' => $validated['end_of_agreement_date'],
            'termination_period' => $validated['termination_period'] ?? null,
            'date_of_rent_payment' => $validated['date_of_rent_payment'] ?? null,
            'pan_number' => $validated['pan_number'] ?? null,
            'contact_person_name' => $validated['contact_person_name'] ?? null,
            'contact_person_number' => $validated['contact_person_number'] ?? null,
            'created_by' => (int) auth()->id(),
        ];

        if ($request->hasFile('attachment')) {
            $upload = $this->saveUploaded($request->file('attachment'));
            $payload['attachment_path'] = $upload['path'];
            $payload['attachment_original_name'] = $upload['name'];
        }

        if ($request->hasFile('building_photo')) {
            $upload = $this->saveUploaded($request->file('building_photo'));
            $payload['building_photo_path'] = $upload['path'];
            $payload['building_photo_original_name'] = $upload['name'];
        }

        RentalAgreement::query()->create($payload);

        return redirect()
            ->route('rental-agreements.index', $this->indexRedirectQuery($request, $agreementType))
            ->with('success', RentalAgreement::typeLabel($agreementType).' saved.');
    }

    public function show(Request $request, RentalAgreement $rentalAgreement): View
    {
        $this->userRow();
        $agreementType = $this->resolveAgreementType($request, $rentalAgreement);
        $rentalAgreement->load([
            'creator:id,user_fullname',
            'company:id,company_name',
            'zone:id,name',
            'branch:id,name,zone_id',
        ]);

        return view('superadmin.landlord_payments.rental_agreements.show', array_merge([
            'admin' => auth()->user(),
            'record' => $rentalAgreement,
        ], $this->moduleViewData($agreementType)));
    }

    public function edit(Request $request, RentalAgreement $rentalAgreement): View
    {
        $this->userRow();
        $agreementType = $this->resolveAgreementType($request, $rentalAgreement);

        return view('superadmin.landlord_payments.rental_agreements.create', array_merge([
            'admin' => auth()->user(),
            'record' => $rentalAgreement,
            'isEdit' => true,
            'gstOptions' => RentalAgreement::GST_TAX_MODE_LABELS,
            'gstTaxes' => $this->activeGstTaxes(),
            'tdsTaxes' => $this->activeTdsTaxes(),
        ], $this->locationDropdownData(), $this->vendorDropdownData(), $this->moduleViewData($agreementType)));
    }

    public function update(Request $request, RentalAgreement $rentalAgreement): RedirectResponse
    {
        $this->userRow();
        $agreementType = $this->resolveAgreementType($request, $rentalAgreement);
        $validated = $this->normalizeGstValidated($request->validate($this->rules($request, true), $this->messages()));
        $vendorId = $this->resolveVendorIdFromRequest($request);
        if ($vendorId <= 0) {
            return back()->withInput()->withErrors(['vendor_id' => 'Please select a landlord from the list.']);
        }

        $ownerName = $this->resolveOwnerNameFromVendor($request, (string) $validated['owner_name']);
        if ($ownerName === '') {
            return back()->withInput()->withErrors(['owner_name' => 'Please select a landlord from the list.']);
        }

        $payload = [
            'company_id' => $validated['company_id'],
            'zone_id' => $validated['zone_id'],
            'branch_id' => $validated['branch_id'],
            'agreement_date' => $validated['agreement_date'],
            'vendor_id' => $vendorId,
            'owner_name' => $ownerName,
            'additional_party_names' => $this->normalizeAdditionalPartyNames($request->input('additional_party_names')),
            'address' => $validated['address'],
            'agreement_period' => $this->formatAgreementPeriod($validated['agreement_period_start'], $validated['agreement_period_end']),
            'advance_amount' => $validated['advance_amount'],
            'monthly_rent_amount' => $validated['monthly_rent_amount'],
            'gst_type' => $validated['gst_type'],
            ...$this->gstPayloadFromValidated($validated),
            ...$this->tdsPayloadFromValidated($validated),
            ...$this->rcmPayloadFromValidated($validated),
            'maintenance_amount' => $validated['maintenance_amount'] ?? null,
            'eb_number' => $validated['eb_number'] ?? null,
            'sq_ft_area' => $validated['sq_ft_area'] ?? null,
            'rent_revision' => $validated['rent_revision'] ?? null,
            'rent_hike_percentage' => $validated['rent_hike_percentage'] ?? null,
            'end_of_agreement_date' => $validated['end_of_agreement_date'],
            'termination_period' => $validated['termination_period'] ?? null,
            'date_of_rent_payment' => $validated['date_of_rent_payment'] ?? null,
            'pan_number' => $validated['pan_number'] ?? null,
            'contact_person_name' => $validated['contact_person_name'] ?? null,
            'contact_person_number' => $validated['contact_person_number'] ?? null,
        ];

        if ($request->hasFile('attachment')) {
            $upload = $this->saveUploaded($request->file('attachment'));
            $this->deleteAttachmentFile($rentalAgreement->attachment_path);
            $payload['attachment_path'] = $upload['path'];
            $payload['attachment_original_name'] = $upload['name'];
        }

        if ($request->hasFile('building_photo')) {
            $upload = $this->saveUploaded($request->file('building_photo'));
            $this->deleteAttachmentFile($rentalAgreement->building_photo_path);
            $payload['building_photo_path'] = $upload['path'];
            $payload['building_photo_original_name'] = $upload['name'];
        }

        $rentalAgreement->update($payload);

        return redirect()
            ->route('rental-agreements.index', $this->indexRedirectQuery($request, $agreementType))
            ->with('success', RentalAgreement::typeLabel($agreementType).' updated.');
    }
}
