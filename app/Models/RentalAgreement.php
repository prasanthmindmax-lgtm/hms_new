<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RentalAgreement extends Model
{
    public const TYPE_HOSPITAL = 'hospital';

    public const TYPE_HOSTEL = 'hostel';

    public const TYPES = [
        self::TYPE_HOSPITAL,
        self::TYPE_HOSTEL,
    ];

    /** @var array<string, string> */
    public const TYPE_LABELS = [
        self::TYPE_HOSPITAL => 'Hospital Rental Agreement',
        self::TYPE_HOSTEL => 'Hostel Rental Agreement',
    ];

    public const GST_INCLUDING = 'including_gst';

    public const GST_EXCLUDING = 'excluding_gst';

    public const GST_NONE = 'no_gst';

    public const GST_TYPES = [
        self::GST_INCLUDING,
        self::GST_EXCLUDING,
        self::GST_NONE,
    ];

    /** Tax mode options when GST is applicable (Yes). */
    public const GST_TAX_MODE_LABELS = [
        self::GST_INCLUDING => 'Including GST',
        self::GST_EXCLUDING => 'Excluding GST',
    ];

    /** @var array<string, string> */
    public const GST_LABELS = [
        self::GST_INCLUDING => 'Including GST',
        self::GST_EXCLUDING => 'Excluding GST',
        self::GST_NONE => 'No GST',
    ];

    /** Canonical GST party filter keys (vendor_tbl.vendor_type_name). */
    public const VENDOR_GST_PARTY_REGISTERED = 'GST Registered Party';

    public const VENDOR_GST_PARTY_UNREGISTERED = 'Unregistered Party';

    /** @var list<string> */
    public const VENDOR_GST_PARTY_TYPES = [
        self::VENDOR_GST_PARTY_REGISTERED,
        self::VENDOR_GST_PARTY_UNREGISTERED,
    ];

    /**
     * All vendor_type_name values in DB that match a GST party filter option.
     *
     * @return array<string, list<string>>
     */
    public static function vendorGstPartyDbAliases(): array
    {
        return [
            self::VENDOR_GST_PARTY_REGISTERED => [
                'GST Registered Party',
                'Registered Party',
            ],
            self::VENDOR_GST_PARTY_UNREGISTERED => [
                'Unregistered Party',
                'GST Unregistered Party',
            ],
        ];
    }

    public static function isVendorGstPartyType(?string $vendorTypeName): bool
    {
        return self::canonicalVendorGstPartyType($vendorTypeName) !== null;
    }

    public static function canonicalVendorGstPartyType(?string $vendorTypeName): ?string
    {
        $normalized = mb_strtolower(trim((string) $vendorTypeName));
        if ($normalized === '') {
            return null;
        }

        if (str_contains($normalized, 'unregist')) {
            return self::VENDOR_GST_PARTY_UNREGISTERED;
        }

        if (str_contains($normalized, 'regist')) {
            return self::VENDOR_GST_PARTY_REGISTERED;
        }

        return null;
    }

    public static function vendorGstPartyTypeLabel(?string $vendorTypeName): string
    {
        return match (self::canonicalVendorGstPartyType($vendorTypeName)) {
            self::VENDOR_GST_PARTY_REGISTERED => 'GST Registered Party',
            self::VENDOR_GST_PARTY_UNREGISTERED => 'Unregistered Party',
            default => trim((string) $vendorTypeName),
        };
    }

    /**
     * @return list<string>
     */
    public static function vendorTypeNamesForGstFilter(string $filterKey): array
    {
        $aliases = self::vendorGstPartyDbAliases();

        if (isset($aliases[$filterKey])) {
            return array_values(array_unique($aliases[$filterKey]));
        }

        $canonical = self::canonicalVendorGstPartyType($filterKey);
        if ($canonical !== null && isset($aliases[$canonical])) {
            return array_values(array_unique($aliases[$canonical]));
        }

        $trimmed = trim($filterKey);

        return $trimmed !== '' ? [$trimmed] : [];
    }

    protected $fillable = [
        'agreement_type',
        'agreement_number',
        'company_id',
        'zone_id',
        'branch_id',
        'agreement_date',
        'owner_name',
        'vendor_id',
        'additional_party_names',
        'address',
        'agreement_period',
        'advance_amount',
        'monthly_rent_amount',
        'gst_type',
        'gst_percentage',
        'gst_amount',
        'gst_tax_id',
        'gst_tax_name',
        'gst_tax_type',
        'cgst_amount',
        'sgst_amount',
        'igst_amount',
        'tds_tax_id',
        'tds_tax_name',
        'tds_rate',
        'tds_section_id',
        'tds_section',
        'tds_amount',
        'rcm_applicable',
        'rcm_value',
        'maintenance_amount',
        'eb_number',
        'sq_ft_area',
        'rent_revision',
        'rent_hike_percentage',
        'end_of_agreement_date',
        'termination_period',
        'date_of_rent_payment',
        'pan_number',
        'contact_person_name',
        'contact_person_number',
        'attachment_path',
        'attachment_original_name',
        'building_photo_path',
        'building_photo_original_name',
        'created_by',
    ];

    protected $casts = [
        'agreement_date' => 'date',
        'advance_amount' => 'decimal:2',
        'monthly_rent_amount' => 'decimal:2',
        'gst_percentage' => 'decimal:2',
        'gst_amount' => 'decimal:2',
        'cgst_amount' => 'decimal:2',
        'sgst_amount' => 'decimal:2',
        'igst_amount' => 'decimal:2',
        'tds_rate' => 'decimal:4',
        'tds_amount' => 'decimal:2',
        'rcm_applicable' => 'boolean',
        'rcm_value' => 'decimal:2',
        'maintenance_amount' => 'decimal:2',
        'sq_ft_area' => 'decimal:2',
        'rent_hike_percentage' => 'decimal:2',
        'end_of_agreement_date' => 'date',
    ];

    public static function normalizeType(?string $type): string
    {
        return in_array((string) $type, self::TYPES, true) ? (string) $type : self::TYPE_HOSPITAL;
    }

    public static function typeLabel(?string $type): string
    {
        $type = self::normalizeType($type);

        return self::TYPE_LABELS[$type];
    }

    public static function gstLabel(?string $type): string
    {
        return self::GST_LABELS[(string) $type] ?? 'Not specified';
    }

    public static function gstPillClass(?string $type): string
    {
        return match ($type) {
            self::GST_INCLUDING => 'badge rounded-pill text-bg-success',
            self::GST_EXCLUDING => 'badge rounded-pill text-bg-warning text-dark',
            self::GST_NONE => 'badge rounded-pill text-bg-secondary',
            default => 'badge rounded-pill text-bg-secondary',
        };
    }

    public static function isGstApplicableType(?string $type): bool
    {
        return in_array((string) $type, [self::GST_INCLUDING, self::GST_EXCLUDING], true);
    }

    /**
     * GST amount for rent + maintenance (each line calculated separately, then summed).
     */
    public static function computeGstAmount(
        string $gstType,
        float $monthlyRent,
        float $maintenance,
        float $percentage
    ): float {
        return self::computeGstBreakdown($gstType, $monthlyRent, $maintenance, $percentage)['gst_amount'];
    }

    /**
     * GST breakdown for a single inclusive/exclusive base amount.
     *
     * @return array{
     *     taxable: float,
     *     gst_amount: float,
     *     cgst_amount: float,
     *     sgst_amount: float,
     *     igst_amount: float,
     * }
     */
    public static function computeGstBreakdownForBase(
        string $gstType,
        float $base,
        float $percentage,
        string $taxType = 'GST'
    ): array {
        $base = max(0.0, $base);
        $rate = max(0.0, $percentage);

        $empty = [
            'taxable' => 0.0,
            'gst_amount' => 0.0,
            'cgst_amount' => 0.0,
            'sgst_amount' => 0.0,
            'igst_amount' => 0.0,
        ];

        if ($base <= 0 || $rate <= 0) {
            return $empty;
        }

        $rateFraction = $rate / 100;

        if ($gstType === self::GST_EXCLUDING) {
            $gstAmount = round($base * $rateFraction, 2);
            $taxable = round($base, 2);
        } elseif ($gstType === self::GST_INCLUDING) {
            $taxable = round($base / (1 + $rateFraction), 2);
            $gstAmount = round($base - $taxable, 2);
        } else {
            return $empty;
        }

        $taxKind = strtoupper(trim($taxType)) ?: 'GST';

        if ($taxKind === 'IGST') {
            return [
                'taxable' => $taxable,
                'gst_amount' => $gstAmount,
                'cgst_amount' => 0.0,
                'sgst_amount' => 0.0,
                'igst_amount' => $gstAmount,
            ];
        }

        $sgst = round($gstAmount / 2, 2);
        $cgst = round($gstAmount - $sgst, 2);

        return [
            'taxable' => $taxable,
            'gst_amount' => $gstAmount,
            'cgst_amount' => $cgst,
            'sgst_amount' => $sgst,
            'igst_amount' => 0.0,
        ];
    }

    /**
     * Taxable base and GST split for rent and maintenance (calculated separately, totals combined).
     *
     * @return array{
     *     taxable: float,
     *     gst_amount: float,
     *     cgst_amount: float,
     *     sgst_amount: float,
     *     igst_amount: float,
     *     rent: array{taxable: float, gst_amount: float, cgst_amount: float, sgst_amount: float, igst_amount: float},
     *     maintenance: array{taxable: float, gst_amount: float, cgst_amount: float, sgst_amount: float, igst_amount: float},
     * }
     */
    public static function computeGstBreakdown(
        string $gstType,
        float $monthlyRent,
        float $maintenance,
        float $percentage,
        string $taxType = 'GST'
    ): array {
        $rentBreakdown = self::computeGstBreakdownForBase(
            $gstType,
            max(0.0, $monthlyRent),
            $percentage,
            $taxType
        );
        $maintenanceBreakdown = self::computeGstBreakdownForBase(
            $gstType,
            max(0.0, $maintenance),
            $percentage,
            $taxType
        );

        return [
            'taxable' => round($rentBreakdown['taxable'] + $maintenanceBreakdown['taxable'], 2),
            'gst_amount' => round($rentBreakdown['gst_amount'] + $maintenanceBreakdown['gst_amount'], 2),
            'cgst_amount' => round($rentBreakdown['cgst_amount'] + $maintenanceBreakdown['cgst_amount'], 2),
            'sgst_amount' => round($rentBreakdown['sgst_amount'] + $maintenanceBreakdown['sgst_amount'], 2),
            'igst_amount' => round($rentBreakdown['igst_amount'] + $maintenanceBreakdown['igst_amount'], 2),
            'rent' => $rentBreakdown,
            'maintenance' => $maintenanceBreakdown,
        ];
    }

    /**
     * Register / show: e.g. "18% · ₹3,600.00" or em dash when unset.
     */
    public function gstRateAmountSummary(): string
    {
        if (! self::isGstApplicableType((string) ($this->gst_type ?? ''))) {
            return '—';
        }

        $parts = [];
        if (filled(trim((string) ($this->gst_tax_name ?? '')))) {
            $parts[] = trim((string) $this->gst_tax_name);
        } elseif ($this->gst_percentage !== null && (float) $this->gst_percentage > 0) {
            $parts[] = rtrim(rtrim(number_format((float) $this->gst_percentage, 2), '0'), '.').'%';
        }

        if ($this->gst_amount !== null && (float) $this->gst_amount > 0) {
            $parts[] = '₹'.number_format((float) $this->gst_amount, 2);
        }

        return $parts !== [] ? implode(' · ', $parts) : '—';
    }

    /**
     * @return array<int, array{type: string, rate: float, amount: float}>
     */
    public function gstSplitLines(): array
    {
        if (! self::isGstApplicableType((string) ($this->gst_type ?? ''))) {
            return [];
        }

        $lines = [];
        $taxKind = strtoupper(trim((string) ($this->gst_tax_type ?? 'GST')));

        if ($taxKind === 'IGST' && (float) ($this->igst_amount ?? 0) > 0) {
            $rate = (float) ($this->gst_percentage ?? 0);
            $lines[] = ['type' => 'IGST', 'rate' => $rate, 'amount' => (float) $this->igst_amount];

            return $lines;
        }

        if ((float) ($this->cgst_amount ?? 0) > 0) {
            $half = (float) ($this->gst_percentage ?? 0) / 2;
            $lines[] = ['type' => 'CGST', 'rate' => $half, 'amount' => (float) $this->cgst_amount];
        }
        if ((float) ($this->sgst_amount ?? 0) > 0) {
            $half = (float) ($this->gst_percentage ?? 0) / 2;
            $lines[] = ['type' => 'SGST', 'rate' => $half, 'amount' => (float) $this->sgst_amount];
        }

        return $lines;
    }

    /**
     * Register / show: TDS tax label with rate and amount.
     */
    public function tdsSummary(): string
    {
        $parts = [];
        if (filled(trim((string) ($this->tds_tax_name ?? '')))) {
            $parts[] = trim((string) $this->tds_tax_name);
        }
        $rate = (float) ($this->tds_rate ?? 0);
        if ($rate > 0) {
            $display = $rate <= 1 ? $rate * 100 : $rate;
            $parts[] = rtrim(rtrim(number_format($display, 2), '0'), '.').'%';
        }
        if (filled(trim((string) ($this->tds_section ?? '')))) {
            $parts[] = trim((string) $this->tds_section);
        }
        if ($this->tds_amount !== null && (float) $this->tds_amount > 0) {
            $parts[] = '₹'.number_format((float) $this->tds_amount, 2);
        }

        return $parts !== [] ? implode(' · ', $parts) : '—';
    }

    /** TDS rate as decimal (0.10 for 10%) for landlord payment calculator. */
    public function tdsRateDecimal(): float
    {
        $rate = (float) ($this->tds_rate ?? 0);
        if ($rate <= 0) {
            return 0.0;
        }

        return $rate <= 1 ? $rate : $rate / 100;
    }

    public function isRcmApplicable(): bool
    {
        return (bool) ($this->rcm_applicable ?? false);
    }

    /**
     * Register / show summary: "No" or "Yes — ₹1,234.00".
     */
    public function rcmSummary(): string
    {
        if (! $this->isRcmApplicable()) {
            return 'No';
        }

        $value = $this->rcm_value;
        if ($value === null || (float) $value <= 0) {
            return 'Yes';
        }

        return 'Yes — ₹'.number_format((float) $value, 2);
    }

    public static function attachmentPublicUrl(?string $storedPath): ?string
    {
        if ($storedPath === null || $storedPath === '') {
            return null;
        }

        $path = str_replace('\\', '/', trim((string) $storedPath));
        $path = trim($path, '/');

        if ($path === '' || $path === '.' || $path === '..') {
            return null;
        }

        if (str_starts_with($path, 'uploads/')) {
            return asset($path);
        }

        if (str_starts_with($path, 'public/')) {
            return asset($path);
        }

        if (str_starts_with($path, 'rental_agreement_attachments/')) {
            return asset('public/'.$path);
        }

        $name = basename($path);
        if ($name === '' || $name === '.' || $name === '..') {
            return null;
        }

        return asset('public/rental_agreement_attachments/'.rawurlencode($name));
    }

    public static function buildingPhotoPublicUrl(?string $storedPath): ?string
    {
        return self::attachmentPublicUrl($storedPath);
    }

    /**
     * @return array{badge: string, icon: string, kind: string, tone: string}
     */
    public static function attachmentFileMeta(string $fileName): array
    {
        $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        return match ($ext) {
            'pdf' => ['badge' => 'PDF', 'icon' => 'bi-file-earmark-pdf', 'kind' => 'pdf', 'tone' => 'pdf'],
            'doc', 'docx' => ['badge' => 'DOC', 'icon' => 'bi-file-earmark-word', 'kind' => 'doc', 'tone' => 'doc'],
            'png', 'jpg', 'jpeg', 'gif', 'webp', 'bmp' => [
                'badge' => $ext === 'jpeg' ? 'JPG' : strtoupper($ext),
                'icon' => 'bi-file-earmark-image',
                'kind' => 'image',
                'tone' => 'image',
            ],
            default => [
                'badge' => $ext !== '' ? strtoupper($ext) : 'FILE',
                'icon' => 'bi-file-earmark-text',
                'kind' => 'other',
                'tone' => 'file',
            ],
        };
    }

    /**
     * @return list<string>
     */
    public function additionalPartyNamesList(): array
    {
        $raw = trim((string) ($this->additional_party_names ?? ''));
        if ($raw === '') {
            return [];
        }

        $lines = preg_split('/\r\n|\r|\n/', $raw);
        if (! is_array($lines)) {
            return [];
        }

        $out = [];
        foreach ($lines as $line) {
            $t = trim((string) $line);
            if ($t !== '') {
                $out[] = $t;
            }
        }

        return $out;
    }

    /**
     * Google Maps search URL from zone, branch, and address text.
     */
    public static function googleMapsSearchUrl(?string $zoneName, ?string $branchName, ?string $address): string
    {
        $parts = [];
        foreach ([$branchName, $zoneName, $address] as $p) {
            $t = trim((string) $p);
            if ($t !== '') {
                $parts[] = $t;
            }
        }
        $q = implode(', ', $parts);

        if ($q === '') {
            return 'https://www.google.com/maps';
        }

        return 'https://www.google.com/maps/search/?api=1&query='.rawurlencode($q);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(usermanagementdetails::class, 'created_by');
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Tblvendor::class, 'vendor_id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Tblcompany::class, 'company_id');
    }

    public function zone(): BelongsTo
    {
        return $this->belongsTo(TblZonesModel::class, 'zone_id');
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(TblLocationModel::class, 'branch_id');
    }

    public function scopeActiveWithinDays(Builder $query, int $days): Builder
    {
        $today = Carbon::today();

        return $query
            ->whereDate('end_of_agreement_date', '>=', $today->toDateString())
            ->whereDate('end_of_agreement_date', '<=', $today->copy()->addDays($days)->toDateString());
    }

    /**
     * @return array{0: Carbon, 1: Carbon}|null
     */
    public static function parseAgreementPeriodDates(?string $raw): ?array
    {
        $raw = trim((string) $raw);
        if ($raw === '' || ! preg_match('/\s+to\s+/i', $raw)) {
            return null;
        }

        $parts = preg_split('/\s+to\s+/i', $raw, 2);
        if (! is_array($parts) || count($parts) !== 2) {
            return null;
        }

        foreach (['d-m-Y', 'd/m/Y', 'Y-m-d'] as $format) {
            try {
                $start = Carbon::createFromFormat($format, trim((string) $parts[0]))->startOfDay();
                $end = Carbon::createFromFormat($format, trim((string) $parts[1]))->startOfDay();

                return [$start, $end];
            } catch (\Throwable $e) {
                // Try next format.
            }
        }

        try {
            return [
                Carbon::parse(trim((string) $parts[0]))->startOfDay(),
                Carbon::parse(trim((string) $parts[1]))->startOfDay(),
            ];
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * Year-wise monthly rent schedule (Yr 1, Yr 2, …) using rent hike % on each anniversary.
     *
     * @return list<array{label: string, period_label: string, monthly_rent: float}>
     */
    public function yearlyRentSchedule(): array
    {
        $parsed = self::parseAgreementPeriodDates($this->agreement_period);
        if ($parsed === null) {
            return [];
        }

        [$start, $end] = $parsed;
        if ($end->lt($start)) {
            return [];
        }

        $baseRent = (float) $this->monthly_rent_amount;
        $hikePct = max(0, (float) ($this->rent_hike_percentage ?? 0));

        $schedule = [];
        $yearIndex = 1;
        $cursor = $start->copy();

        while ($cursor->lte($end) && $yearIndex <= 40) {
            $yearEnd = $cursor->copy()->addYear()->subDay();
            if ($yearEnd->gt($end)) {
                $yearEnd = $end->copy();
            }

            $rent = $yearIndex === 1
                ? $baseRent
                : round($baseRent * pow(1 + ($hikePct / 100), $yearIndex - 1), 2);

            $schedule[] = [
                'label' => 'Yr '.$yearIndex,
                'period_label' => $cursor->format('d M Y').' – '.$yearEnd->format('d M Y'),
                'monthly_rent' => $rent,
            ];

            if ($yearEnd->gte($end)) {
                break;
            }

            $cursor = $yearEnd->copy()->addDay();
            $yearIndex++;
        }

        return $schedule;
    }
}
