<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;

class ExpenseDetailsExport implements FromView
{
    protected $details, $type, $totalInvoiceAmount,$totalFinalAmount, $totalTDS, $totalGST;

    public function __construct($details, $type, $totalInvoiceAmount,$totalFinalAmount, $totalTDS, $totalGST)
    {
        $this->details = $details;
        $this->type = $type;
        $this->totalInvoiceAmount = $totalInvoiceAmount;
        $this->totalFinalAmount = $totalFinalAmount;
        $this->totalTDS = $totalTDS;
        $this->totalGST = $totalGST;
    }

    public function view(): View
    {
        return view('vendor.expense_details_excel', [
            'details' => $this->details,
            'type' => $this->type,
            'totalInvoiceAmount' => $this->totalInvoiceAmount,
            'totalFinalAmount' => $this->totalFinalAmount,
            'totalTDS' => $this->totalTDS,
            'totalGST' => $this->totalGST,
        ]);
    }
}
