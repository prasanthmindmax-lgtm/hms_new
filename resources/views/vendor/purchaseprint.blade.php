<?php
// Set base font
$pdf->SetFont('dejavusans', '', 10);
$pdf->SetTextColor(0, 0, 0); // black

$imagePath = str_replace('\\', '/', base_path('assets/images/dralogos.png'));
$pdf->Image($imagePath, 15, 10, 50, '', 'PNG', '', 'T', false, 300);

// Company Information (Top Left)
$pdf->SetFont('dejavusans', 'B', 10);
$pdf->SetXY(15, 30);
$pdf->Cell(0, 0, $purchase->TblCompany->company_name ?? "Aravind's IVF", 0, 1);

$pdf->SetFont('dejavusans', '', 9);
$pdf->SetXY(15, 35);
$pdf->MultiCell(70, 5,
    ($purchase->TblCompany->state ?? 'Tamil Nadu') . "\n" .
    ($purchase->TblCompany->country ?? 'India') . "\n" .
    ($purchase->TblCompany->phone ?? '+91 90 2012 2012') . "\n" .
    ($purchase->TblCompany->email ?? 'info@draravindsivf.com'),
    0, 'L', false);

// BILL Title & Details (Top Right)
$pdf->SetFont('dejavusans', 'B', 16);
$pdf->SetXY(150, 15);
$pdf->Cell(0, 0, 'Purchase Order', 0, 1, 'R');

$pdf->SetFont('dejavusans', '', 10);
$pdf->SetXY(150, 22);
$pdf->Cell(0, 0, 'PO # ' . ($purchase->purchase_order_number ?? '321456'), 0, 1, 'R');

// Balance Due
$pdf->SetFont('dejavusans', '', 9);
$pdf->SetXY(150, 28);
$pdf->Cell(0, 0, 'Balance Due', 0, 1, 'R');

$pdf->SetFont('dejavusans', 'B', 12);
$pdf->SetXY(150, 34);
$pdf->SetTextColor(0, 0, 0);
$pdf->Cell(0, 0, '₹' . number_format($purchase->balance_amount ?? 50000.00, 2), 0, 1, 'R');
$pdf->SetTextColor(0, 0, 0); // reset to black

// Bill From Section
$pdf->SetXY(15, 55);
$pdf->SetFont('dejavusans', 'B', 9);
$pdf->Cell(0, 0, 'Vendor Address', 0, 1);

$pdf->SetFont('dejavusans', 'B', 9);
$pdf->SetXY(15, 60);
$pdf->Cell(0, 0, $purchase->Tblvendor->display_name, 0, 1);

$pdf->SetFont('dejavusans', '', 9);
$pdf->SetXY(15, 65);
$pdf->MultiCell(70, 5,
    ($purchase->TblBilling?->address ?? 'Null') . "\n" .
    ($purchase->TblBilling?->city ?? 'Null') . "\n" .
    ($purchase->TblBilling?->zip_code ?? 'Null') . ' ' . ($purchase->TblBilling?->state ?? 'Null') . "\n" .
    ($purchase->TblBilling?->country ?? 'Null'),
    0, 'L', false
);

// Delivery Address Section
$pdf->SetXY(15, 97);
$pdf->SetFont('dejavusans', 'B', 9);
$pdf->Cell(0, 0, 'Delivery to', 0, 1);

$pdf->SetFont('dejavusans', '', 9);
$pdf->SetXY(15, 102);
$pdf->MultiCell(130, 20, ($purchase->delivery_address), 0, 'L', false);

