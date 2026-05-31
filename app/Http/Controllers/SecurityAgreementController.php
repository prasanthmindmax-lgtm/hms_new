<?php

namespace App\Http\Controllers;

use App\Models\SecurityAgreement;
use App\Services\FileUploadService;
use App\Models\Tblgsttax;
use App\Models\Tbltdstax;
use App\Models\Tblcompany;
use App\Models\TblLocationModel;
use App\Models\Tblvendor;
use App\Models\TblZonesModel;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class SecurityAgreementController extends Controller
{
    public function __construct(
        private readonly FileUploadService $fileUpload
    ) {}

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
        $prefix = 'SA-'.$year.'-';

        $last = SecurityAgreement::query()
            ->where('agreement_number', 'like', $prefix.'%')
            ->orderByDesc('id')
            ->value('agreement_number');

        $next = 1;
        if ($last && preg_match('/^'.preg_quote($prefix, '/').'(\d+)$/', (string) $last, $matches)) {
            $next = (int) $matches[1] + 1;
        }

        return $prefix.str_pad((string) $next, 5, '0', STR_PAD_LEFT);
    }

    private function resolveAgreementType(Request $request, ?SecurityAgreement $securityAgreement = null): string
    {
        if ($securityAgreement) {
            return SecurityAgreement::normalizeType($securityAgreement->agreement_type);
        }

        $segment = strtolower(trim((string) ($request->query('segment') ?? $request->input('segment', ''))));
        if (! in_array($segment, SecurityAgreement::TYPES, true)) {
            $segment = SecurityAgreement::TYPE_HOSPITAL;
        }

        return $segment;
    }

    /**
     * @return array<string, mixed>
     */
    private function moduleViewData(string $agreementType): array
    {
        $moduleTitle = SecurityAgreement::typeLabel($agreementType);

        return [
            'agreementType' => $agreementType,
            'moduleTitle' => $moduleTitle,
            'moduleTitleLower' => Str::lower($moduleTitle),
            'moduleRegisterTitle' => $moduleTitle.' Register',
            'routeNames' => [
                'index' => 'security-agreements.index',
                'create' => 'security-agreements.create',
                'store' => 'security-agreements.store',
                'show' => 'security-agreements.show',
                'edit' => 'security-agreements.edit',
                'update' => 'security-agreements.update',
                'destroy' => 'security-agreements.destroy',
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
            'moduleTitle' => 'Security agreements',
            'moduleTitleLower' => 'security agreement',
            'moduleRegisterTitle' => 'Security agreements register',
            'routeNames' => [
                'index' => 'security-agreements.index',
                'create' => 'security-agreements.create',
                'store' => 'security-agreements.store',
                'show' => 'security-agreements.show',
                'edit' => 'security-agreements.edit',
                'update' => 'security-agreements.update',
                'destroy' => 'security-agreements.destroy',
            ],
        ];
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
        if (in_array($category, SecurityAgreement::TYPES, true)) {
            return [$category, $category];
        }

        $legacy = strtolower(trim((string) ($request->query('segment') ?? $request->input('segment', ''))));
        if (in_array($legacy, SecurityAgreement::TYPES, true)) {
            return [$legacy, $legacy];
        }

        return [null, 'all'];
    }

    /**
     * Query string for security-agreements.index after create/update.
     *
     * @return array<string, string>
     */
    private function indexRedirectQuery(Request $request, string $agreementType): array
    {
        $category = strtolower(trim((string) ($request->query('category') ?? $request->input('category', ''))));
        if ($category === 'all' || $category === '') {
            return ['category' => 'all'];
        }
        if (in_array($category, SecurityAgreement::TYPES, true)) {
            return ['category' => $category];
        }
        if (in_array($agreementType, SecurityAgreement::TYPES, true)) {
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
        $query = SecurityAgreement::query();
        if ($agreementType !== null && in_array($agreementType, SecurityAgreement::TYPES, true)) {
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
            $query->whereIn('security_agreements.vendor_id', $landlordVendorIds);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('agreement_date', '>=', $request->date('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->whereDate('agreement_date', '<=', $request->date('date_to'));
        }

        $vendorGstPartyDbNames = $this->vendorGstPartyNamesForFilter($request);
        if ($vendorGstPartyDbNames !== []) {
            $query->whereIn('security_agreements.vendor_id', function ($vendorSub) use ($vendorGstPartyDbNames) {
                $vendorSub->select('vendor_tbl.id')
                    ->from('vendor_tbl')
                    ->where('vendor_tbl.active_status', 0)
                    ->where('vendor_tbl.party_type', Tblvendor::PARTY_VENDOR)
                    ->whereIn('vendor_tbl.vendor_type_name', $vendorGstPartyDbNames);
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
                    ->orWhere('address', 'like', $like)
                    ->orWhere('agreement_period', 'like', $like)
                    ->orWhere('pan_number', 'like', $like)
                    ->orWhere('contact_person_name', 'like', $like)
                    ->orWhere('contact_person_number', 'like', $like)
                    ->orWhereHas('vendor', function (Builder $vendorQuery) use ($like) {
                        $vendorQuery->where('display_name', 'like', $like)
                            ->orWhere('company_name', 'like', $like);
                    })
                    ->orWhereHas('company', function (Builder $companyQuery) use ($like) {
                        $companyQuery->where('company_name', 'like', $like);
                    })
                    ->orWhereHas('zone', function (Builder $zoneQuery) use ($like) {
                        $zoneQuery->where('name', 'like', $like);
                    })
                    ->orWhereHas('branch', function (Builder $branchQuery) use ($like) {
                        $branchQuery->where('name', 'like', $like);
                    });
            });
        }

        return $query;
    }

    private function resolveVendorIdFromRequest(Request $request): int
    {
        $vendorId = (int) $request->input('vendor_id', 0);
        if ($vendorId <= 0) {
            return 0;
        }

        return $this->activeVendorQuery()->whereKey($vendorId)->exists()
            ? $vendorId
            : 0;
    }

    private function activeVendorQuery()
    {
        return Tblvendor::query()->active();
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
        $vendors = $this->activeVendorQuery()
            ->get([
                'id',
                'display_name',
                'company_name',
                'vendor_id',
                'vendor_first_name',
                'vendor_last_name',
                'pan_number',
                'vendor_type_name',
                'party_type',
            ])
            ->sortBy(fn (Tblvendor $vendor) => strtolower($this->vendorDisplayLabel($vendor)))
            ->values();

        return [
            'vendors' => $vendors,
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
    private function serviceVendorFilterOptions()
    {
        return $this->activeVendorQuery()
            ->get(['id', 'display_name', 'company_name', 'vendor_id', 'vendor_first_name', 'vendor_last_name'])
            ->sortBy(fn (Tblvendor $vendor) => strtolower($this->vendorDisplayLabel($vendor)))
            ->values();
    }

    private function vendorDisplayLabel(Tblvendor $vendor): string
    {
        return $vendor->listDisplayLabel();
    }

    /**
     * GST type filter options keyed by canonical vendor_type_name (Registered / Unregistered party).
     *
     * @return array<string, string>
     */
    private function vendorGstPartyFilterOptions(): array
    {
        $options = [];
        foreach (SecurityAgreement::VENDOR_GST_PARTY_TYPES as $canonical) {
            $options[$canonical] = SecurityAgreement::vendorGstPartyTypeLabel($canonical);
        }

        $fromMaster = $this->activeVendorQuery()
            ->whereNotNull('vendor_type_name')
            ->where('vendor_type_name', '!=', '')
            ->distinct()
            ->orderBy('vendor_type_name')
            ->pluck('vendor_type_name');

        foreach ($fromMaster as $dbName) {
            $canonical = SecurityAgreement::canonicalVendorGstPartyType((string) $dbName);
            if ($canonical !== null) {
                $options[$canonical] = SecurityAgreement::vendorGstPartyTypeLabel($canonical);
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
            $dbNames = array_merge($dbNames, SecurityAgreement::vendorTypeNamesForGstFilter($filterKey));
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
            'address' => 'required|string|max:5000',
            'agreement_period' => 'required|string|max:50',
            'agreement_period_start' => 'required|date',
            'agreement_period_end' => 'required|date|after_or_equal:agreement_period_start',
            'security_fixed_salary_amount' => 'required|numeric|min:0',
            'housekeeping_fixed_salary_amount' => 'required|numeric|min:0',
            'housekeeping_paid_leave_applicable' => 'required|in:0,1',
            'housekeeping_paid_leave_days' => 'nullable|required_if:housekeeping_paid_leave_applicable,1|integer|min:1|max:366',
            'gst_applicable' => 'required|in:0,1',
            'gst_type' => 'nullable|required_if:gst_applicable,1|in:'.SecurityAgreement::GST_INCLUDING.','.SecurityAgreement::GST_EXCLUDING,
            'gst_tax_id' => 'nullable|required_if:gst_applicable,1|integer',
            'gst_tax_name' => 'nullable|required_if:gst_applicable,1|string|max:120',
            'gst_tax_type' => 'nullable|required_if:gst_applicable,1|string|in:GST,IGST',
            'gst_percentage' => 'nullable|required_if:gst_applicable,1|numeric|min:0|max:100',
            'gst_amount' => 'nullable|required_if:gst_applicable,1|numeric|min:0',
            'cgst_amount' => 'nullable|numeric|min:0',
            'sgst_amount' => 'nullable|numeric|min:0',
            'igst_amount' => 'nullable|numeric|min:0',
            'tds_tax_id' => ['required', 'integer', Rule::exists('tds_tax_tbl', 'id')],
            'rcm_applicable' => 'required|in:0,1',
            'rcm_value' => 'nullable|required_if:rcm_applicable,1|numeric|min:0',
            'end_of_agreement_date' => 'required|date|after_or_equal:agreement_date',
            'termination_period' => 'required|string|max:120',
            'pan_number' => 'required|string|max:30',
            'contact_person_name' => 'required|string|max:255',
            'contact_person_number' => 'required|string|max:30',
            'vendor_id' => [
                'required',
                'integer',
                'min:1',
                Rule::exists('vendor_tbl', 'id')->where(function ($q) {
                    $q->where('active_status', 0);
                }),
            ],
            ...$this->fileValidationRules($isEdit),
        ];

        if (! $isEdit) {
            $rules['agreement_type'] = 'required|in:'.implode(',', SecurityAgreement::TYPES);
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
     * @param  array<string, mixed>  $validated
     * @return array<string, mixed>
     */
    private function housekeepingPaidLeavePayloadFromValidated(array $validated): array
    {
        $applicable = (string) ($validated['housekeeping_paid_leave_applicable'] ?? '0') === '1';

        return [
            'housekeeping_paid_leave_applicable' => $applicable,
            'housekeeping_paid_leave_days' => $applicable
                ? (int) ($validated['housekeeping_paid_leave_days'] ?? 0)
                : null,
        ];
    }

    /**
     * @param  array<string, mixed>  $validated
     * @return array<string, mixed>
     */
    private function normalizeGstValidated(array $validated): array
    {
        if ((string) ($validated['gst_applicable'] ?? '0') !== '1') {
            $validated['gst_type'] = SecurityAgreement::GST_NONE;

            return $validated;
        }

        $type = (string) ($validated['gst_type'] ?? '');
        if (! in_array($type, [SecurityAgreement::GST_INCLUDING, SecurityAgreement::GST_EXCLUDING], true)) {
            $validated['gst_type'] = SecurityAgreement::GST_EXCLUDING;
        }

        return $validated;
    }

    /**
     * @return array<string, mixed>
     */
    private function gstPayloadFromValidated(array $validated): array
    {
        $gstType = (string) ($validated['gst_type'] ?? '');

        if (! SecurityAgreement::isGstApplicableType($gstType)) {
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
        $securityBase = SecurityAgreement::effectiveServiceTaxBase(
            isset($validated['security_fixed_salary_amount']) ? (float) $validated['security_fixed_salary_amount'] : null
        );
        $housekeepingBase = SecurityAgreement::effectiveServiceTaxBase(
            isset($validated['housekeeping_fixed_salary_amount']) ? (float) $validated['housekeeping_fixed_salary_amount'] : null
        );
        $breakdown = SecurityAgreement::computeGstBreakdown(
            $gstType,
            $securityBase,
            $housekeepingBase,
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
        $tax = Tbltdstax::query()->with('section:id,name')->find((int) ($validated['tds_tax_id'] ?? 0));
        if ($tax === null) {
            return [
                'tds_tax_id' => null,
                'tds_tax_name' => null,
                'tds_rate' => null,
                'tds_section_id' => null,
                'tds_section' => null,
                'tds_amount' => null,
            ];
        }

        $rate = (float) $tax->tax_rate;
        if ($rate > 0 && $rate <= 1) {
            $rate = $rate * 100;
        }

        $sectionName = trim((string) ($tax->section_name ?? $tax->section?->name ?? ''));

        $breakdown = SecurityAgreement::computeTdsBreakdown(
            SecurityAgreement::effectiveServiceTaxBase(
                isset($validated['security_fixed_salary_amount']) ? (float) $validated['security_fixed_salary_amount'] : null
            ),
            SecurityAgreement::effectiveServiceTaxBase(
                isset($validated['housekeeping_fixed_salary_amount']) ? (float) $validated['housekeeping_fixed_salary_amount'] : null
            ),
            $rate
        );

        return [
            'tds_tax_id' => (int) $tax->id,
            'tds_tax_name' => trim((string) $tax->tax_name) ?: null,
            'tds_rate' => round($rate, 4),
            'tds_section_id' => $tax->section_id ? (int) $tax->section_id : null,
            'tds_section' => $sectionName !== '' ? $sectionName : null,
            'tds_amount' => $breakdown['total'],
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
            'agreement_date.required' => 'Security agreement date is required.',
            'vendor_id.required' => 'Please select a vendor from the list.',
            'vendor_id.exists' => 'Selected vendor is invalid or inactive.',
            'security_fixed_salary_amount.required' => 'Security fixed salary amount is required.',
            'housekeeping_fixed_salary_amount.required' => 'Housekeeping fixed salary amount is required.',
            'housekeeping_paid_leave_applicable.required' => 'Select whether paid leave applies for housekeeping.',
            'housekeeping_paid_leave_days.required_if' => 'Enter paid leave days for housekeeping when applicable.',
            'termination_period.required' => 'Termination period is required.',
            'pan_number.required' => 'PAN number is required.',
            'contact_person_name.required' => 'Contact person name is required.',
            'contact_person_number.required' => 'Contact person number is required.',
            'address.required' => 'Address is required.',
            'agreement_period.required' => 'Agreement period is required.',
            'agreement_period_start.required' => 'Select the agreement period start date.',
            'agreement_period_end.required' => 'Select the agreement period end date.',
            'agreement_period_end.after_or_equal' => 'Agreement period end date must be the same as or after the start date.',
            'gst_applicable.required' => 'Select whether GST is applicable (Yes or No).',
            'gst_type.required_if' => 'Select tax mode (Including or Excluding GST).',
            'gst_tax_id.required_if' => 'Select a GST rate from the list.',
            'gst_tax_name.required_if' => 'Select a GST rate from the list.',
            'gst_percentage.required_if' => 'Select a GST rate from the list.',
            'gst_amount.required_if' => 'GST amount is required when GST is applicable.',
            'tds_tax_id.required' => 'Select a TDS tax from the list.',
            'tds_tax_id.exists' => 'Selected TDS tax is not valid.',
            'rcm_applicable.required' => 'Select whether RCM is applicable (Yes or No).',
            'rcm_applicable.in' => 'RCM must be Yes or No.',
            'rcm_value.required_if' => 'Enter the RCM value when RCM is Yes.',
            'end_of_agreement_date.required' => 'End of agreement date is required.',
            'end_of_agreement_date.after_or_equal' => 'End of agreement date must be the same as or after the agreement date.',
            ...$this->fileValidationMessages(),
            'agreement_type.required' => 'Select a category (Hospital or Hostel).',
            'agreement_type.in' => 'Selected category is not valid.',
        ];
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
            'security_salary_total' => (float) (clone $base)->sum('security_fixed_salary_amount'),
            'housekeeping_salary_total' => (float) (clone $base)->sum('housekeeping_fixed_salary_amount'),
            'ending_soon' => (clone $base)->activeWithinDays(30)->count(),
        ];

        $records = (clone $base)
            ->with([
                'creator:id,user_fullname',
                'vendor:id,display_name,company_name',
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
            'perPageChoices' => $perPageChoices,
            'perPage' => $perPage,
            'gstOptions' => $this->vendorGstPartyFilterOptions(),
            'stateOptions' => $this->billStateFilterOptions(),
            'rcmOptions' => $this->rcmFilterOptions(),
            'indexCategoryQuery' => $indexCategoryQuery,
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
            $this->serviceVendorFilterOptions(),
        ));
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
                ->map(fn (Tblvendor $vendor) => $this->vendorDisplayLabel($vendor))
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
        $raListingBaseUrl = route('security-agreements.index');

        $raCreateUrl = route('security-agreements.create');
        if ($indexCategoryQuery !== 'all') {
            $raCreateUrl .= '?'.http_build_query(['category' => $indexCategoryQuery]);
        }

        $chipUrl = function (array $without) use ($raListingBaseUrl, $raListQs, $request): string {
            $strip = array_merge($without, ['page', 'segment', 'category']);

            return $raListingBaseUrl.'?'.http_build_query(
                array_merge($raListQs, $request->except($strip))
            );
        };
        $clearRegisterFiltersUrl = route('security-agreements.index');

        $categoryChipLabel = '';
        if ($indexCategoryQuery !== 'all') {
            $categoryChipLabel = match ($indexCategoryQuery) {
                SecurityAgreement::TYPE_HOSPITAL => 'Category: Hospital',
                SecurityAgreement::TYPE_HOSTEL => 'Category: Hostel',
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
            'landlordDisp' => $selectedLandlordLabel !== '' ? $selectedLandlordLabel : 'All vendors',
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

    public function index(Request $request): View|\Illuminate\Http\Response
    {
        $data = $this->indexViewData($request);

        if ($request->ajax()) {
            $view = view('superadmin.security_agreements.index', $data);

            return response(
                $view->fragment('sa-register-stats')
                . $view->fragment('sa-register-chips')
                . $view->fragment('sa-register-panel')
            );
        }

        return view('superadmin.security_agreements.index', $data);
    }

    public function create(Request $request): View
    {
        $this->userRow();
        $pre = strtolower(trim((string) ($request->query('category') ?? $request->query('segment') ?? '')));
        if (! in_array($pre, SecurityAgreement::TYPES, true)) {
            $pre = SecurityAgreement::TYPE_HOSPITAL;
        }

        return view('superadmin.security_agreements.create', array_merge([
            'admin' => auth()->user(),
            'record' => null,
            'isEdit' => false,
            'gstOptions' => SecurityAgreement::GST_TAX_MODE_LABELS,
            'gstTaxes' => $this->activeGstTaxes(),
            'tdsTaxes' => $this->activeTdsTaxes(),
            'defaultAgreementType' => $pre,
        ], $this->locationDropdownData(), $this->vendorDropdownData(), $this->moduleViewDataForAll()));
    }

    public function store(Request $request): RedirectResponse
    {
        $this->userRow();
        $validated = $this->normalizeGstValidated($request->validate($this->rules($request), $this->messages()));
        $this->assertRequiredFileUploads($request, null, false);
        $agreementType = SecurityAgreement::normalizeType((string) $validated['agreement_type']);
        $vendorId = $this->resolveVendorIdFromRequest($request);
        if ($vendorId <= 0) {
            return back()->withInput()->withErrors(['vendor_id' => 'Please select a vendor from the list.']);
        }

        $payload = [
            'agreement_type' => $agreementType,
            'agreement_number' => $this->nextAgreementNumber(),
            'company_id' => $validated['company_id'],
            'zone_id' => $validated['zone_id'],
            'branch_id' => $validated['branch_id'],
            'agreement_date' => $validated['agreement_date'],
            'vendor_id' => $vendorId,
            'address' => $validated['address'],
            'agreement_period' => $this->formatAgreementPeriod($validated['agreement_period_start'], $validated['agreement_period_end']),
            'security_fixed_salary_amount' => $validated['security_fixed_salary_amount'] ?? null,
            'housekeeping_fixed_salary_amount' => $validated['housekeeping_fixed_salary_amount'] ?? null,
            ...$this->housekeepingPaidLeavePayloadFromValidated($validated),
            'gst_type' => $validated['gst_type'],
            ...$this->gstPayloadFromValidated($validated),
            ...$this->tdsPayloadFromValidated($validated),
            ...$this->rcmPayloadFromValidated($validated),
            'end_of_agreement_date' => $validated['end_of_agreement_date'],
            'termination_period' => $validated['termination_period'] ?? null,
            'pan_number' => $validated['pan_number'] ?? null,
            'contact_person_name' => $validated['contact_person_name'] ?? null,
            'contact_person_number' => $validated['contact_person_number'] ?? null,
            'created_by' => (int) auth()->id(),
        ];

        $this->applyFileUploadsToPayload($request, $payload, null, false);

        SecurityAgreement::query()->create($payload);

        return redirect()
            ->route('security-agreements.index', $this->indexRedirectQuery($request, $agreementType))
            ->with('success', 'Security agreement saved successfully.');
    }

    public function show(Request $request, SecurityAgreement $securityAgreement): View
    {
        $this->userRow();
        $agreementType = $this->resolveAgreementType($request, $securityAgreement);
        $securityAgreement->load([
            'creator:id,user_fullname',
            'vendor:id,display_name,company_name',
            'company:id,company_name',
            'zone:id,name',
            'branch:id,name,zone_id',
        ]);

        return view('superadmin.security_agreements.show', array_merge([
            'admin' => auth()->user(),
            'record' => $securityAgreement,
        ], $this->moduleViewData($agreementType)));
    }

    public function edit(Request $request, SecurityAgreement $securityAgreement): View
    {
        $this->userRow();
        $agreementType = $this->resolveAgreementType($request, $securityAgreement);

        return view('superadmin.security_agreements.create', array_merge([
            'admin' => auth()->user(),
            'record' => $securityAgreement,
            'isEdit' => true,
            'gstOptions' => SecurityAgreement::GST_TAX_MODE_LABELS,
            'gstTaxes' => $this->activeGstTaxes(),
            'tdsTaxes' => $this->activeTdsTaxes(),
        ], $this->locationDropdownData(), $this->vendorDropdownData(), $this->moduleViewData($agreementType)));
    }

    public function update(Request $request, SecurityAgreement $securityAgreement): RedirectResponse
    {
        $this->userRow();
        $agreementType = $this->resolveAgreementType($request, $securityAgreement);
        $validated = $this->normalizeGstValidated($request->validate($this->rules($request, true), $this->messages()));
        $this->assertRequiredFileUploads($request, $securityAgreement, true);
        $vendorId = $this->resolveVendorIdFromRequest($request);
        if ($vendorId <= 0) {
            return back()->withInput()->withErrors(['vendor_id' => 'Please select a vendor from the list.']);
        }

        $payload = [
            'company_id' => $validated['company_id'],
            'zone_id' => $validated['zone_id'],
            'branch_id' => $validated['branch_id'],
            'agreement_date' => $validated['agreement_date'],
            'vendor_id' => $vendorId,
            'address' => $validated['address'],
            'agreement_period' => $this->formatAgreementPeriod($validated['agreement_period_start'], $validated['agreement_period_end']),
            'security_fixed_salary_amount' => $validated['security_fixed_salary_amount'] ?? null,
            'housekeeping_fixed_salary_amount' => $validated['housekeeping_fixed_salary_amount'] ?? null,
            ...$this->housekeepingPaidLeavePayloadFromValidated($validated),
            'gst_type' => $validated['gst_type'],
            ...$this->gstPayloadFromValidated($validated),
            ...$this->tdsPayloadFromValidated($validated),
            ...$this->rcmPayloadFromValidated($validated),
            'end_of_agreement_date' => $validated['end_of_agreement_date'],
            'termination_period' => $validated['termination_period'] ?? null,
            'pan_number' => $validated['pan_number'] ?? null,
            'contact_person_name' => $validated['contact_person_name'] ?? null,
            'contact_person_number' => $validated['contact_person_number'] ?? null,
        ];

        $this->applyFileUploadsToPayload($request, $payload, $securityAgreement, true);

        $securityAgreement->update($payload);

        return redirect()
            ->route('security-agreements.index', $this->indexRedirectQuery($request, $agreementType))
            ->with('success', 'Security agreement updated successfully.');
    }

    public function destroy(Request $request, SecurityAgreement $securityAgreement): RedirectResponse
    {
        $this->userRow();
        $agreementType = SecurityAgreement::normalizeType((string) $securityAgreement->agreement_type);
        $agreementNumber = (string) $securityAgreement->agreement_number;
        $this->deleteAgreementFiles($securityAgreement);
        $securityAgreement->delete();

        return redirect()
            ->route('security-agreements.index', $this->indexRedirectQuery($request, $agreementType))
            ->with('success', 'Agreement '.$agreementNumber.' deleted.');
    }

    private function deleteAgreementFiles(SecurityAgreement $record): void
    {
        $folder = SecurityAgreement::FILE_STORAGE_FOLDER;

        foreach (SecurityAgreement::FILE_SLOTS as $slot => $meta) {
            foreach ($record->filesForSlot($slot) as $file) {
                $path = (string) ($file['path'] ?? '');
                if ($path !== '') {
                    $this->fileUpload->delete($path, $folder);
                }
            }
        }
    }

    /**
     * @return array<string, mixed>
     */
    private function fileValidationRules(bool $isEdit): array
    {
        $item = $this->fileUpload->validationRule();
        $rules = [];

        foreach (SecurityAgreement::FILE_SLOTS as $slot => $meta) {
            $input = SecurityAgreement::FILE_INPUT_NAMES[$slot];
            $keep = SecurityAgreement::FILE_KEEP_INPUT_NAMES[$slot];
            $rules[$input] = 'nullable|array';
            $rules[$input.'.*'] = $item;
            if ($isEdit) {
                $rules[$keep] = 'nullable|array';
                $rules[$keep.'.*'] = 'string|max:500';
            }
        }

        return $rules;
    }

    /**
     * @return array<string, string>
     */
    private function fileValidationMessages(): array
    {
        $messages = [];
        foreach (SecurityAgreement::FILE_SLOTS as $slot => $meta) {
            $input = SecurityAgreement::FILE_INPUT_NAMES[$slot];
            $label = (string) ($meta['label'] ?? $slot);
            $messages[$input.'.*.max'] = $label.' must not be larger than 10 MB.';
            $messages[$input.'.*.mimes'] = $label.' must be PDF, image, Word, or Excel.';
        }

        return $messages;
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private function applyFileUploadsToPayload(
        Request $request,
        array &$payload,
        ?SecurityAgreement $record,
        bool $isEdit
    ): void {
        $folder = SecurityAgreement::FILE_STORAGE_FOLDER;

        foreach (SecurityAgreement::FILE_SLOTS as $slot => $meta) {
            $input = SecurityAgreement::FILE_INPUT_NAMES[$slot];
            $keepKey = SecurityAgreement::FILE_KEEP_INPUT_NAMES[$slot];
            $existing = $record !== null ? $record->filesForSlot($slot) : [];
            $files = [];

            if ($isEdit) {
                $keepPaths = array_values(array_filter(array_map(
                    static fn ($v) => trim((string) $v),
                    (array) $request->input($keepKey, [])
                ), static fn ($v) => $v !== ''));

                foreach ($existing as $file) {
                    $path = (string) ($file['path'] ?? '');
                    if ($path !== '' && in_array($path, $keepPaths, true)) {
                        $files[] = $file;

                        continue;
                    }
                    $this->fileUpload->delete($path !== '' ? $path : null, $folder);
                }
            }

            if ($request->hasFile($input)) {
                $uploaded = $this->fileUpload->uploadMultiple($request->file($input), $folder);
                $files = array_merge($files, $uploaded);
            }

            $payload[$meta['column']] = $this->fileUpload->encodeFiles($files);
        }
    }

    private function assertRequiredFileUploads(
        Request $request,
        ?SecurityAgreement $record,
        bool $isEdit
    ): void {
        $errors = [];

        foreach (SecurityAgreement::FILE_SLOTS as $slot => $meta) {
            $input = SecurityAgreement::FILE_INPUT_NAMES[$slot];
            $keepKey = SecurityAgreement::FILE_KEEP_INPUT_NAMES[$slot];
            $label = (string) ($meta['label'] ?? $slot);

            $existing = $record !== null ? $record->filesForSlot($slot) : [];
            $keepPaths = $isEdit
                ? array_values(array_filter(array_map(
                    static fn ($v) => trim((string) $v),
                    (array) $request->input($keepKey, [])
                ), static fn ($v) => $v !== ''))
                : [];

            $keptCount = 0;
            if ($isEdit) {
                foreach ($existing as $file) {
                    if (in_array((string) ($file['path'] ?? ''), $keepPaths, true)) {
                        $keptCount++;
                    }
                }
            }

            $newCount = 0;
            if ($request->hasFile($input)) {
                $uploaded = $request->file($input);
                $newCount = is_array($uploaded) ? count($uploaded) : 1;
            }

            if ($keptCount + $newCount < 1) {
                $errors[$input] = 'Please upload at least one '.$label.'.';
            }
        }

        if ($errors !== []) {
            throw ValidationException::withMessages($errors);
        }
    }
}
