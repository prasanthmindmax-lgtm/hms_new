<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class IncomeTemplateExport implements FromArray, WithHeadings, WithEvents
{

    public function headings(): array
    {
        return [

            // ---------------- ROW 1 (Main Header) ----------------
            [
                'Sl.No','Zone','Branch','Date',

                'Cash','','','','','','','',
                'Card','','','','','',
                'UPI','','','','','',
                'Bank Statement','','',
                'NEFT','','','','',
            ],

            // ---------------- ROW 2 (Sub Header) ----------------
            [
                'Sl.No','Zone','Branch','Date',

                'Moc doc','Radiant Date','Date fetch amt','Radiant','Diff','BS','Diff','Remark',

                'Moc doc','Orange Date','Date fetch amt','Orange','Diff','Remark',

                'Moc doc' ,'Orange Date','Date fetch amt','Orange','Diff','Remark',

                'Charges','Amount','Diff',

                'Moc doc','BS Date','Date fetch amt','BS','Diff','Remark'
            ]
        ];
    }


    public function array(): array
    {
        return [
            // SAMPLE ROW
            [
                1,'TN CHENNAI','Chennai - Madipakkam','02-11-2025',

                12000,'01/11/2025 - 01/11/2025',11800,11800,200,5000,-200,'',

                3000,'01/11/2025 - 01/11/2025',3000,2950,150,'',

                1500,'01/11/2025 - 01/11/2025',1500,1450,150,'',

                50,20000,100,

                3000,'01/11/2025 - 01/11/2025',100,50,50,'',
            ]
        ];
    }


    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {

                $sheet = $event->sheet->getDelegate();

                // ---------- MERGE MAIN HEADERS ----------
                $sheet->mergeCells('A1:A2'); // Sl.No
                $sheet->mergeCells('B1:B2'); // Zone
                $sheet->mergeCells('C1:C2'); // Branch
                $sheet->mergeCells('D1:D2'); // Date

                $sheet->mergeCells('E1:L1'); // Cash
                $sheet->mergeCells('M1:R1'); // Card
                $sheet->mergeCells('S1:X1'); // UPI
                $sheet->mergeCells('Y1:AA1'); // Bank Statement
                $sheet->mergeCells('AB1:AG1'); // NEFT

                // ---------- STYLE ----------
                $sheet->getStyle('A1:AG2')->getAlignment()->setHorizontal('center')->setVertical('center');
                $sheet->getStyle('A1:AG2')->getFont()->setBold(true);

                // background colors
                $sheet->getStyle('E1:L1')->getFill()
                    ->setFillType('solid')->getStartColor()->setARGB('CCFFCC'); // Cash light green

                $sheet->getStyle('M1:R1')->getFill()
                    ->setFillType('solid')->getStartColor()->setARGB('FFFF99'); // Card yellow

                $sheet->getStyle('S1:X1')->getFill()
                    ->setFillType('solid')->getStartColor()->setARGB('CCFFFF'); // UPI blue

                $sheet->getStyle('Y1:AA1')->getFill()
                    ->setFillType('solid')->getStartColor()->setARGB('FFFFCC'); // Bank

                $sheet->getStyle('AB1:AG1')->getFill()
                    ->setFillType('solid')->getStartColor()->setARGB('CCFF99'); // NEFT

                // border for whole table
                $sheet->getStyle('A1:AG2')->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
                        ]
                    ]
                ]);

            }
        ];
    }
}
