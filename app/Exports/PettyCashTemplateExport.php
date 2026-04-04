<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

/**
 * Excel template for petty cash bulk import (same pattern as QuotationTemplateExport).
 *
 * Rows with the same `report_group` become one petty cash header + multiple line items.
 * Leave `report_group` empty to import each row as its own petty cash (single line).
 */
class PettyCashTemplateExport implements FromArray, WithHeadings
{
    public function headings(): array
    {
        return [
            'report_group',
            'report_id',
            'report_name',
            'expense_date',
            'vendor_id',
            'vendor_name',
            'zone',
            'branch',
            'company',
            'header_expense_category',
            'currency',
            'reference_no',
            'claim_reimbursement',
            'status',
            'notes',
            'item_description',
            'expense_category',
            'line_amount',
        ];
    }

    public function array(): array
    {
        return [
            [
                'PC-001',
                '',
                'March office expenses',
                '15/03/2026',
                'VEN-001',
                'Sample Vendor',
                'TN CENTRAL',
                'Harur',
                'DR A5 PHARMA PVT LTD',
                'Travel',
                'INR',
                'REF-IMP-001',
                '0',
                'pending',
                'Template example row 1',
                'Local conveyance',
                'Travel',
                '500.00',
            ],
            [
                'PC-001',
                '',
                'March office expenses',
                '15/03/2026',
                'VEN-001',
                'Sample Vendor',
                'TN CENTRAL',
                'Harur',
                'DR A5 PHARMA PVT LTD',
                'Travel',
                'INR',
                'REF-IMP-001',
                '0',
                'pending',
                '',
                'Team lunch',
                'Food',
                '1200.00',
            ],
            [
                '',
                '',
                'Single-line import example',
                '16/03/2026',
                'VEN-001',
                'Sample Vendor',
                'TN CENTRAL',
                'Kallakurichi',
                'DR A5 PHARMA PVT LTD',
                'Office supplies',
                'INR',
                '',
                '0',
                'pending',
                '',
                'Stationery',
                'Office supplies',
                '350.00',
            ],
        ];
    }
}
