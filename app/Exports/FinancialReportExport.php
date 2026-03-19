<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class FinancialReportExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths, WithColumnFormatting
{
    protected $reports;

    public function __construct($reports)
    {
        $this->reports = $reports;
    }

    public function collection()
    {
        return collect($this->reports);
    }

    public function headings(): array
    {
        return [
            'ID',
            'Report Date',
            'Zone',
            'Branch',
            'Radiant Collection Period',
            'Radiant Collection (₹)',
            'Card Amount (₹)',
            'UPI Amount (₹)',
            'Deposit Amount (₹)',
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
            'Auditor Status',
            'Management Status',
            'Created By',
            'Created At',
            'Last Updated',
        ];
    }

    public function map($report): array
    {
        // Radiant collection period
        if ($report->radiant_collection_from_date && $report->radiant_collection_to_date) {
            $radiantPeriod = $report->radiant_collection_from_date->format('Y-m-d')
                . ' to ' . $report->radiant_collection_to_date->format('Y-m-d');
        } elseif ($report->radiant_collected_date) {
            $radiantPeriod = $report->radiant_collected_date->format('Y-m-d');
        } else {
            $radiantPeriod = 'N/A';
        }

        $auditorStatus = ['0' => 'Pending', '1' => 'Approved', '2' => 'Rejected'];
        $mgmtStatus    = ['0' => 'Pending', '1' => 'Approved', '2' => 'Rejected'];

        return [
            $report->id,
            $report->report_date ? $report->report_date->format('Y-m-d') : 'N/A',
            optional($report->zone)->name ?? 'N/A',
            optional($report->branch)->name ?? 'N/A',
            $radiantPeriod,
            (float) ($report->radiant_collection_amount ?? 0),
            (float) ($report->actual_card_amount ?? 0),
            (float) ($report->upi_amount ?? 0),
            (float) ($report->deposit_amount ?? 0),
            (float) ($report->bank_deposit_amount ?? 0),
            (float) $report->total_collection,
            (float) ($report->today_discount_amount ?? 0),
            (float) ($report->cancel_bill_amount ?? 0),
            (float) ($report->refund_bill_amount ?? 0),
            (float) $report->total_deductions,
            (float) $report->net_amount,
            $report->placed_by_whom ?? 'N/A',
            $report->locker_by_whom ?? 'N/A',
            $report->who_gave_radiant_cash ?? 'N/A',
            $auditorStatus[$report->auditor_approval_status] ?? 'Pending',
            $mgmtStatus[$report->management_approval_status] ?? 'Pending',
            optional($report->creator)->user_fullname ?? 'N/A',
            $report->created_at ? $report->created_at->format('Y-m-d H:i:s') : 'N/A',
            $report->updated_at ? $report->updated_at->format('Y-m-d H:i:s') : 'N/A',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['color' => ['rgb' => 'FFFFFF'], 'bold' => true, 'size' => 11],
                'fill' => [
                    'fillType'   => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4472C4'],
                ],
            ],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 8,   // ID
            'B' => 14,  // Report Date
            'C' => 20,  // Zone
            'D' => 25,  // Branch
            'E' => 24,  // Radiant Period
            'F' => 18,  // Radiant
            'G' => 16,  // Card
            'H' => 16,  // UPI
            'I' => 16,  // Deposit
            'J' => 16,  // Bank Deposit
            'K' => 20,  // Total Collection
            'L' => 14,  // Discount
            'M' => 16,  // Cancelled
            'N' => 14,  // Refunds
            'O' => 18,  // Total Deductions
            'P' => 16,  // Net Amount
            'Q' => 20,  // Placed By
            'R' => 20,  // Locker By
            'S' => 20,  // Cash Given By
            'T' => 16,  // Auditor Status
            'U' => 18,  // Management Status
            'V' => 22,  // Created By
            'W' => 20,  // Created At
            'X' => 20,  // Last Updated
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
            'O' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'P' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
        ];
    }
}
