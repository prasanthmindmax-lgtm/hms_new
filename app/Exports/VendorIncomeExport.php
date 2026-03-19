<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class VendorIncomeExport implements FromCollection, WithHeadings, WithMapping, WithEvents
{
    protected $data;

    public function __construct(Collection $data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        return $this->data;
    }

    public function headings(): array
    {
        return [
            'Location',
            'Cash', 'Card', 'Cheque', 'DD', 'NEFT', 'Credit', 'UPI', 'Total'
        ];
    }

 public function map($row): array
{
    return [
        $row->location_name,
        number_format($row->cash, 2, '.', ','),
        number_format($row->card, 2, '.', ','),
        number_format($row->cheque, 2, '.', ','),
        number_format($row->dd, 2, '.', ','),
        number_format($row->neft, 2, '.', ','),
        number_format($row->credit, 2, '.', ','),
        number_format($row->upi, 2, '.', ','),
        number_format($row->total, 2, '.', ','),
    ];
}




    // 🔥 Add Grand Total as last row
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {

                $sheet = $event->sheet->getDelegate();
                $lastRow = $sheet->getHighestRow() + 1;

                // Calculate final total
                $grandTotal = $this->data->sum('total');

                // Insert "Grand Total" row
                $sheet->setCellValue('A' . $lastRow, 'Grand Total');
                $sheet->setCellValue('I' . $lastRow, $grandTotal); // Column I = Total Column

                // Apply styling
                $sheet->getStyle('A' . $lastRow . ':I' . $lastRow)->getFont()->setBold(true);
                $sheet->getStyle('A' . $lastRow)->getFont()->getColor()->setARGB('FFFF0000'); // Red text Grand Total
                $sheet->getStyle('I' . $lastRow)->getFont()->getColor()->setARGB('FFFF0000'); // Red text total
            }
        ];
    }
}