// Right Side Info
$rightY = 60;
$rightX1 = 135;
$rightX2 = 170;
$info = [
    'Order Number :' => $purchase->order_number,
    'Purchase Date :' => $purchase->bill_date,
    'Due Date :' => $purchase->due_date,
    'Terms :' => $purchase->payment_terms,
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
$pdf->SetXY(15, 125);
$pdf->SetFont('dejavusans', '', 9);
$pdf->Cell(0, 0, 'Subject : ', 0, 1);

$pdf->SetXY(30, 125);
$pdf->Cell(0, 0, $purchase->subject, 0, 1);

// Items Table
$y = 130;
$pdf->SetFont('dejavusans', 'B', 9);
$pdf->SetFillColor(60, 60, 60);
$pdf->SetTextColor(255);

$col1 = 10;
$col2 = 100;
$col3 = 25;
$col4 = 25;
$col5 = 25;

// Header row
$pdf->SetXY(15, $y);
$pdf->Cell($col1, 8, '#', 0, 0, 'C', true);
$pdf->Cell($col2, 8, 'Item & Description', 0, 0, 'C', true);
$pdf->Cell($col3, 8, 'Qty', 0, 0, 'C', true);
$pdf->Cell($col4, 8, 'Rate', 0, 0, 'C', true);
$pdf->Cell($col5, 8, 'Amount', 0, 1, 'C', true);

// Reset font and text color
$pdf->SetFont('dejavusans', '', 9);
$pdf->SetTextColor(0);
$y += 8;

// Track items to calculate page breaks
$items = $purchase->BillLines;
$itemCount = count($items);
$currentItem = 0;

while ($currentItem < $itemCount) {
    $line = $items[$currentItem];

    // Calculate row height BEFORE checking page break
    $itemHeight = ceil($pdf->GetStringWidth($line->item_details) / $col2) * 6;
    $itemHeight = max($itemHeight, 12);

    $maxHeight = max($itemHeight, 15);

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
        $pdf->Cell($col3, 8, 'Qty', 0, 0, 'C', true);
        $pdf->Cell($col4, 8, 'Rate', 0, 0, 'C', true);
        $pdf->Cell($col5, 8, 'Amount', 0, 1, 'C', true);

        $pdf->SetFont('dejavusans', '', 9);
        $pdf->SetTextColor(0);
        $y += 8;
    }

    // Draw the row
    $pdf->SetXY(15, $y);
    $pdf->Cell($col1, $maxHeight, $currentItem + 1, 0, 0, 'C');

    // Column 2: Item & Description
    $pdf->SetXY(15 + $col1, $y+5);
    $pdf->MultiCell($col2, 2, $line->item_details, 0, 'L');

    // Column 3: Qty
    $pdf->SetXY(15 + $col1 + $col2, $y);
    $pdf->Cell($col3, $maxHeight, number_format($line->quantity, 2), 0, 0, 'C');

    // Column 4: Rate
    $pdf->SetXY(15 + $col1 + $col2 + $col3, $y);
    $pdf->Cell($col4, $maxHeight, number_format($line->rate, 2), 0, 0, 'C');

    // Column 5: Amount
    $pdf->SetXY(15 + $col1 + $col2 + $col3 + $col4, $y);
    $pdf->Cell($col5, $maxHeight, number_format($line->amount, 2), 0, 1, 'C');

    // Draw line under the row
    $pdf->Line(15, $y + $maxHeight, 15 + $col1 + $col2 + $col3 + $col4 + $col5, $y + $maxHeight);

    // Update Y position for next row
    $y += $maxHeight;
    $currentItem++;
}

// After all items are processed, add a new page if needed for summary
if ($y > 190) {
    $pdf->AddPage();
    $y = 30;
}

