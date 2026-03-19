<?php

namespace App\Exports;

use App\Models\Tblbill;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class TdsDetailedExport implements FromCollection, WithHeadings, WithEvents
{
    protected $request;
    protected $format;

    public function __construct($request, $format = 'xlsx')
    {
        $this->request = $request;
        $this->format = $format;
    }

    public function collection()
    {
        $query = Tblbill::where('tds_paid_status', 'Paid')
            ->where('tax_amount', '>', 0)->where('delete_status',0);

        /** Financial year filter */
        if ($this->request->filled('financial_name')) {
            [$start, $end] = explode('-', $this->request->financial_name);

            $query->whereBetween(
                DB::raw("STR_TO_DATE(bill_date, '%d/%m/%Y')"),
                ["{$start}-04-01", "{$end}-03-31"]
            );
        }

        /** Other filters */
        foreach (['zone_id','branch_id','company_id','vendor_id'] as $field) {
            if ($this->request->filled($field)) {
                $query->whereIn($field, explode(',', $this->request->$field));
            }
        }

        if ($this->request->filled('bill_ids')) {
            $query->whereIn('id', explode(',', $this->request->bill_ids));
        }

        $rows = collect();
        $sno = 1;

        foreach ($query->orderBy(
            DB::raw("STR_TO_DATE(bill_date, '%d/%m/%Y')")
        )->get() as $bill) {

            $rows->push([
                $sno++,
                Carbon::createFromFormat('d/m/Y', $bill->bill_date)->format('M'),
                $bill->Tblvendor->vendor_id,
                $bill->vendor_name,
                $bill->branch_name,
                $bill->vendor_designation ?? 'Consultant Gynec',
                number_format($bill->grand_total_amount),
                number_format($bill->grand_total_amount),
                number_format($bill->tax_amount),
                number_format($bill->grand_total_amount - $bill->tax_amount),
                Carbon::createFromFormat('d/m/Y', $bill->tds_pay_date)->format('d.m.Y'),
                $bill->tds_utr_no,
            ]);
        }

        return $rows;
    }

    public function headings(): array
    {
        return [
            'S.No',
            'Month',
            'ID',
            'Name',
            'Branch',
            'Designation',
            'Gross Profee fee',
            'Gross Earnings',
            'TDS 10%',
            'Net Profee fee',
            'Date Of Payment',
            'UTR Number',
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

                /** Header style */
                $sheet->getStyle('A1:L1')->applyFromArray([
                    'font' => ['bold' => true],
                    'alignment' => [
                        'horizontal' => 'center',
                        'vertical'   => 'center'
                    ],
                    'fill' => [
                        'fillType' => 'solid',
                        'color' => ['rgb' => 'F8CBAD']
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => 'thin'
                        ]
                    ]
                ]);
                /** DATA CELL COLORS (G, H, J) */
                for ($row = 2; $row <= $highestRow; $row++) {

                    // Skip TOTAL row
                    if ($sheet->getCell("E{$row}")->getValue() === 'Total') {
                        continue;
                    }

                    // G & H → Gross columns (Yellow)
                    $sheet->getStyle("G{$row}")->applyFromArray([
                        'fill' => [
                            'fillType' => 'solid',
                            'color' => ['rgb' => 'FFF58A']
                        ]
                    ]);

                    // I → TDS (Orange)
                    $sheet->getStyle("H{$row}")->applyFromArray([
                        'fill' => [
                            'fillType' => 'solid',
                            'color' => ['rgb' => 'B4DEBD']
                        ]
                    ]);

                    // J → Net Fee (Green)
                    $sheet->getStyle("J{$row}")->applyFromArray([
                        'fill' => [
                            'fillType' => 'solid',
                            'color' => ['rgb' => 'A3CCDA']
                        ]
                    ]);
                }



                /** Borders for entire table */
                $sheet->getStyle("A1:L{$highestRow}")->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => 'thin'
                        ]
                    ]
                ]);

                /** Auto size columns */
                foreach (range('A', 'L') as $col) {
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                }
            }
        ];
    }
}
