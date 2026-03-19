<?php

namespace App\Exports;

use App\Models\Tblbill;
use App\Models\Tbltdstax;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class TdsReportExport implements FromCollection, WithHeadings, WithEvents
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
        $query = Tblbill::with(['BillLines','Tblvendor','TblBilling','Tblbankdetails'])
            ->orderBy('id', 'desc')
            ->where('tds_paid_status','Paid')->where('delete_status',0)
            ->where('tax_amount', '>', 0);

        // Apply filters similar to your gettdssummary function
        $request = $this->request;

        if ($request->filled('section_id')) {
            $tdsids = Tbltdstax::where('section_id', $request->section_id)->pluck('id')->toArray();
        }

        if (!empty($tdsids)) {
            $query->whereIn('tds_tax_id', $tdsids);
        }

        // Apply filters
        if ($request->filled('date_from') && $request->filled('date_to')) {
            $from = Carbon::createFromFormat('d/m/Y', $request->date_from)->startOfDay();
            $to   = Carbon::createFromFormat('d/m/Y', $request->date_to)->endOfDay();

            $query->whereRaw("
                STR_TO_DATE(bill_date, '%d/%m/%Y') BETWEEN ? AND ? ", [$from, $to]);
        }

        if($request->filled('state_name')){
            $state_name=$request->state_name;
            if ($state_name === 'Tamil Nadu') {
                $ids = ['2','4','6','7','8','9'];
                $query->whereIn('zone_id', $ids);
            } elseif($state_name === 'Karnataka') {
                $ids = ['3'];
                $query->whereIn('zone_id', $ids);
            } elseif($state_name === 'Kerala') {
                $ids = ['5'];
                $query->whereIn('zone_id', $ids);
            } elseif($state_name === 'International') {
                $ids = ['10'];
                $query->whereIn('zone_id', $ids);
            } elseif($state_name === 'Andra Pradesh') {
                $ids = ['30'];
                $query->whereIn('branch_id', $ids);
            }
        }

        if ($request->filled('zone_id')) {
            $ids = explode(',', $request->zone_id);
            $query->whereIn('zone_id', $ids);
        }

        if ($request->filled('branch_id')) {
            $ids = explode(',', $request->branch_id);
            $query->whereIn('branch_id', $ids);
        }

        if ($request->filled('company_id')) {
            $ids = explode(',', $request->company_id);
            $query->whereIn('company_id', $ids);
        }

        if ($request->filled('vendor_id')) {
            $ids = explode(',', $request->vendor_id);
            $query->whereIn('vendor_id', $ids);
        }

        if ($request->filled('bill_ids')) {
            if(!empty($request->bill_ids)){
                $query->whereIn('id', explode(',', $request->bill_ids));
            }
        }

        if ($request->filled('financial_name')) {
            $financialYears = array_filter(explode(',', $request->financial_name));
            $query->where(function ($query) use ($financialYears) {
                foreach ($financialYears as $fy) {
                    [$startYear, $endYear] = explode('-', $fy);

                    $fyStart = "$startYear-04-01";
                    $fyEnd   = "$endYear-03-31";
                    $query->orWhereBetween(
                        DB::raw("STR_TO_DATE(bill_date, '%d/%m/%Y')"),
                        [$fyStart, $fyEnd]
                    );
                }
            });
        }

        if ($request->filled('quarter_name')) {
            $quarterIds = array_filter(explode(',', $request->quarter_name));
            $query->whereIn('tds_quarter', $quarterIds);
        }

        return $query->get()->map(function ($bill) {
            return [
                'Date' => $bill->bill_date,
                'Bill Gerate No' => $bill->bill_gen_number,
                'Bill No' => $bill->bill_number,
                'Zone' => $bill->zone_name,
                'Branch' => $bill->branch_name,
                'Vendor Name' => $bill->vendor_name,
                'Vendor Pan' => $bill->Tblvendor->pan_number,
                'Invoice Amount' => $bill->grand_total_amount,
                'TDS Amount' => $bill->tax_amount,
                'Paid Date' => $bill->tds_pay_date,
                'Portal Paid Date' => '',
                'Challan No' => $bill->tds_challan_no,
                'Section' => optional(optional($bill->TblTDSsection)->section)->name,
                'Quarter' => $bill->tds_quarter,
                'Status' => ucfirst($bill->tds_paid_status),
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Date',
            'Bill Gerate No',
            'Bill No',
            'Zone',
            'Branch',
            'Vendor Name',
            'Vendor Pan',
            'Invoice Amount',
            'TDS Amount',
            'Paid Date',
            'Portal Paid Date',
            'Challan No',
            'Section',
            'Quarter',
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
                $sheet->getStyle('A1:O1')->applyFromArray([
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
                    $sheet->getStyle("A1:O{$highestRow}")->applyFromArray([
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => 'thin'
                            ]
                        ]
                    ]);
                }

                /** Auto size columns */
                foreach (range('A', 'O') as $col) {
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                }

                /** Optional: Format numeric columns */
                if ($highestRow > 1) {
                    // Columns H and I (Invoice Amount and TDS Amount)
                    $sheet->getStyle("H2:I{$highestRow}")->getNumberFormat()->setFormatCode('#,##0.00');

                    /** Optional: Apply background colors to important columns */
                    $sheet->getStyle("H2:H{$highestRow}")->applyFromArray([
                        'fill' => [
                            'fillType' => 'solid',
                            'color' => ['rgb' => 'FFF58A'] // Yellow for Invoice Amount
                        ]
                    ]);

                    $sheet->getStyle("I2:I{$highestRow}")->applyFromArray([
                        'fill' => [
                            'fillType' => 'solid',
                            'color' => ['rgb' => 'B4DEBD'] // Green for TDS Amount
                        ]
                    ]);

                    /** Optional: Highlight the Status column */
                    $sheet->getStyle("O2:O{$highestRow}")->applyFromArray([
                        'fill' => [
                            'fillType' => 'solid',
                            'color' => ['rgb' => 'A3CCDA'] // Blue for Status (matches your TDS export)
                        ]
                    ]);
                }

                /** Optional: Center align specific columns */
                $sheet->getStyle("A2:O{$highestRow}")->getAlignment()->setHorizontal('left');
                $sheet->getStyle("H2:I{$highestRow}")->getAlignment()->setHorizontal('right');
                $sheet->getStyle("O2:O{$highestRow}")->getAlignment()->setHorizontal('center');
            }
        ];
    }
}