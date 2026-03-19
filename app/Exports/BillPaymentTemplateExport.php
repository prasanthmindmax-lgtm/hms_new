<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use Illuminate\Support\Facades\DB;

class BillPaymentTemplateExport implements FromArray, WithHeadings, WithEvents
{
    protected $gstList;
    protected $tdsList;
    protected $ModeType;
    protected $zoneList;
    protected $branchList;
    protected $companyList;
    protected $accountList;

    public function __construct()
    {
        $this->zoneList = DB::table('tblzones')->pluck('name')->toArray();
        $this->branchList = DB::table('tbl_locations')->pluck('name')->toArray();
        $this->companyList = DB::table('company_tbl')->pluck('company_name')->toArray();
        $this->ModeType = ['Cash', 'Bank Transfer','Cheque','Credit Card','UPI'];
    }

    public function headings(): array
    {
        return [
            'Bill No', 'Vendor Id', 'Vendor Name', 'Payment Date',
            'Paid Through', 'Payment Made Date', 'Payment Amount', 'Zone',
            'Branch', 'Company',
        ];
    }

    public function array(): array
    {
        return [
            [
                'BILL-001', 'VEN-001', "Aravind's IVF", "01/08/2025", "Cash", '01/08/2025', '10000', '', '', '',
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $spreadsheet = $sheet->getParent();

                // Create all dropdowns with named ranges
                $this->createSearchableDropdown($spreadsheet, 'Account', $this->accountList);
                $this->createSearchableDropdown($spreadsheet, 'GST', $this->gstList);
                $this->createSearchableDropdown($spreadsheet, 'TDS', $this->tdsList);
                $this->createSearchableDropdown($spreadsheet, 'ModeType', $this->ModeType);
                $this->createSearchableDropdown($spreadsheet, 'Zone', $this->zoneList);
                $this->createSearchableDropdown($spreadsheet, 'Branch', $this->branchList);
                $this->createSearchableDropdown($spreadsheet, 'Company', $this->companyList);

                // Apply dropdowns to columns
                // $this->applyDropdownToColumn($sheet, 'K', 'Account'); // Account
                // $this->applyDropdownToColumn($sheet, 'O', 'GST'); // GST
                // $this->applyDropdownToColumn($sheet, 'P', 'TDS'); // TDS
                // $this->applyDropdownToColumn($sheet, 'Q', 'DiscountType'); // Discount Type
                // $this->applyDropdownToColumn($sheet, 'S', 'DiscountType'); // Discount Type
                // $this->applyDropdownToColumn($sheet, 'U', 'DiscountType'); // Discount Type
                $this->applyDropdownToColumn($sheet, 'E', 'ModeType'); // Discount Type
                $this->applyDropdownToColumn($sheet, 'H', 'Zone'); // Zone
                $this->applyDropdownToColumn($sheet, 'I', 'Branch'); // Branch
                $this->applyDropdownToColumn($sheet, 'J', 'Company'); // Company

                // Auto-size columns for better visibility
                foreach (range('A', 'V') as $col) {
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                }

                // Freeze the first row (headers)
                $sheet->freezePane('A2');
            },
        ];
    }

    private function createSearchableDropdown($spreadsheet, $name, $options)
    {
        if (empty($options)) {
            return;
        }

        // Create a temporary sheet for dropdown values
        $helperSheet = $spreadsheet->createSheet();
        $helperSheet->setTitle('Data_' . $name);

        // Add options to helper sheet
        foreach ($options as $index => $option) {
            $helperSheet->setCellValue('A' . ($index + 1), $option);
        }

        // Create named range
        $range = 'Data_' . $name . '!$A$1:$A$' . count($options);
        $spreadsheet->addNamedRange(
            new \PhpOffice\PhpSpreadsheet\NamedRange(
                $name . '_List',
                $helperSheet,
                $range
            )
        );

        // Hide the helper sheet
        $helperSheet->setSheetState(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet::SHEETSTATE_HIDDEN);
    }

    private function applyDropdownToColumn($sheet, $column, $listName)
    {
        // Apply to first 1000 rows (you can adjust this number)
        for ($row = 2; $row <= 1000; $row++) {
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
            $validation->setFormula1('=' . $listName . '_List');

            // Set helpful messages
            $validation->setPromptTitle('Select ' . $listName);
            $validation->setPrompt('Please select from the dropdown list. You can type to search.');
            $validation->setErrorTitle('Invalid input');
            $validation->setError('Value must be from the dropdown list.');
        }
    }
}