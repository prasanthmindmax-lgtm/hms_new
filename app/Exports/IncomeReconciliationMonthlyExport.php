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

class IncomeReconciliationMonthlyExport implements
    FromCollection,
    WithHeadings,
    WithMapping,
    WithEvents,
    ShouldAutoSize
{
    protected $filter;
    protected $dateRange;
    protected $rows;

    public function __construct($filter, $dateRange)
    {
        $this->filter = $filter;
        $this->dateRange = $dateRange;
    }

    /* =====================================================
     * COLLECTION (MONTHLY – BRANCH WISE)
     * ===================================================== */
    public function collection()
    {
        $query = DB::table('income_reconciliation_table')
            ->select(
                'zone_name',
                'location_name',

                DB::raw('SUM(moc_cash_amt) as moc_cash'),
                DB::raw('SUM(moc_card_amt) as moc_card'),
                DB::raw('SUM(moc_upi_amt) as moc_upi'),
                DB::raw('SUM(moc_total_upi_card) as moc_total_upi_card'),
                DB::raw('SUM(moc_neft_amt) as moc_neft'),
                DB::raw('SUM(moc_other_amt) as moc_others'),

                DB::raw('SUM(deposite_amount) as actual_cash'),
                DB::raw('SUM(bank_chargers) as bank_chargers'),
                DB::raw('SUM(bank_upi_card) as bank_upi_card'),
                DB::raw('SUM(bank_neft) as actual_neft'),
                DB::raw('SUM(bank_others) as actual_others'),

                DB::raw('SUM(radiant_diff) as cash_diff'),
                DB::raw('
                    SUM(moc_total_upi_card)
                    - (SUM(bank_chargers) + SUM(bank_upi_card))
                    as upi_card_diff
                '),
                DB::raw('SUM(moc_neft_amt) - SUM(bank_neft) as neft_diff'),
                DB::raw('SUM(moc_other_amt) - SUM(bank_others) as others_diff')
            );

        /* ===============================
        * SAFE DATE FILTER
        * =============================== */
        if (!empty($this->dateRange) && str_contains($this->dateRange, ' - ')) {

            [$from, $to] = explode(' - ', $this->dateRange);

            try {
                $start = Carbon::createFromFormat('d/m/Y', trim($from))->startOfDay();
                $end   = Carbon::createFromFormat('d/m/Y', trim($to))->endOfDay();

                $query->whereBetween(
                    DB::raw("STR_TO_DATE(date_range,'%d/%m/%Y')"),
                    [$start, $end]
                );
            } catch (\Exception $e) {
                // fallback if format fails
            }
        }

        /* ===============================
        * FILTERS
        * =============================== */
          // ZONE FILTER
          preg_match("/tblzones\.name='([^']+)'/", $this->filter, $zone);
          $zones = !empty($zone[1])
              ? array_map('trim', explode(',', $zone[1]))
              : [];

          if ($zones) {
              $query->whereIn('zone_name', $zones);
          }

          // LOCATION FILTER
          preg_match("/tbl_locations\.name='([^']+)'/", $this->filter, $loc);
          $locations = !empty($loc[1])
              ? array_map('trim', explode(',', $loc[1]))
              : [];

          if ($locations) {
              $query->whereIn('location_name', $locations);
          }


        /* ===============================
        * GROUP BY (IMPORTANT FIX)
        * =============================== */
        $query->groupBy('zone_name', 'location_name')
              ->orderBy('location_name');

        // DEBUG (TEMP)
        // dd($query->get());
        return $query->get();
    }


    /* =====================================================
     * HEADERS (MONTHLY)
     * ===================================================== */
    public function headings(): array
    {
        return [
            [
                'S.No', 'Zone', 'Branch',
                'As per Mocdoc Sale', '', '', '', '', '',
                'Actual Collection As per Bank Statement', '', '', '', '',
                'Difference', '', '', ''
            ],
            [
                '', '', '',
                'Cash', 'Card', 'UPI', 'Total Card/UPI', 'NEFT', 'Other',
                'Cash', 'Bank Charges', 'Total Card/UPI', 'NEFT', 'Other',
                'Cash', 'Total Card/UPI', 'NEFT', 'Other'
            ]
        ];
    }

    /* =====================================================
     * MAP
     * ===================================================== */
    public function map($r): array
    {
        static $i = 1;

        return [
            $i++,
            $r->zone_name,
            $r->location_name,

            $r->moc_cash,
            $r->moc_card,
            $r->moc_upi,
            $r->moc_total_upi_card,
            $r->moc_neft,
            $r->moc_others,

            $r->actual_cash,
            $r->bank_chargers,
            $r->bank_upi_card,
            $r->actual_neft,
            $r->actual_others,

            $r->cash_diff,
            $r->upi_card_diff,
            $r->neft_diff,
            $r->others_diff,
        ];
    }

    /* =====================================================
     * STYLING + FREEZE + TOTAL
     * ===================================================== */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {

                $sheet = $event->sheet->getDelegate();
                $lastRow = $sheet->getHighestRow();

                /* Merge headers */
                $sheet->mergeCells('A1:A2');
                $sheet->mergeCells('B1:B2');
                $sheet->mergeCells('C1:C2');
                $sheet->mergeCells('D1:I1');
                $sheet->mergeCells('J1:N1');
                $sheet->mergeCells('O1:R1');

                /* Freeze 3 columns */
                $sheet->freezePane('D3');

                /* Header style */
                $sheet->getStyle('A1:R2')->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                        'wrapText' => true,
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'color' => ['argb' => 'FF6C70E8'],
                    ],
                ]);

                /* Difference coloring */
                foreach (['O','P','Q','R'] as $col) {
                    for ($r = 3; $r <= $lastRow; $r++) {
                        $v = $sheet->getCell($col.$r)->getValue();
                        if (!is_numeric($v)) continue;

                        $styleArray = ['fill' => ['fillType' => Fill::FILL_SOLID]];

                        if ($v == 0) {
                            // Green for zero
                            $styleArray['fill']['color'] = ['argb' => 'FFDFF6DD'];
                        } elseif ($v > 0) {
                            // Red for greater than 0
                            $styleArray['fill']['color'] = ['argb' => 'FFFDE2E1'];
                        } else {
                            // Light blue for less than 0
                            $styleArray['fill']['color'] = ['argb' => 'FFE1F5FE'];
                        }

                        $sheet->getStyle($col.$r)->applyFromArray($styleArray);
                    }
                }

                /* TOTAL ROW */
                $totalRow = $lastRow + 1;
                $sheet->setCellValue("A{$totalRow}", 'TOTAL');

                foreach (range('D','R') as $col) {
                    $sheet->setCellValue("{$col}{$totalRow}", "=SUM({$col}3:{$col}{$lastRow})");
                }

                $sheet->getStyle("A{$totalRow}:R{$totalRow}")->applyFromArray([
                    'font' => ['bold' => true],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['argb' => 'FFE7F3FF']],
                    'borders' => [
                        'top' => ['borderStyle' => Border::BORDER_THICK],
                    ],
                ]);
            }
        ];
    }
}
