<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PharmacyAuditExport implements FromCollection, WithHeadings, WithMapping
{
    public function __construct(
        protected Collection $rows
    ) {}

    public function collection(): Collection
    {
        return $this->rows;
    }

    public function headings(): array
    {
        return [
            'Audit Number',
            'Company',
            'Zone',
            'Branch',
            'Audit Date',
            'S.No',
            'Name',
            'Batch',
            'Expiry',
            'MRP',
            'System Quantity',
            'Manual Quantity',
            'Diff',
            'Val',
        ];
    }

    /**
     * @param  \App\Models\PharmacyAuditItem  $item
     */
    public function map($item): array
    {
        $a = $item->audit;

        return [
            $a?->audit_number,
            $a?->company?->company_name,
            $a?->zone?->name,
            $a?->branch?->name,
            $a?->audit_date?->format('Y-m-d'),
            $item->line_no,
            $item->item_name,
            $item->batch_no,
            $item->expiry,
            (float) $item->mrp,
            (int) $item->system_qty,
            (int) $item->manual_qty,
            (int) $item->diff_qty,
            (float) $item->val,
        ];
    }
}