// ================== GST Breakdown ==================
$gstSummary = [];
foreach ($purchase->BillLines as $line) {
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
$summaryY = $y + 10;

// Sub Total
$pdf->SetFont('dejavusans', 'B', 10);
$pdf->SetXY(140, $summaryY);
$pdf->Cell(30, 7, 'Sub Total', 0, 0, 'L');
$pdf->Cell(30, 7, number_format($purchase->sub_total_amount, 2), 0, 1, 'R');
$summaryY += 7;

// GST breakdown
$pdf->SetFont('dejavusans', '', 10);
foreach ($gstSummary as $entry) {
    $pdf->SetXY(140, $summaryY);
    $pdf->Cell(30, 7, $entry['type'] . ' ' . $entry['rate'] . '%', 0, 0, 'L');
    $pdf->Cell(30, 7, number_format($entry['amount'], 2), 0, 1, 'R');
    $summaryY += 7;
}

$pdf->SetXY(140, $summaryY + 7);
$pdf->Cell(30, 7, 'Discount(-)', 0, 0, 'L');
$pdf->SetFont('dejavusans', 'B', 10);
$pdf->Cell(30, 7, '₹' . number_format($purchase->discount_amount ?? 100000.00, 2), 0, 1, 'R');

$pdf->SetXY(140, $summaryY + 14);
$pdf->Cell(30, 7, 'TDS(-)', 0, 0, 'L');
$pdf->SetFont('dejavusans', 'B', 10);
$pdf->Cell(30, 7, '₹' . number_format($purchase->tax_amount ?? 100000.00, 2), 0, 1, 'R');

$pdf->SetXY(140, $summaryY + 21);
$pdf->Cell(30, 7, 'TOTAL', 0, 0, 'L');
$pdf->SetFont('dejavusans', 'B', 10);
$pdf->Cell(30, 7, '₹' . number_format($purchase->grand_total_amount ?? '-'), 0, 1, 'R');


if($purchase->export_name !== null){
    $pdf->SetXY(140, $summaryY + 28);
    $pdf->Cell(30, 7, $purchase->export_name , 0, 0, 'L');
    $pdf->SetFont('dejavusans', 'B', 10);
    $pdf->Cell(30, 7, '₹' . number_format($purchase->export_amount ?? 100000.00, 2), 0, 1, 'R');
}

if($purchase->export_name === null){
    $pdf->SetFont('dejavusans', '', 9);
    $pdf->SetTextColor(255, 0, 0);
    $pdf->SetXY(140, $summaryY + 28);
    $pdf->Cell(30, 7, 'Payments Made', 0, 0, 'L');
    $pdf->Cell(30, 7, '(-) ₹' . number_format($purchase->partially_payment ?? 50000.00, 2), 0, 1, 'R');
}else{
    $pdf->SetFont('dejavusans', '', 9);
    $pdf->SetTextColor(255, 0, 0);
    $pdf->SetXY(140, $summaryY + 35);
    $pdf->Cell(30, 7, 'Payments Made', 0, 0, 'L');
    $pdf->Cell(30, 7, '(-) ₹' . number_format($purchase->partially_payment ?? 50000.00, 2), 0, 1, 'R');
}

if($purchase->export_name === null){
    $pdf->SetFillColor(240, 240, 240);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFont('dejavusans', 'B', 10);
    $pdf->SetXY(140, $summaryY + 35);
    $pdf->Cell(60, 8, 'Balance Due : ₹' . number_format($purchase->balance_amount ?? 50000.00, 2), 0, 1, 'C', true);
}else{
    $pdf->SetFillColor(240, 240, 240);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFont('dejavusans', 'B', 10);
    $pdf->SetXY(140, $summaryY + 42);
    $pdf->Cell(60, 8, 'Balance Due : ₹' . number_format($purchase->balance_amount ?? 50000.00, 2), 0, 1, 'C', true);
}

// ================== NOTE SECTION ==================
// Get note data from purchase object (assuming it might have a 'notes' field)
$noteText = $purchase->note ?? ''; // Adjust this based on your actual field name

if (!empty($noteText)) {
    // Calculate Y position for note section
    if($purchase->export_name === null){
        $noteY = $summaryY + 45; // Position after Balance Due
    } else {
        $noteY = $summaryY + 52; // Position after Balance Due when export exists
    }

    // Note section styling
    $pdf->SetFont('dejavusans', 'B', 9);
    $pdf->SetXY(15, $noteY);
    $pdf->Cell(30, 7, 'Note:', 0, 0, 'L');

    // Note content
    $pdf->SetFont('dejavusans', '', 9);
    $pdf->SetXY(30, $noteY);
    // MultiCell for note text with word wrap
    $pdf->MultiCell(100, 5, $noteText, 0, 'L');

    // Get the Y position after the note to position signature accordingly
    $signatureY = $pdf->GetY() + 5;
} else {
    // If no note, set signature position based on existing logic
    if($purchase->export_name === null){
        $signatureY = $summaryY + 45;
    } else {
        $signatureY = $summaryY + 50;
    }
}
// ================== AUTHORIZED SIGNATURE ==================

// Ensure signature fits in current page
$pageHeight = $pdf->getPageHeight();
$bottomMargin = 30;

// If signature crosses page, add new page
if ($signatureY > ($pageHeight - $bottomMargin)) {
    $pdf->AddPage();
    $signatureY = $pdf->GetY() + 10;
}

// Signature Text
$pdf->SetFont('dejavusans', '', 9);
$pdf->SetXY(15, $signatureY);
$pdf->Cell(50, 8, 'Authorized Signature', 0, 1, 'L');

// Signature Line (aligned with text)
$pdf->Line(50, $signatureY + 7, 100, $signatureY + 7);

?>