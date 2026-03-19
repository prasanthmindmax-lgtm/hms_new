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

class IncomeReconciliationExport implements
    FromCollection,
    WithHeadings,
    WithMapping,
    WithEvents,
    ShouldAutoSize
{
    protected $dateRange;
    protected $filter;

    public function __construct($dateRange,$filter)
    {
        $this->dateRange = $dateRange;
        $this->filter = $filter;
    }

    public function collection()
    {
        $query = DB::table('income_reconciliation_table');

        if ($this->dateRange) {
            [$from,$to] = explode(' - ', $this->dateRange);

            $start = Carbon::createFromFormat('d/m/Y',$from)->startOfDay();
            $end   = Carbon::createFromFormat('d/m/Y',$to)->endOfDay();

            $query->whereBetween(
                DB::raw("STR_TO_DATE(date_range,'%d/%m/%Y')"),
                [$start,$end]
            );
        }

        preg_match("/tblzones\.name='([^']+)'/", $this->filter, $zoneMatch);
        if (!empty($zoneMatch[1])) {
            $query->where('zone_name',$zoneMatch[1]);
        }

        preg_match("/tbl_locations\.name='([^']+)'/", $this->filter, $locMatch);
        if (!empty($locMatch[1])) {
            $query->where('location_name',$locMatch[1]);
        }

        return $query
            ->orderByRaw("STR_TO_DATE(date_range,'%d/%m/%Y')")
            ->get();
    }


    public function headings(): array
    {
        return [
            [
                'Date','Zone','Location',

                'Cash','Cash','','','',

                'Card','','',

                'UPI','','',

                'Bank Statement','','',

                'NEFT','','',
            ],
            [
                '','','',
                'Moc doc','Radiant','Diff','Challan','Diff',

                'Moc doc','Orange','Diff',

                'Moc doc','Orange','Diff',

                'Charges','Amount','Diff',

                'Moc doc','BS','Diff',
            ]
        ];
    }


    public function map($row): array
    {
        return [
            $row->date_range,
            $row->zone_name,
            $row->location_name,

            $row->cash_moc_amt,
            $row->cash_radiant,
            $row->cash_date_amt_filter - $row->cash_radiant ,
            $row->cash_bank,
            $row->cash_radiant - $row->cash_bank,

            $row->card_moc_amt,
            $row->card_radiant,
            $row->card_date_amt_filter - $row->card_radiant,

            $row->upi_moc_amt,
            $row->upi_radiant,
            $row->upi_date_amt_filter - $row->upi_radiant,

            $row->bank_stmt_charge,
            $row->bank_stmt_amount,
            $row->bank_stmt_diff,

            $row->neft_moc_amt,
            $row->neft_bank,
            $row->neft_date_amt_filter - $row->neft_bank,
        ];
    }


    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {

                $sheet = $event->sheet->getDelegate();

                // ===============================
                // 🔹 MERGE HEADINGS
                // ===============================
                $sheet->mergeCells('A1:A2');   // Date
                $sheet->mergeCells('B1:B2');   // Zone
                $sheet->mergeCells('C1:C2');   // Location

                $sheet->mergeCells('D1:H1');   // Cash
                $sheet->mergeCells('I1:K1');   // Card
                $sheet->mergeCells('L1:N1');   // UPI
                $sheet->mergeCells('O1:Q1');   // Bank
                $sheet->mergeCells('R1:T1');   // NEFT


                // ===============================
                // 🎨 HEADING BACKGROUND COLOR
                // ===============================
                $sheet->getStyle('A1:T2')->applyFromArray([
                    'font' => ['bold' => true],
                    'alignment' => [
                        'horizontal' => 'center',
                        'vertical'   => 'center'
                    ],
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'color'    => ['rgb' => 'D9E1F2']   // light blue header
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' =>
                                \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        ],
                    ],
                ]);

                $sheet->freezePane('A3');


                // ===============================
                // 🔴🟡🟢 COLOR DIFF VALUES
                // ===============================
                $highestRow = $sheet->getHighestRow();

                $diffCols = ['F','H','K','N','Q','T'];

                foreach ($diffCols as $col) {
                    for ($row = 3; $row <= $highestRow; $row++) {

                        $cell = $sheet->getCell($col.$row);
                        $value = $cell->getValue();

                        if (!is_numeric($value)) continue;

                        if ($value < 0) {
                            // 🔴 NEGATIVE
                            $color = 'FFC7CE';
                        } elseif ($value > 0) {
                            // 🟡 POSITIVE
                            $color = 'FFC7CE';
                        } else {
                            // 🟢 ZERO
                            $color = 'C6EFCE';
                        }

                        $sheet->getStyle($col.$row)->applyFromArray([
                            'fill' => [
                                'fillType' =>
                                    \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                                'color' => ['rgb' => $color]
                            ]
                        ]);
                    }
                }
            }
        ];
    }
}
