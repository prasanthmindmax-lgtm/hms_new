<?php
// Set base font
$pdf->SetFont('dejavusans', '', 10);
$pdf->SetTextColor(0, 0, 0); // black

$imagePath = str_replace('\\', '/', base_path('assets/images/dralogos.png'));
$pdf->Image($imagePath, 15, 10, 50, '', 'PNG', '', 'T', false, 300);

// Company Information (Top Left)
$pdf->SetFont('dejavusans', 'B', 10);
$pdf->SetXY(15, 30);
$pdf->Cell(0, 0, $quotation->TblCompany->company_name ?? "Aravind's IVF", 0, 1);
$pdf->SetFont('dejavusans', '', 9);
$pdf->SetXY(15, 35);
$pdf->MultiCell(70, 5,
    ($quotation->TblCompany->state ?? 'Tamil Nadu') . "\n" .
    ($quotation->TblCompany->country ?? 'India') . "\n" .
    ($quotation->TblCompany->phone ?? '+91 90 2012 2012') . "\n" .
    ($quotation->TblCompany->email ?? 'info@draravindsivf.com'),
    0, 'L', false);

// BILL Title & Details (Top Right)
$pdf->SetFont('dejavusans', 'B', 16);
$pdf->SetXY(150, 15);
$pdf->Cell(0, 0, 'Quotation Order', 0, 1, 'R');

$pdf->SetFont('dejavusans', '', 10);
$pdf->SetXY(150, 22);
$pdf->Cell(0, 0, ($quotation->quotation_no ?? '321456'), 0, 1, 'R');

// Balance Due
$pdf->SetFont('dejavusans', '', 9);
$pdf->SetXY(150, 28);
$pdf->Cell(0, 0, 'Balance Due', 0, 1, 'R');

$pdf->SetFont('dejavusans', 'B', 12);
$pdf->SetXY(150, 34);
$pdf->SetTextColor(0, 0, 0);
$pdf->Cell(0, 0, '₹' . number_format($quotation->balance_amount ?? 50000.00, 2), 0, 1, 'R');
$pdf->SetTextColor(0, 0, 0); // reset to black

// Bill From Section
$pdf->SetXY(15, 55);
$pdf->SetFont('dejavusans', '', 9);
$pdf->Cell(0, 0, 'Vendor Address', 0, 1);

$pdf->SetFont('dejavusans', 'B', 9);
$pdf->SetXY(15, 60);
$pdf->Cell(0, 0, $quotation->Tblvendor->display_name, 0, 1);

$pdf->SetFont('dejavusans', '', 9);
$pdf->SetXY(15, 65);
$pdf->MultiCell(70, 5,
    ($quotation->TblBilling->address ?? '-') . "\n" .
    ($quotation->TblBilling->city ?? '-') . "\n" .
    ($quotation->TblBilling->zip_code ?? '-') . ' ' . ($quotation->TblBilling->state ?? '-') . "\n" .
    ($quotation->TblBilling->country ?? '-'),
    0, 'L', false);

// Delivery Address Section
$pdf->SetXY(15, 90);
$pdf->SetFont('dejavusans', '', 9);
$pdf->Cell(0, 0, 'Delivery to', 0, 1);

$pdf->SetFont('dejavusans', '', 9);
$pdf->SetXY(15, 95);
$pdf->MultiCell(70, 5, ($quotation->delivery_address), 0, 'L', false);

// Right Side Info
$rightY = 60;
$rightX1 = 135;
$rightX2 = 170;
$info = [
    'Order Number :' => $quotation->order_number,
    'Purchase Date :' => $quotation->bill_date,
    'Due Date :' => $quotation->due_date,
    'Terms :' => $quotation->payment_terms,
];

$pdf->SetFont('dejavusans', '', 9);
foreach ($info as $label => $value) {
    $pdf->SetXY($rightX1, $rightY);
    $pdf->Cell(0, 0, $label, 0, 1);
    $pdf->SetXY($rightX2, $rightY);
    $pdf->Cell(0, 0, $value, 0, 1);
    $rightY += 7;
}

