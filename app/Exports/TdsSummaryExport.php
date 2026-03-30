<?php

namespace App\Exports;

use App\Models\Tblbill;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class TdsSummaryExport implements FromCollection, WithHeadings, WithEvents
{
    protected $filters;
    protected $format;

    public function __construct(array $filters, $format = 'xlsx')
    {
        $this->filters = $filters;
        $this->format = $format;
    }

    public function collection()
    {
        $filters = $this->filters;

        $query = Tblbill::with([
            'BillLines',
            'Tblvendor',
            'TblBilling',
            'Tblbankdetails',
            'TblTDSsection',
            'TblTDSsection.section',
        ])->where('delete_status', 0)->orderBy('id', 'desc');

        // Date filter
        if (!empty($filters['date_from']) && !empty($filters['date_to'])) {
            $from = Carbon::createFromFormat('d/m/Y', $filters['date_from'])->startOfDay();
            $to   = Carbon::createFromFormat('d/m/Y', $filters['date_to'])->endOfDay();
            $query->whereBetween('created_at', [$from, $to]);
        }

        // Vendor filter
        if (!empty($filters['vendor_id'])) {
            $vendorIds = explode(',', $filters['vendor_id']);
            $query->whereIn('vendor_id', $vendorIds);
        }

        // Bill IDs filter
        if (!empty($filters['bill_ids'])) {
            $billIds = explode(',', $filters['bill_ids']);
            $query->whereIn('id', $billIds);
        }

        // Common conditions
        $query->where('tax_amount', '>', 0)
              ->where('tds_paid_status', 'Not Paid');

        // Fetch and map data
        return $query->get()->map(function ($bill) {

            // Account names from bill lines — unique, comma-separated
            $accountNames = $bill->BillLines
                ? $bill->BillLines->pluck('account')->filter()->unique()->values()->implode(', ')
                : '-';

            // TDS section details from TblTDSsection relation
            $tdsTax     = $bill->TblTDSsection;
            $tdsSection = $tdsTax ? ($tdsTax->section_name ?? ($tdsTax->section->name ?? '-')) : '-';
            $tdsRate    = $tdsTax ? ($tdsTax->tax_rate ?? '-') : '-';
            $tdsName    = $tdsTax ? ($tdsTax->tax_name ?? '-') : '-';

            return [
                'Date'            => $bill->bill_date,
                'Bill No'         => $bill->bill_number,
                'Vendor Name'     => $bill->vendor_name,
                'Account Names'   => $accountNames ?: '-',
                'Due Date'        => $bill->due_date,
                'Invoice Amount'  => $bill->grand_total_amount,
                'TDS Amount'      => $bill->tax_amount,
                'TDS Section'     => $tdsSection,
                'TDS Name'        => $tdsName,
                'TDS Rate (%)'    => $tdsRate,
                'Status'          => ucfirst($bill->bill_status),
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Date',
            'Bill No',
            'Vendor Name',
            'Account Names',
            'Due Date',
            'Invoice Amount',
            'TDS Amount',
            'TDS Section',
            'TDS Name',
            'TDS Rate (%)',
            'Status',
        ];
    }

    public function registerEvents(): array
    {
        // Last column is K (11 columns: A–K)
        $lastCol    = 'K';
        $headerRange = "A1:{$lastCol}1";

        return [
            AfterSheet::class => function (AfterSheet $event) use ($lastCol, $headerRange) {
                if ($this->format === 'csv') {
                    return;
                }
                $sheet      = $event->sheet;
                $highestRow = $sheet->getHighestRow();

                /** Header style */
                $sheet->getStyle($headerRange)->applyFromArray([
                    'font' => ['bold' => true],
                    'alignment' => [
                        'horizontal' => 'center',
                        'vertical'   => 'center',
                    ],
                    'fill' => [
                        'fillType' => 'solid',
                        'color'    => ['rgb' => 'F8CBAD'],
                    ],
                    'borders' => [
                        'allBorders' => ['borderStyle' => 'thin'],
                    ],
                ]);

                /** Apply borders to all data cells */
                if ($highestRow > 1) {
                    $sheet->getStyle("A1:{$lastCol}{$highestRow}")->applyFromArray([
                        'borders' => [
                            'allBorders' => ['borderStyle' => 'thin'],
                        ],
                    ]);
                }

                /** Auto-size all columns */
                foreach (range('A', $lastCol) as $col) {
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                }

                if ($highestRow > 1) {
                    // F = Invoice Amount, G = TDS Amount
                    $sheet->getStyle("F2:G{$highestRow}")->getNumberFormat()->setFormatCode('#,##0.00');

                    // Yellow for Invoice Amount (F)
                    $sheet->getStyle("F2:F{$highestRow}")->applyFromArray([
                        'fill' => ['fillType' => 'solid', 'color' => ['rgb' => 'FFF58A']],
                    ]);

                    // Green for TDS Amount (G)
                    $sheet->getStyle("G2:G{$highestRow}")->applyFromArray([
                        'fill' => ['fillType' => 'solid', 'color' => ['rgb' => 'B4DEBD']],
                    ]);

                    // Light blue for TDS Section (H), TDS Name (I), TDS Rate (J)
                    $sheet->getStyle("H2:J{$highestRow}")->applyFromArray([
                        'fill' => ['fillType' => 'solid', 'color' => ['rgb' => 'DAEEF3']],
                    ]);

                    // Blue for Status (K)
                    $sheet->getStyle("K2:K{$highestRow}")->applyFromArray([
                        'fill' => ['fillType' => 'solid', 'color' => ['rgb' => 'A3CCDA']],
                    ]);

                    // Alignment
                    $sheet->getStyle("A2:{$lastCol}{$highestRow}")->getAlignment()->setHorizontal('left');
                    $sheet->getStyle("F2:G{$highestRow}")->getAlignment()->setHorizontal('right');
                    $sheet->getStyle("J2:J{$highestRow}")->getAlignment()->setHorizontal('center');
                    $sheet->getStyle("K2:K{$highestRow}")->getAlignment()->setHorizontal('center');

                    // Total row
                    $totalRow = $highestRow + 1;
                    $sheet->setCellValue("E{$totalRow}", "Total:");
                    $sheet->setCellValue("F{$totalRow}", "=SUM(F2:F{$highestRow})");
                    $sheet->setCellValue("G{$totalRow}", "=SUM(G2:G{$highestRow})");

                    $sheet->getStyle("E{$totalRow}:{$lastCol}{$totalRow}")->applyFromArray([
                        'font' => ['bold' => true],
                        'fill' => [
                            'fillType' => 'solid',
                            'color'    => ['rgb' => 'E6E6E6'],
                        ],
                        'borders' => [
                            'allBorders' => ['borderStyle' => 'thin'],
                        ],
                    ]);

                    $sheet->getStyle("F{$totalRow}:G{$totalRow}")->getNumberFormat()->setFormatCode('#,##0.00');
                }
            },
        ];
    }
}
