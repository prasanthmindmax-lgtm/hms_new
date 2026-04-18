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
            'Cash', 'Card', 'Cheque', 'DD', 'NEFT', 'Credit', 'UPI',
            'Discount', 'Cancel', 'Refund', 'Total (payments only)',
        ];
    }

    public function map($row): array
    {
        $cash = (float) ($row->cash ?? 0);
        $card = (float) ($row->card ?? 0);
        $cheque = (float) ($row->cheque ?? 0);
        $dd = (float) ($row->dd ?? 0);
        $neft = (float) ($row->neft ?? 0);
        $credit = (float) ($row->credit ?? 0);
        $upi = (float) ($row->upi ?? 0);
        $lineTotal = $cash + $card + $cheque + $dd + $neft + $credit + $upi;

        return [
            $row->location_name,
            number_format($cash, 2, '.', ','),
            number_format($card, 2, '.', ','),
            number_format($cheque, 2, '.', ','),
            number_format($dd, 2, '.', ','),
            number_format($neft, 2, '.', ','),
            number_format($credit, 2, '.', ','),
            number_format($upi, 2, '.', ','),
            number_format((float) ($row->discount ?? 0), 2, '.', ','),
            number_format((float) ($row->cancel_amt ?? 0), 2, '.', ','),
            number_format((float) ($row->refund_amt ?? 0), 2, '.', ','),
            number_format($lineTotal, 2, '.', ','),
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $lastRow = $sheet->getHighestRow() + 1;

                $grandTotal = $this->data->sum(function ($row) {
                    return (float) ($row->cash ?? 0) + (float) ($row->card ?? 0) + (float) ($row->cheque ?? 0)
                        + (float) ($row->dd ?? 0) + (float) ($row->neft ?? 0) + (float) ($row->credit ?? 0)
                        + (float) ($row->upi ?? 0);
                });

                $sheet->setCellValue('A' . $lastRow, 'Grand Total');
                $sheet->setCellValue('L' . $lastRow, $grandTotal);

                $sheet->getStyle('A' . $lastRow . ':L' . $lastRow)->getFont()->setBold(true);
                $sheet->getStyle('A' . $lastRow)->getFont()->getColor()->setARGB('FFFF0000');
                $sheet->getStyle('L' . $lastRow)->getFont()->getColor()->setARGB('FFFF0000');
            },
        ];
    }
}