// Subject
$pdf->SetXY(15, 105);
$pdf->SetFont('dejavusans', '', 9);
$pdf->Cell(0, 0, 'Subject : ', 0, 1);

$pdf->SetXY(30, 105);
$pdf->Cell(0, 0, $quotation->subject, 0, 1);

// Items Table
$y = 115;
$pdf->SetFont('dejavusans', 'B', 9);
$pdf->SetFillColor(60, 60, 60);
$pdf->SetTextColor(255);

$col1 = 10;
$col2 = 40;
$col3 = 30;
$col4 = 25;
$col5 = 20;
$col6 = 20;
$col7 = 20;
$col8 = 20;

// Header row
$pdf->SetXY(15, $y);
$pdf->Cell($col1, 8, '#', 0, 0, 'C', true);
$pdf->Cell($col2, 8, 'Item & Description', 0, 0, 'C', true);
$pdf->Cell($col3, 8, 'Customer Details', 0, 0, 'C', true);
$pdf->Cell($col4, 8, 'Account', 0, 0, 'C', true);
$pdf->Cell($col5, 8, 'Qty', 0, 0, 'C', true);
$pdf->Cell($col6, 8, 'Rate', 0, 0, 'C', true);
$pdf->Cell($col7, 8, 'GST', 0, 0, 'C', true);
$pdf->Cell($col8, 8, 'Amount', 0, 1, 'C', true);

// Reset font and text color
$pdf->SetFont('dejavusans', '', 9);
$pdf->SetTextColor(0);
$y += 8;

// Track items to calculate page breaks
$items = $quotation->BillLines;
$itemCount = count($items);
$currentItem = 0;

while ($currentItem < $itemCount) {
    $line = $items[$currentItem];

    // Calculate row height BEFORE checking page break
    $itemHeight = ceil($pdf->GetStringWidth($line->item_details) / $col2) * 6;
    $itemHeight = max($itemHeight, 12);

    $custText = "⚑ " . ($line->customer) . "\n" . 'Non-Billable';
    $custHeight = ceil($pdf->GetStringWidth($custText) / $col3) * 6;
    $custHeight = max($custHeight, 12);

    $accountHeight = ceil($pdf->GetStringWidth($line->account) / $col4) * 6;
    $accountHeight = max($accountHeight, 12);

    $maxHeight = max($itemHeight, $custHeight, $accountHeight, 15);

    // Check if we need a new page BEFORE drawing the row
    if ($y + $maxHeight > 270) {
        $pdf->AddPage();
        $y = 30; // Start from top on new page

        // Recreate table header on new page
        $pdf->SetFont('dejavusans', 'B', 9);
        $pdf->SetFillColor(60, 60, 60);
        $pdf->SetTextColor(255);
        $pdf->SetXY(15, $y);
        $pdf->Cell($col1, 8, '#', 0, 0, 'C', true);
        $pdf->Cell($col2, 8, 'Item & Description', 0, 0, 'C', true);
        $pdf->Cell($col3, 8, 'Customer Details', 0, 0, 'C', true);
        $pdf->Cell($col4, 8, 'Account', 0, 0, 'C', true);
        $pdf->Cell($col5, 8, 'Qty', 0, 0, 'C', true);
        $pdf->Cell($col6, 8, 'Rate', 0, 0, 'C', true);
        $pdf->Cell($col7, 8, 'GST', 0, 0, 'C', true);
        $pdf->Cell($col8, 8, 'Amount', 0, 1, 'C', true);

        $pdf->SetFont('dejavusans', '', 9);
        $pdf->SetTextColor(0);
        $y += 8;
    }

    // Draw the row
    $pdf->SetXY(15, $y);
    $pdf->Cell($col1, $maxHeight, $currentItem + 1, 0, 0, 'C');

    // Column 2: Item & Description
    $pdf->SetXY(15 + $col1, $y);
    $pdf->MultiCell($col2, 6, $line->item_details, 0, 'L');

    // Column 3: Customer Details
    $pdf->SetXY(15 + $col1 + $col2, $y);
    $pdf->MultiCell($col3, 6, $custText, 0, 'L');

    // Column 4: Account
    $pdf->SetXY(15 + $col1 + $col2 + $col3, $y);
    $pdf->MultiCell($col4, 6, $line->account, 0, 'C');

    // Column 5: Qty
    $pdf->SetXY(15 + $col1 + $col2 + $col3 + $col4, $y);
    $pdf->Cell($col5, $maxHeight, number_format($line->quantity, 2), 0, 0, 'C');

    // Column 6: Rate
    $pdf->SetXY(15 + $col1 + $col2 + $col3 + $col4 + $col5, $y);
    $pdf->Cell($col6, $maxHeight, number_format($line->rate, 2), 0, 0, 'C');

    // Column 7: GST
    $pdf->SetXY(15 + $col1 + $col2 + $col3 + $col4 + $col5 + $col6, $y);
    $pdf->Cell($col7, $maxHeight, number_format($line->gst_amount, 2), 0, 0, 'C');

    // Column 8: Amount
    $pdf->SetXY(15 + $col1 + $col2 + $col3 + $col4 + $col5 + $col6 + $col7, $y);
    $pdf->Cell($col8, $maxHeight, number_format($line->amount, 2), 0, 1, 'C');

    // Draw line under the row
    $pdf->Line(15, $y + $maxHeight, 15 + $col1 + $col2 + $col3 + $col4 + $col5 + $col6 + $col7 + $col8, $y + $maxHeight);

    // Update Y position for next row
    $y += $maxHeight;
    $currentItem++;
}

