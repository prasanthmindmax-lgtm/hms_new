<?php

namespace App\Exports;

use App\Models\Asset;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class AssetExport implements FromCollection, WithHeadings, WithEvents
{
    protected Collection $rows;
    protected array $exportColumns;

    public function __construct(Collection $rows, array $columnDefinitions = [])
    {
        $this->rows = $rows;

        $this->exportColumns = array_values(array_filter(
            $columnDefinitions,
            static fn (array $c): bool => ($c['key'] ?? '') !== '' && ($c['key'] ?? '') !== 'action'
        ));
    }

    public function headings(): array
    {
        if ($this->exportColumns === []) {
            return [
                'S.No',
                'Category',
                'Company',
                'Zone',
                'Branch',
                'Department',
                'System model',
                'Monitor model',
                'Responsible person',
                'OS installed',
                'Processor',
                'SSD/HDD',
                'RAM',
                'Warranty',
                'Remarks',
            ];
        }

        return array_map(function (array $c) {
            $label = trim((string) ($c['label'] ?? ''));

            return $label === '' ? '' : Str::title(Str::lower($label));
        }, $this->exportColumns);
    }

    public function collection(): Collection
    {
        if ($this->exportColumns === []) {
            return $this->defaultCollection();
        }

        $i = 0;

        return $this->rows->map(function (Asset $a) use (&$i) {
            $i++;
            $cells = [];
            foreach ($this->exportColumns as $col) {
                $cells[] = $this->plainCell($a, (string) ($col['key'] ?? ''), $i);
            }

            return collect($cells);
        });
    }

    protected function defaultCollection(): Collection
    {
        $i = 0;

        return $this->rows->map(function (Asset $a) use (&$i) {
            $i++;

            return collect([
                $i,
                $a->category->name ?? '',
                (string) ($a->primaryCompany->company_name ?? ''),
                (string) ($a->primaryZone->name ?? ''),
                (string) ($a->primaryBranch->name ?? ''),
                $a->department?->name ?? '',
                $a->systemModelDisplay(),
                (string) ($a->typeAttr('monitor_model') ?? ''),
                $a->responsible_person ?? '',
                (string) ($a->typeAttr('os_installed') ?? ''),
                (string) ($a->typeAttr('processor') ?? ''),
                (string) ($a->typeAttr('ssd_hdd') ?? ''),
                (string) ($a->typeAttr('ram') ?? ''),
                $a->warranty_expiry ? $a->warranty_expiry->format('d/m/Y') : '',
                $this->sanitizeOneLine((string) ($a->remarks ?? '')),
            ]);
        });
    }

    protected function plainCell(Asset $a, string $key, int $rowNum): string
    {
        $raw = match ($key) {
            'sno' => (string) $rowNum,
            'category' => (string) ($a->category->name ?? ''),
            'company' => (string) ($a->primaryCompany->company_name ?? ''),
            'zone' => (string) ($a->primaryZone->name ?? ''),
            'branch' => (string) ($a->primaryBranch->name ?? ''),
            'department' => (string) ($a->department?->name ?? ''),
            'system_model' => $a->systemModelDisplay() ?: '',
            'monitor_model' => (string) ($a->typeAttr('monitor_model') ?? ''),
            'serial' => (string) ($a->serial_number ?? ''),
            'os_installed' => (string) ($a->typeAttr('os_installed') ?? ''),
            'processor' => (string) ($a->typeAttr('processor') ?? ''),
            'ssd_hdd' => (string) ($a->typeAttr('ssd_hdd') ?? ''),
            'ram' => (string) ($a->typeAttr('ram') ?? ''),
            'responsible' => (string) ($a->responsible_person ?? ''),
            'responsible_person' => (string) ($a->responsible_person ?? ''),
            'model' => (string) ($a->model ?? ''),
            'brand' => (string) ($a->typeAttr('brand') ?? ''),
            'ip_address' => (string) ($a->typeAttr('ip_address') ?? ''),
            'camera_name' => (string) ($a->typeAttr('camera_name') ?? ''),
            'dvr_name' => (string) ($a->typeAttr('dvr_name') ?? ''),
            'dvr_channel' => (string) ($a->typeAttr('dvr_channel') ?? ''),
            'device_username' => (string) ($a->typeAttr('device_username') ?? ''),
            'warranty' => $a->warranty_expiry ? $a->warranty_expiry->format('d/m/Y') : '',
            'remarks' => $this->sanitizeOneLine((string) ($a->remarks ?? '')),
            default => '',
        };

        return $this->sanitizeOneLine($raw);
    }

    protected function sanitizeOneLine(string $s): string
    {
        $s = str_replace(["\r\n", "\r", "\n"], ' ', $s);

        return trim($s);
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet   = $event->sheet->getDelegate();
                $lastRow = $sheet->getHighestRow();
                $lastCol = Coordinate::stringFromColumnIndex(max(1, count($this->headings())));

                $sheet->getStyle('A1:' . $lastCol . '1')->applyFromArray([
                    'font' => ['bold' => true],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'color'    => ['rgb' => 'E0E7FF'],
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

                $colCount = count($this->headings());
                for ($i = 1; $i <= $colCount; $i++) {
                    $col = Coordinate::stringFromColumnIndex($i);
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                }
            },
        ];
    }
}
