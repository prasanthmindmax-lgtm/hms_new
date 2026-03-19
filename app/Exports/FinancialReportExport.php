<?php

namespace App\Exports;

use App\Models\BranchFinancialReport;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;

class FinancialReportExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths, WithColumnFormatting
{
    protected $filters;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = BranchFinancialReport::with(['creator', 'updater']);

        // Apply filters
        if (!empty($this->filters['start_date']) && !empty($this->filters['end_date'])) {
            $query->whereBetween('report_date', [$this->filters['start_date'], $this->filters['end_date']]);
        } elseif (!empty($this->filters['start_date'])) {
            $query->where('report_date', '>=', $this->filters['start_date']);
        } elseif (!empty($this->filters['end_date'])) {
            $query->where('report_date', '<=', $this->filters['end_date']);
        }

        if (!empty($this->filters['zone_id'])) {
            $query->where('zone_id', $this->filters['zone_id']);
        }

        if (!empty($this->filters['branch_id'])) {
            $query->where('branch_id', $this->filters['branch_id']);
        }

        return $query->orderBy('report_date', 'desc')->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Report Date',
            'Zone',
            'Branch',
            'Radiant Collected Date',
            'Radiant Collection (₹)',
            'Card Amount (₹)',
            'Bank Deposit (₹)',
            'Total Collection (₹)',
            'Discount (₹)',
            'Cancelled Bills (₹)',
            'Refunds (₹)',
            'Total Deductions (₹)',
            'Net Amount (₹)',
            'Placed By',
            'Locker By',
            'Cash Given By',
            'Created By',
            'Edit Count',
            'Created At',
            'Last Updated',
        ];
    }

    public function map($report): array
    {
        return [
            $report->id,
            $report->report_date->format('Y-m-d'),
            $report->zone_name,
            $report->branch_name,
            $report->radiant_collected_date ? $report->radiant_collected_date->format('Y-m-d') : 'N/A',
            (float) $report->radiant_collection_amount,
            (float) $report->actual_card_amount,
            (float) $report->bank_deposit_amount,
            (float) $report->total_collection,
            (float) $report->today_discount_amount,
            (float) $report->cancel_bill_amount,
            (float) $report->refund_bill_amount,
            (float) $report->total_deductions,
            (float) $report->net_amount,
            $report->placed_by_whom ?? 'N/A',
            $report->locker_by_whom ?? 'N/A',
            $report->who_gave_radiant_cash ?? 'N/A',
            $report->creator->name ?? 'N/A',
            $report->edit_count,
            $report->created_at->format('Y-m-d H:i:s'),
            $report->updated_at->format('Y-m-d H:i:s'),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'size' => 12],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4F81BD']
                ],
                'font' => ['color' => ['rgb' => 'FFFFFF'], 'bold' => true],
            ],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 8,  // ID
            'B' => 15, // Report Date
            'C' => 20, // Zone
            'D' => 25, // Branch
            'E' => 20, // Radiant Date
            'F' => 18, // Radiant Amount
            'G' => 18, // Card Amount
            'H' => 18, // Bank Amount
            'I' => 20, // Total Collection
            'J' => 15, // Discount
            'K' => 18, // Cancelled
            'L' => 15, // Refunds
            'M' => 20, // Total Deductions
            'N' => 18, // Net Amount
            'O' => 20, // Placed By
            'P' => 20, // Locker By
            'Q' => 20, // Cash Given By
            'R' => 20, // Created By
            'S' => 12, // Edit Count
            'T' => 20, // Created At
            'U' => 20, // Updated At
        ];
    }

    public function columnFormats(): array
    {
        return [
            'F' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'G' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'H' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'I' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'J' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'K' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'L' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'M' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'N' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
        ];
    }
}
