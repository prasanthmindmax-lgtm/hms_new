<?php
namespace App\Exports;

use App\Models\Tblbill;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class TdsFyWiseExport implements FromCollection, WithHeadings, WithEvents
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

        /** Financial year */
        if ($this->request->filled('financial_name')) {
            [$start, $end] = explode('-', $this->request->financial_name);
            $query->whereBetween(
                DB::raw("STR_TO_DATE(bill_date, '%d/%m/%Y')"),
                ["$start-04-01", "$end-03-31"]
            );
        }

        foreach (['zone_id','branch_id','company_id','vendor_id','bill_ids'] as $field) {
            if ($this->request->filled($field)) {
              if($field === 'bill_ids'){
                $query->whereIn('id', explode(',', $this->request->$field));
              }else{
                $query->whereIn($field, explode(',', $this->request->$field));
              }
            }
        }

        $bills = $query
            ->orderBy('vendor_id')
            ->orderByRaw("STR_TO_DATE(bill_date, '%d/%m/%Y')")
            ->get()
            ->groupBy('vendor_id');

        $rows = collect();
        $sno = 1;

        foreach ($bills as $vendorBills) {

            $vendorGross = $vendorTds = $vendorNet = 0;

            foreach ($vendorBills as $bill) {

                $gross = $bill->grand_total_amount;
                $tds   = $bill->tax_amount;
                $net   = $gross - $tds;

                $vendorGross += $gross;
                $vendorTds   += $tds;
                $vendorNet   += $net;

                $rows->push([
                    $sno++,
                    Carbon::createFromFormat('d/m/Y', $bill->bill_date)->format('M'),
                    $bill->Tblvendor->vendor_id,
                    $bill->vendor_name,
                    $bill->branch_name,
                    $bill->vendor_designation ?? 'Consultant',
                    number_format($gross),
                    number_format($gross),
                    number_format($tds),
                    number_format($net),
                ]);
            }

            /** Vendor TOTAL */
            $rows->push([
                '',
                '',
                '',
                '',
                'Total',
                '',
                number_format($vendorGross),
                '',
                number_format($vendorTds),
                number_format($vendorNet),
            ]);

            /** Blank line */
            $rows->push(['','','','','','','','','','']);
        }

        return $rows;
    }

    public function headings(): array
    {
        return [
            'S.No','Month','ID','Name','Branch','Designation',
            'Gross Prof fee','Gross Earnings','TDS 10%','Net Prof fee'
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
                $lastRow = $sheet->getHighestRow();

                /** HEADER */
                $sheet->getStyle('A1:J1')->applyFromArray([
                    'font' => ['bold' => true],
                    'fill' => [
                        'fillType' => 'solid',
                        'color' => ['rgb' => 'F4B084']
                    ],
                    'alignment' => ['horizontal' => 'center']
                ]);

                for ($row = 2; $row <= $lastRow; $row++) {

                    /** ✅ BLANK ROW → NO STYLE */
                    if (
                        trim($sheet->getCell("A{$row}")->getValue()) === '' &&
                        trim($sheet->getCell("E{$row}")->getValue()) === ''
                    ) {
                        continue;
                    }

                    /** ✅ TOTAL ROW → OLD TOTAL STYLE */
                    if ($sheet->getCell("E{$row}")->getValue() === 'Total') {

                        $sheet->getStyle("E{$row}:F{$row}")->applyFromArray([
                            'font' => ['bold' => true],
                            'fill' => [
                                'fillType' => 'solid',
                                'color' => ['rgb' => 'DDDAD0']
                            ]
                        ]);

                        foreach (['G','I','J'] as $col) {
                            $sheet->getStyle("{$col}{$row}")->applyFromArray([
                                'font' => ['bold' => true],
                                'fill' => [
                                    'fillType' => 'solid',
                                    'color' => ['rgb' => 'FFDE63']
                                ]
                            ]);
                        }

                        continue;
                    }

                    /** DATA ROW COLORS */
                    $sheet->getStyle("G{$row}")->applyFromArray([
                        'fill' => ['fillType' => 'solid','color' => ['rgb' => 'FFF58A']]
                    ]);
                    $sheet->getStyle("H{$row}")->applyFromArray([
                        'fill' => ['fillType' => 'solid','color' => ['rgb' => 'B4DEBD']]
                    ]);
                    $sheet->getStyle("J{$row}")->applyFromArray([
                        'fill' => ['fillType' => 'solid','color' => ['rgb' => 'A3CCDA']]
                    ]);
                }

                /** AUTO SIZE */
                foreach (range('A','J') as $col) {
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                }
            }
        ];
    }
}
