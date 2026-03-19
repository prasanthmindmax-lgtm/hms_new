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

        $query = Tblbill::with(['BillLines', 'Tblvendor', 'TblBilling', 'Tblbankdetails'])->where('delete_status',0)
                        ->orderBy('id', 'desc');

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
            return [
                'Date'            => $bill->bill_date,
                'Bill No'         => $bill->bill_number,
                'Vendor Name'     => $bill->vendor_name,
                'Due Date'        => $bill->due_date,
                'Invoice Amount'  => $bill->grand_total_amount,
                'TDS Amount'      => $bill->tax_amount,
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
            'Due Date',
            'Invoice Amount',
            'TDS Amount',
            'Status',
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                if ($this->format === 'csv') {
                    return;
                }
                $sheet = $event->sheet;
                $highestRow = $sheet->getHighestRow();

                /** Header style - consistent with other exports */
                $sheet->getStyle('A1:G1')->applyFromArray([
                    'font' => ['bold' => true],
                    'alignment' => [
                        'horizontal' => 'center',
                        'vertical'   => 'center'
                    ],
                    'fill' => [
                        'fillType' => 'solid',
                        'color' => ['rgb' => 'F8CBAD'] // Light orange color
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => 'thin'
                        ]
                    ]
                ]);

                /** Apply borders to all data cells */
                if ($highestRow > 1) {
                    $sheet->getStyle("A1:G{$highestRow}")->applyFromArray([
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => 'thin'
                            ]
                        ]
                    ]);
                }

                /** Auto size columns */
                foreach (range('A', 'G') as $col) {
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                }

                /** Optional: Format numeric columns */
                if ($highestRow > 1) {
                    // Columns E and F (Invoice Amount and TDS Amount)
                    $sheet->getStyle("E2:F{$highestRow}")->getNumberFormat()->setFormatCode('#,##0.00');

                    /** Optional: Apply background colors to important columns */
                    $sheet->getStyle("E2:E{$highestRow}")->applyFromArray([
                        'fill' => [
                            'fillType' => 'solid',
                            'color' => ['rgb' => 'FFF58A'] // Yellow for Invoice Amount
                        ]
                    ]);

                    $sheet->getStyle("F2:F{$highestRow}")->applyFromArray([
                        'fill' => [
                            'fillType' => 'solid',
                            'color' => ['rgb' => 'B4DEBD'] // Green for TDS Amount
                        ]
                    ]);

                    /** Optional: Highlight the Status column */
                    $sheet->getStyle("G2:G{$highestRow}")->applyFromArray([
                        'fill' => [
                            'fillType' => 'solid',
                            'color' => ['rgb' => 'A3CCDA'] // Blue for Status
                        ]
                    ]);
                }

                /** Optional: Center align specific columns */
                $sheet->getStyle("A2:G{$highestRow}")->getAlignment()->setHorizontal('left');
                $sheet->getStyle("E2:F{$highestRow}")->getAlignment()->setHorizontal('right');
                $sheet->getStyle("G2:G{$highestRow}")->getAlignment()->setHorizontal('center');

                /** Optional: Add total row if needed */
                if ($highestRow > 1) {
                    $totalRow = $highestRow + 1;
                    $sheet->setCellValue("D{$totalRow}", "Total:");
                    $sheet->setCellValue("E{$totalRow}", "=SUM(E2:E{$highestRow})");
                    $sheet->setCellValue("F{$totalRow}", "=SUM(F2:F{$highestRow})");

                    // Style the total row
                    $sheet->getStyle("D{$totalRow}:G{$totalRow}")->applyFromArray([
                        'font' => ['bold' => true],
                        'fill' => [
                            'fillType' => 'solid',
                            'color' => ['rgb' => 'E6E6E6'] // Light gray for total row
                        ],
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => 'thin'
                            ]
                        ]
                    ]);

                    $sheet->getStyle("E{$totalRow}:F{$totalRow}")->getNumberFormat()->setFormatCode('#,##0.00');
                }
            }
        ];
    }
}