// After all items are processed, add a new page if needed for summary
if ($y > 170) {
    $pdf->AddPage();
    $y = 30;
}

// ================== GST Breakdown ==================
$gstSummary = [];
foreach ($quotation->BillLines as $line) {
    if (is_numeric($line->gst_rate) && $line->gst_rate > 0) {
        if (is_numeric($line->cgst_amount) && $line->cgst_amount > 0) {
            $cgstRate = $line->gst_rate / 2;
            $sgstRate = $line->gst_rate / 2;

            $cgstAmount = $line->gst_amount / 2;
            $sgstAmount = $line->gst_amount / 2;

            $key = "CGST_{$cgstRate}";
            if (!isset($gstSummary[$key])) {
                $gstSummary[$key] = [
                    'type'   => 'CGST',
                    'rate'   => $cgstRate,
                    'amount' => 0
                ];
            }
            $gstSummary[$key]['amount'] += $cgstAmount;

            $key = "SGST_{$sgstRate}";
            if (!isset($gstSummary[$key])) {
                $gstSummary[$key] = [
                    'type'   => 'SGST',
                    'rate'   => $sgstRate,
                    'amount' => 0
                ];
            }
            $gstSummary[$key]['amount'] += $sgstAmount;

        } else {
            if (is_numeric($line->gst_rate) && is_numeric($line->gst_amount)) {
                $igstRate = $line->gst_rate;
                $igstAmount = $line->gst_amount;

                $key = "IGST_{$igstRate}";
                if (!isset($gstSummary[$key])) {
                    $gstSummary[$key] = [
                        'type'   => 'IGST',
                        'rate'   => $igstRate,
                        'amount' => 0
                    ];
                }
                $gstSummary[$key]['amount'] += $igstAmount;
            }
        }
    }
}

// ================== Render ==================

// Start summary Y position
$summaryY = max($y , 10);

// Sub Total
$pdf->SetFont('dejavusans', 'B', 10);
$pdf->SetXY(140, $summaryY);
$pdf->Cell(30, 7, 'Sub Total', 0, 0, 'L');
$pdf->Cell(30, 7, number_format($quotation->sub_total_amount, 2), 0, 1, 'R');
$summaryY += 7;

