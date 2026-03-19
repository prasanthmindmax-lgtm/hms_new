<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;

class QuotationTemplateExport implements FromArray, WithHeadings, WithEvents
{
    protected $gstList;
    protected $tdsList;
    protected $discountType;
    protected $zoneList;
    protected $branchList;
    protected $companyList;
    protected $accountList;

    public function __construct()
    {
        $this->gstList = DB::table('gst_tax_tbl')
            ->select(DB::raw("CONCAT(tax_name, ' - ', tax_rate) as name"))
            ->pluck('name')->toArray();

        $this->tdsList = DB::table('tds_tax_tbl')
            ->select(DB::raw("CONCAT(tax_name, ' - ', tax_rate) as name"))
            ->pluck('name')->toArray();

        $this->zoneList = DB::table('tblzones')->pluck('name')->toArray();
        $this->branchList = DB::table('tbl_locations')->pluck('name')->toArray();
        $this->companyList = DB::table('company_tbl')->pluck('company_name')->toArray();
        $this->accountList = DB::table('account_tbl')->pluck('name')->toArray();

        $this->discountType = ['percent', 'money'];
    }

    public function headings(): array
    {
        return [
            'quotation_no', 'vendor_id', 'vendor_name', 'delivery_address',
            'order_number', 'bill_date', 'due_date', 'payment_terms',
            'subject', 'item_details', 'account', 'quantity', 'rate',
            'customer', 'GST', 'TDS', 'Discount Type', 'Discount',
            'Adjustment', 'Zone', 'Branch', 'Company',
        ];
    }

    public function array(): array
    {
        return [
            [
                'QO-001', 'VEN-001', "Aravind's IVF", "Chennai", "PO-001",
                '01/08/2025', '01/08/2025', 'Due on Receipt', 'Quotation for testing',
                'Item A', 'Sales', 2, 100, 'Aravind', '', '', '', '', '', '', '', '',
            ],
            [
                'QO-002', 'VEN-002', "Test Vendor", "Bangalore", "PO-002",
                '02/08/2025', '02/08/2025', 'Net 30', 'Quotation for dummy',
                'Item B', 'Purchase', 5, 200, 'Test Customer', '', '', '', '', '', '', '', '',
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Example: Dropdown for GST column (O)
                $lastRow = 100; // For example, first 100 rows
                for ($row = 2; $row <= $lastRow; $row++) {
                    $validation = $sheet->getCell("O$row")->getDataValidation();
                    $validation->setType(DataValidation::TYPE_LIST);
                    $validation->setErrorStyle(DataValidation::STYLE_INFORMATION);
                    $validation->setAllowBlank(true);
                    $validation->setShowInputMessage(true);
                    $validation->setShowErrorMessage(true);
                    $validation->setFormula1('"' . implode(',', $this->gstList) . '"');
                }

                // Similar code can be added for TDS (P), Discount Type (Q), Zone (T), Branch (U), Company (V), Account (K)
            },
        ];
    }
}
