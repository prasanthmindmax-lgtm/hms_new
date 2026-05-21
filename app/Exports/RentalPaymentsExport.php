<?php

namespace App\Exports;

use App\Models\RentalAgreement;
use App\Services\LandlordBillPaymentsListingService;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class RentalPaymentsExport implements FromCollection, WithHeadings
{
    public function __construct(
        private readonly Request $request,
        private readonly string $agreementType,
    ) {}

    public function collection(): Collection
    {
        $rows = app(LandlordBillPaymentsListingService::class)
            ->collectDisplayRows($this->request, $this->agreementType);

        return collect($rows)->values()->map(function (array $row, int $index): array {
            $agreement = $row['agreement'] ?? null;
            $type = (string) ($row['agreement_type'] ?? $agreement?->agreement_type ?? '');
            $tdsPct = $row['tds_percent'] ?? null;

            return [
                $index + 1,
                (string) ($row['agreement_number'] ?? $agreement?->agreement_number ?? '—'),
                (string) ($row['owner_name'] ?? '—'),
                $agreement?->company?->company_name ?? '—',
                $this->locationLabel($agreement),
                $type !== '' ? RentalAgreement::typeLabel($type) : '—',
                (string) ($row['nature_label'] ?? '—'),
                (string) ($row['payment_month'] ?? '—'),
                (string) ($row['tds_section'] ?? '194I'),
                $tdsPct !== null ? rtrim(rtrim(number_format((float) $tdsPct, 2), '0'), '.').'%' : '—',
                round((float) ($row['sgst_display'] ?? 0), 2),
                round((float) ($row['cgst_display'] ?? 0), 2),
                round((float) ($row['igst_display'] ?? 0), 2),
                round((float) ($row['other_deductions'] ?? 0), 2),
                round((float) ($row['display_neft'] ?? 0), 2),
                (string) ($row['utr_display'] ?? '—'),
                (string) ($row['status_label'] ?? '—'),
            ];
        });
    }

    public function headings(): array
    {
        return [
            'S.No',
            'Agreement',
            'Landlord',
            'Company',
            'Location',
            'Category',
            'Type',
            'Month',
            'TDS Section',
            'TDS %',
            'SGST',
            'CGST',
            'IGST',
            'Other Deductions',
            'Final NEFT',
            'UTR',
            'Status',
        ];
    }

    private function locationLabel(?RentalAgreement $agreement): string
    {
        if ($agreement === null) {
            return '—';
        }

        $parts = array_filter([
            $agreement->zone?->name,
            $agreement->branch?->name,
        ]);

        return $parts !== [] ? implode(' · ', $parts) : '—';
    }
}