// GST breakdown
$pdf->SetFont('dejavusans', '', 10);
foreach ($gstSummary as $entry) {
    $pdf->SetXY(140, $summaryY);
    $pdf->Cell(30, 7, $entry['type'] . ' ' . $entry['rate'] . '%', 0, 0, 'L');
    $pdf->Cell(30, 7, number_format($entry['amount'], 2), 0, 1, 'R');
    $summaryY += 7;
}

$pdf->SetXY(140, $summaryY);
$pdf->Cell(30, 7, 'Discount(-)', 0, 0, 'L');
$pdf->SetFont('dejavusans', 'B', 10);
$pdf->Cell(30, 7, '₹' . number_format($quotation->discount_amount ?? 100000.00, 2), 0, 1, 'R');

$pdf->SetXY(140, $summaryY + 7);
$pdf->Cell(30, 7, 'TDS(-)', 0, 0, 'L');
$pdf->SetFont('dejavusans', 'B', 10);
$pdf->Cell(30, 7, '₹' . number_format($quotation->tax_amount ?? 100000.00, 2), 0, 1, 'R');

$pdf->SetXY(140, $summaryY + 14);
$pdf->Cell(30, 7, 'TOTAL', 0, 0, 'L');
$pdf->SetFont('dejavusans', 'B', 10);
$pdf->Cell(30, 7, '₹' . number_format($quotation->grand_total_amount ?? '-'), 0, 1, 'R');


if($quotation->export_name !== null){
    $pdf->SetXY(140, $summaryY + 21);
    $pdf->Cell(30, 7, $quotation->export_name , 0, 0, 'L');
    $pdf->SetFont('dejavusans', 'B', 10);
    $pdf->Cell(30, 7, '₹' . number_format($quotation->export_amount ?? 100000.00, 2), 0, 1, 'R');
}

if($quotation->export_name === null){
    $pdf->SetFont('dejavusans', '', 9);
    $pdf->SetTextColor(255, 0, 0);
    $pdf->SetXY(140, $summaryY + 21);
    $pdf->Cell(30, 7, 'Payments Made', 0, 0, 'L');
    $pdf->Cell(30, 7, '(-) ₹' . number_format($quotation->partially_payment ?? 50000.00, 2), 0, 1, 'R');
}else{
    $pdf->SetFont('dejavusans', '', 9);
    $pdf->SetTextColor(255, 0, 0);
    $pdf->SetXY(140, $summaryY + 28);
    $pdf->Cell(30, 7, 'Payments Made', 0, 0, 'L');
    $pdf->Cell(30, 7, '(-) ₹' . number_format($quotation->partially_payment ?? 50000.00, 2), 0, 1, 'R');
}

if($quotation->export_name === null){
    $pdf->SetFillColor(240, 240, 240);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFont('dejavusans', 'B', 10);
    $pdf->SetXY(140, $summaryY + 30);
    $pdf->Cell(60, 8, 'Balance Due : ₹' . number_format($quotation->balance_amount ?? 50000.00, 2), 0, 1, 'C', true);
}else{
    $pdf->SetFillColor(240, 240, 240);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFont('dejavusans', 'B', 10);
    $pdf->SetXY(140, $summaryY + 38);
    $pdf->Cell(60, 8, 'Balance Due : ₹' . number_format($quotation->balance_amount ?? 50000.00, 2), 0, 1, 'C', true);
}

if($quotation->export_name === null){
    $pdf->SetFont('dejavusans', '', 9);
    $pdf->SetXY(15, $summaryY + 45);
    $pdf->Cell(60, 8, 'Authorized Signature', 0, 1, 'L');
    $pdf->Line(50, $summaryY + 52, 100, $summaryY + 52);
}else{
    $pdf->SetFont('dejavusans', '', 9);
    $pdf->SetXY(15, $summaryY + 50);
    $pdf->Cell(60, 8, 'Authorized Signature', 0, 1, 'L');
    $pdf->Line(50, $summaryY + 57, 100, $summaryY + 57);
}
?>