<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class IncomeTemplateExportnew implements FromArray, WithHeadings, WithEvents
{

    public function headings(): array
    {
        return [

            [
                'Serial','Zone','Location','Date',

                'MOC DOC','','','','','','',

                'Radiant cash collection','',

                'Cash Bank Statement','','',

                'MESPOS ORANGE','',

                'BANK Deposit','','','','','','','',

                'DIFFERENCE','','','',
            ],
            [
                '','','','',
                'Cash','Card','UPI','Total card/Upi','NEFT','Others','TOTAL',

                'Date of collection','Collection Amount',
                
                'Date of Deposit','Deposit amount','UTR/Transcation Ids',

                'Card','UPI',

                'Date of Settlement','Bank Charges UPI/Card','UPI / CARD','UPI/CARD UTR NO','NEFT','NEFT UTR NO','OTHERS','OTHERS UTR NO',

                'Radiant collection','Cash Deposit','UPI CARD BANK CHARGES','Others / NEFT','Remarks',
            ]
        ];
    }


    public function array(): array
    {
        return [
            // SAMPLE ROW
            [
                1,'TN CHENNAI','Chennai - Madipakkam','02-11-2025',

                20645,9344,104653,113997,'0','0',134642,

                '02-01-2026',57905,
                
                '02-01-2026',57905,'SBIN180232301234',

                9344,104653,

                '02-01-2026',59,113938,'SBIN180232301234','0','SBIN180232301234','0','SBIN180232301234',

                -37260,'0','0','0',"remark",
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

                $sheet->mergeCells('E1:K1'); // Cash
                $sheet->mergeCells('L1:M1'); // Card
                $sheet->mergeCells('N1:P1'); // Card
                $sheet->mergeCells('Q1:R1'); // UPI
                $sheet->mergeCells('S1:Z1'); // Bank Statement
                $sheet->mergeCells('AA1:AE1'); // NEFT

                // ---------- STYLE ----------
                $sheet->getStyle('A1:AG2')->getAlignment()->setHorizontal('center')->setVertical('center');
                $sheet->getStyle('A1:AG2')->getFont()->setBold(true);

                // background colors
                $sheet->getStyle('E1:K1')->getFill()
                    ->setFillType('solid')->getStartColor()->setARGB('CCFFCC'); // Cash light green

                $sheet->getStyle('L1:M1')->getFill()
                    ->setFillType('solid')->getStartColor()->setARGB('FFFF99'); // Card yellow

                $sheet->getStyle('N1:P1')->getFill()
                    ->setFillType('solid')->getStartColor()->setARGB('CCFFFF'); // UPI blue

                $sheet->getStyle('Q1:R1')->getFill()
                    ->setFillType('solid')->getStartColor()->setARGB('FFFFCC'); // Bank

                $sheet->getStyle('S1:Z1')->getFill()
                    ->setFillType('solid')->getStartColor()->setARGB('CCFF99'); // NEFT

                $sheet->getStyle('AA1:AE1')->getFill()
                    ->setFillType('solid')->getStartColor()->setARGB('CCFF99'); // NEFT

                // border for whole table
                $sheet->getStyle('A1:AA1')->applyFromArray([
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
