<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use Illuminate\Support\Facades\DB;

class PurchaseTemplateExport implements FromArray, WithHeadings, WithEvents
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
            'purchase_no', 'vendor_id', 'vendor_name', 'delivery_address',
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
                'PO-001', 'VEN-001', "Aravind's IVF", "Chennai", "PO-001",
                '01/08/2025', '01/08/2025', 'Due on Receipt', 'Purchase for testing',
                'Item A', 'Sales', 2, 100, 'Aravind', '', '', '', '', '', '', '', '',
            ],
            [
                'PO-001', 'VEN-001', "Aravind's IVF", "Chennai", "PO-001",
                '01/08/2025', '01/08/2025', 'Due on Receipt', 'Purchase for testing',
                'Item A', 'Sales', 2, 100, 'Aravind', '', '', '', '', '', '', '', '',
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                $this->applyDropdown($sheet, 'K', $this->accountList);
                $this->applyDropdown($sheet, 'O', $this->gstList);
                $this->applyDropdown($sheet, 'P', $this->tdsList);
                $this->applyDropdown($sheet, 'Q', $this->discountType);
                $this->applyDropdown($sheet, 'T', $this->zoneList);
                $this->applyDropdown($sheet, 'U', $this->branchList);
                $this->applyDropdown($sheet, 'V', $this->companyList);
            },
        ];
    }

   private function applyDropdown($sheet, $column, $options)
{
    if (empty($options)) {
        return;
    }

    // Create a temporary sheet for the dropdown values
    $spreadsheet = $sheet->getParent();
    $helperSheet = $spreadsheet->createSheet();
    $helperSheet->setTitle('Temp_' . $column);

    // Add options to helper sheet
    foreach ($options as $index => $option) {
        $helperSheet->setCellValue('A' . ($index + 1), $option);
    }

    // Create named range
    $range = 'Temp_' . $column . '!$A$1:$A$' . count($options);
    $spreadsheet->addNamedRange(
        new \PhpOffice\PhpSpreadsheet\NamedRange(
            'Options_' . $column,
            $helperSheet,
            $range
        )
    );

    // Apply validation using named range
    for ($row = 2; $row <= 100; $row++) {
        $cell = $sheet->getCell($column . $row);
        $validation = $cell->getDataValidation();

        if (!$validation) {
            $validation = new DataValidation();
            $cell->setDataValidation($validation);
        }

        $validation->setType(DataValidation::TYPE_LIST);
        $validation->setErrorStyle(DataValidation::STYLE_INFORMATION);
        $validation->setAllowBlank(true);
        $validation->setShowInputMessage(true);
        $validation->setShowErrorMessage(true);
        $validation->setShowDropDown(true);
        $validation->setFormula1('Options_' . $column);
    }

    // Hide the helper sheet
    $helperSheet->setSheetState(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet::SHEETSTATE_HIDDEN);
}
}

