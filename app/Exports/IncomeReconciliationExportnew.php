<?php

namespace App\Exports;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class IncomeReconciliationExportnew implements
    FromCollection,
    WithHeadings,
    WithMapping,
    WithEvents,
    ShouldAutoSize
{
    protected $dateRange;
    protected $filter;
    protected $rows;

    public function __construct($dateRange, $filter)
    {
        $this->dateRange = $dateRange;
        $this->filter    = $filter;
    }

    // ======================================================
    // DATA COLLECTION
    // ======================================================
    public function collection()
    {
        $query = DB::table('income_reconciliation_table');

        if ($this->dateRange) {
            [$from, $to] = explode(' - ', $this->dateRange);
            $start = Carbon::createFromFormat('d/m/Y', trim($from))->startOfDay();
            $end   = Carbon::createFromFormat('d/m/Y', trim($to))->endOfDay();

            $query->whereBetween(
                DB::raw("STR_TO_DATE(date_range,'%d/%m/%Y')"),
                [$start, $end]
            );
        }

        preg_match("/tblzones\.name='([^']+)'/", $this->filter, $zone);
        if (!empty($zone[1])) {
            $query->where('zone_name', $zone[1]);
        }

        preg_match("/tbl_locations\.name='([^']+)'/", $this->filter, $loc);
        if (!empty($loc[1])) {
            $query->where('location_name', $loc[1]);
        }

        $this->rows = $query
            ->orderByRaw("STR_TO_DATE(date_range,'%d/%m/%Y')")
            ->get();

        return $this->rows;
    }

    // ======================================================
    // HEADERS (2 ROWS)
    // ======================================================
    public function headings(): array
    {
        return [
            [
                'Date', 'Zone', 'Location',
                'MOC DOC', '', '', '', '', '', '',
                'Radiant cash collection', '', '', '',
                'MESPOS ORANGE', '',
                'BANK Deposit', '', '', '', '',
                'DIFFERENCE', '', '', '', ''
            ],
            [
                '', '', '',
                'Cash', 'Card', 'UPI', 'Total Card/UPI', 'NEFT', 'Others', 'TOTAL',
                'Date of Collection', 'Collection Amount', 'Date of Deposit', 'Deposit Amount',
                'Card', 'UPI',
                'Date of Settlement', 'Bank Charges UPI/Card', 'UPI / CARD', 'NEFT', 'OTHERS',
                'Radiant Deposit', 'Cash Deposit', 'UPI CARD BANK CHARGES', 'Others / NEFT',
                'Remarks'
            ]
        ];
    }

    // ======================================================
    // ROW MAPPING
    // ======================================================
    public function map($r): array
    {
        return [
            $r->date_range,
            $r->zone_name,
            $r->location_name,

            $r->moc_cash_amt,
            $r->moc_card_amt,
            $r->moc_upi_amt,
            $r->moc_total_upi_card,
            $r->moc_neft_amt,
            $r->moc_other_amt,
            $r->moc_overall_total,

            $r->date_collection,
            $r->collection_amount,
            $r->date_deposited,
            $r->deposite_amount,

            $r->mespos_card,
            $r->mespos_upi,

            $r->date_settlement,
            $r->bank_chargers,
            $r->bank_upi_card,
            $r->bank_neft,
            $r->bank_others,

            $r->radiant_diff,
            $r->cash_diff,
            $r->card_upi_diff,
            $r->neft_others_diff,

            $r->remark ?? '',
        ];
    }

    // ======================================================
    // STYLING + FREEZE + TOTAL ROW
    // ======================================================
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {

                $sheet = $event->sheet->getDelegate();
                $lastRow = $sheet->getHighestRow();

                // -------------------------------
                // MERGE HEADERS
                // -------------------------------
                $sheet->mergeCells('A1:A2');
                $sheet->mergeCells('B1:B2');
                $sheet->mergeCells('C1:C2');

                $sheet->mergeCells('D1:J1');
                $sheet->mergeCells('K1:N1');
                $sheet->mergeCells('O1:P1');
                $sheet->mergeCells('Q1:U1');
                $sheet->mergeCells('V1:Z1');

                // -------------------------------
                // FREEZE DATE+ZONE+LOCATION
                // -------------------------------
                $sheet->freezePane('D3');

                // -------------------------------
                // HEADER STYLE (VISIBLE FIX)
                // -------------------------------
                $headerStyle = [
                    'font' => [
                        'bold' => true,
                        'color' => ['argb' => 'FFFFFFFF'],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical'   => Alignment::VERTICAL_CENTER,
                        'wrapText'   => true,
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'color' => ['argb' => 'FF6C70E8'], // same like screenshot
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => 'FFFFFFFF'],
                        ],
                    ],
                ];

                $sheet->getStyle('A1:Z2')->applyFromArray($headerStyle);
                $sheet->getRowDimension(1)->setRowHeight(32);
                $sheet->getRowDimension(2)->setRowHeight(32);

                // -------------------------------
                // DATA BORDERS
                // -------------------------------
                $sheet->getStyle("A3:Z{$lastRow}")->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => 'FFD9D9D9'],
                        ],
                    ],
                ]);

                // -------------------------------
                // DIFFERENCE COLOR LOGIC
                // -------------------------------
                foreach (['V', 'W', 'X', 'Y'] as $col) {
                    for ($r = 3; $r <= $lastRow; $r++) {
                        $val = $sheet->getCell($col.$r)->getValue();
                        if (!is_numeric($val)) continue;

                        if ((float)$val == 0) {
                            // GREEN (match)
                            $sheet->getStyle($col.$r)->applyFromArray([
                                'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['argb' => 'FFDFF6DD']],
                                'font' => ['color' => ['argb' => 'FF107C10']],
                            ]);
                        } else {
                            // RED (difference)
                            $sheet->getStyle($col.$r)->applyFromArray([
                                'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['argb' => 'FFFDE2E1']],
                                'font' => ['color' => ['argb' => 'FFD13438']],
                            ]);
                        }
                    }
                }

                // -------------------------------
                // TOTAL ROW (LAST LINE)
                // -------------------------------
                $totalRow = $lastRow + 1;
                $sheet->setCellValue("A{$totalRow}", 'TOTAL');

                foreach (range('D', 'Y') as $col) {
                    $sheet->setCellValue(
                        "{$col}{$totalRow}",
                        "=SUM({$col}3:{$col}{$lastRow})"
                    );
                }

                $sheet->getStyle("A{$totalRow}:Z{$totalRow}")->applyFromArray([
                    'font' => ['bold' => true],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['argb' => 'FFE7F3FF']],
                    'borders' => [
                        'top' => [
                            'borderStyle' => Border::BORDER_THICK,
                            'color' => ['argb' => 'FF000000'],
                        ],
                    ],
                ]);

                // -------------------------------
                // NUMBER FORMAT
                // -------------------------------
                foreach (range('D', 'Y') as $col) {
                    $sheet->getStyle("{$col}3:{$col}{$totalRow}")
                        ->getNumberFormat()
                        ->setFormatCode('#,##0.00');
                }
            }
        ];
    }
}
