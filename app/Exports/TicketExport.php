<?php

namespace App\Exports;

use App\Models\Ticket;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class TicketExport implements FromCollection, WithHeadings, WithEvents
{
    protected Collection $tickets;

    public function __construct(Collection $tickets)
    {
        $this->tickets = $tickets;
    }

    public function collection(): Collection
    {
        return $this->tickets->map(function (Ticket $t) {
            $statusLabel = $t->status
                ? ucwords(str_replace('_', ' ', (string) $t->status))
                : '';

            $isClosed = $t->status === 'closed';
            $slaSummary = $t->slaVersusActualSummary();

            return collect([
                $t->ticket_no ?? '',
                $t->location->name ?? '',
                $t->fromDepartment->name ?? '',
                $t->toDepartment->name ?? '',
                $t->category->name ?? '',
                $t->subject ?? '',
                (string) ($t->description ?? ''),
                $isClosed ? $t->closed_solution : '',
                $t->created_at?->format('d M Y') ?? '',
                $isClosed ? $t->status_updated_at?->format('d M Y') : '',
                $t->created_at?->format('h:i A') ?? '',
                $isClosed ? $t->status_updated_at?->format('h:i A') : '',
                $isClosed ? $t->timeToCloseDisplay() : '',
                $isClosed ? $slaSummary['text'] : '',
                $statusLabel,
                $t->priority ?? '',
                $t->creator->user_fullname ?? '',
                $isClosed ? ($t->statusUpdater->user_fullname ?? '') : '',
            ]);
        });
    }

    public function headings(): array
    {
        return [
            'Ticket No',
            'Location',
            'From department',
            'To department',
            'Category',
            'Subject',
            'Description',
            'Solution',
            'Raised Date',
            'Closed Date',
            'Raised Time',
            'Closed Time',
            'TAT',
            'SLA VS ACTUAL',
            'Status',
            'Priority',
            'Raised by',
            'Closed by',
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $lastRow = $sheet->getHighestRow();
                $lastCol = 'R';

                $sheet->getStyle('A1:' . $lastCol . '1')->applyFromArray([
                    'font' => ['bold' => true],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'color' => ['rgb' => 'E0E7FF'],
                    ],
                    'borders' => [
                        'allBorders' => ['borderStyle' => Border::BORDER_THIN],
                    ],
                ]);

                $sheet->getStyle('A1:' . $lastCol . $lastRow)->applyFromArray([
                    'borders' => [
                        'allBorders' => ['borderStyle' => Border::BORDER_THIN],
                    ],
                ]);

                foreach (range('A', $lastCol) as $col) {
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                }
            },
        ];
    }
}
