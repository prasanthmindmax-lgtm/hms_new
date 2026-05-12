<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PharmacyAuditImportTemplate implements FromArray, WithHeadings
{
    public function headings(): array
    {
        return [
            'Company',
            'Zone',
            'Branch',
            'Audit Date',
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

    public function array(): array
    {
        return [
            [
                'Sample Company',
                'Sample Zone',
                'Sample Branch',
                '2026-04-18',
                '3 ML SYRINGE (DISPOSABLE)',
                '26A12K33',
                '2030-12',
                15.47,
                152,
                150,
                -2,
                -30.94,
            ],
        ];
    }
}
