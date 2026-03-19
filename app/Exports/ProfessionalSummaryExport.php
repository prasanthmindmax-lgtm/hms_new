<?php

namespace App\Exports;

use App\Models\Tblbill;
use App\Models\Tblaccount;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class ProfessionalSummaryExport implements FromCollection, WithHeadings, WithEvents
{
    protected $request;

    public function __construct($request)
    {
        $this->request = $request;
    }

    public function collection()
    {
        $professionalAccountNames = [
            'PROFESSIONAL FEES STAFF',
            'PROFESSIONAL FEES - DOCTORS',
            'PROFESSIONAL FEES ADVOCATE',
            'STAFF SALARY',
            'PROFESSIONAL FEES'
        ];

        $accountsids = Tblaccount::whereIn('name', $professionalAccountNames)->pluck('id');

        $query = Tblbill::with(['BillLines','Tblvendor'])
            ->whereHas('BillLines', function ($q) use ($accountsids) {
                $q->whereIn('account_id', $accountsids);
            })->where('delete_status',0)
            ->orderBy('id', 'desc');

        $request = $this->request;

        // SAME FILTERS LIKE view page
        if ($request->filled('date_from') && $request->filled('date_to')) {
            $from = Carbon::createFromFormat('d/m/Y', $request->date_from)->startOfDay();
            $to = Carbon::createFromFormat('d/m/Y', $request->date_to)->endOfDay();
            $query->whereBetween('created_at', [$from, $to]);
        }
        if ($request->filled('vendor_id')) {
            $ids = explode(',', $request->vendor_id);
            $query->whereIn('vendor_id', $ids);
        }
        if ($request->filled('company_id')) {
            $ids = explode(',', $request->company_id);
            $query->whereIn('company_id', $ids);
        }
        if ($request->filled('zone_id')) {
            $ids = explode(',', $request->zone_id);
            $query->whereIn('zone_id', $ids);
        }
        if ($request->filled('branch_id')) {
            $ids = explode(',', $request->branch_id);
            $query->whereIn('branch_id', $ids);
        }
        if ($request->filled('nature_id')) {
            $ids = explode(',', $request->nature_id);

            $query->whereHas('BillLines', function ($q) use ($ids) {
                $q->whereIn('account_id', $ids);
            });
        }
        if ($request->filled('bill_ids')) {
            if(!empty($request->bill_ids)){
                $query->whereIn('id', explode(',', $request->bill_ids));
            }
        }

        return $query->get()->map(function ($bill) {
            $accountName = optional($bill->BillLines->first())->account ?? null;
            return [
                'Date'                  => $bill->bill_date,
                'Zone'                  => $bill->zone_name,
                'Branch'                => $bill->branch_name,
                'Bill Gen No'           => $bill->bill_gen_number,
                'Bill No'               => $bill->bill_number,
                'Vendor ID'             => optional($bill->Tblvendor)->vendor_id,
                'Vendor Name'           => $bill->vendor_name,
                'Account Name'          => $accountName,
                'Due Date'              => $bill->due_date,
                'Invoice Amount'        => $bill->sub_total_amount,
                'Final Invoice Amount'  => $bill->grand_total_amount,
                'Status'                => ucfirst($bill->bill_status),
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Date',
            'Zone',
            'Branch',
            'Bill Gen No',
            'Bill No',
            'Vendor ID',
            'Vendor Name',
            'Account Name',
            'Due Date',
            'Invoice Amount',
            'Final Invoice Amount',
            'Status',
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet;
                $highestRow = $sheet->getHighestRow();

                /** Header style - same color as other exports */
                $sheet->getStyle('A1:L1')->applyFromArray([
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
                    $sheet->getStyle("A1:L{$highestRow}")->applyFromArray([
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => 'thin'
                            ]
                        ]
                    ]);
                }

                /** Auto size columns */
                foreach (range('A', 'L') as $col) {
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                }

                /** Optional: Format numeric columns (Invoice Amount and Final Invoice Amount) */
                if ($highestRow > 1) {
                    // Columns J and K (10th and 11th columns)
                    $sheet->getStyle("J2:K{$highestRow}")->getNumberFormat()->setFormatCode('#,##0.00');

                    /** Optional: Apply background colors to numeric columns like in TdsDetailedExport */
                    $sheet->getStyle("J2:J{$highestRow}")->applyFromArray([
                        'fill' => [
                            'fillType' => 'solid',
                            'color' => ['rgb' => 'FFF58A'] // Yellow for Invoice Amount
                        ]
                    ]);

                    $sheet->getStyle("K2:K{$highestRow}")->applyFromArray([
                        'fill' => [
                            'fillType' => 'solid',
                            'color' => ['rgb' => 'B4DEBD'] // Green for Final Invoice Amount
                        ]
                    ]);
                }
            }
        ];
    }
}