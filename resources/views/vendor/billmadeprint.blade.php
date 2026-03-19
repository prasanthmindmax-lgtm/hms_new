<?php
// Set base font and starting position
$pdf->SetFont('dejavusans', '', 10);
$pdf->SetTextColor(0, 0, 0); // black
$pdf->SetY(15); // Start 15mm from top


$imagePath = str_replace('\\', '/', base_path('assets/images/dralogos.png'));
$pdf->Image($imagePath, 15, 10, 50, '', 'PNG', '', 'T', false, 300);


// Company Information (Top Left)
$pdf->SetFont('dejavusans', 'B', 12);
$pdf->SetXY(130, 15);
$pdf->Cell(0, 5, $billmade->TblCompany->company_name ?? 'abc private limited', 0, 1, 'L');

$pdf->SetFont('dejavusans', '', 10);
$billing = $billmade->TblBilling;
$pdf->SetXY(130, 20);
$pdf->MultiCell(90, 5,
    ($billmade->TblCompany->state ?? 'Tamil Nadu') . "\n" .
    ($billmade->TblCompany->phone ?? '9502970811') . "\n" .
    ($billmade->TblCompany->email  ?? 'santibar708@gmail.com'),
0, 'L', false);

// Divider line
$pdf->SetY(40);
$pdf->Line(15, $pdf->GetY(), 195, $pdf->GetY());
$pdf->SetY($pdf->GetY() + 5);

// Payment header
$pdf->SetFont('dejavusans', 'B', 14);
$pdf->SetY(50);
$pdf->Cell(0, 8, 'PAYMENTS MADE', 0, 1, 'C');
$pdf->SetY($pdf->GetY() + 5);

// Payment details
$labels = ['Payment#', 'Payment Date', 'Reference Number', 'Paid To', 'Payment Mode', 'Paid Through', 'Amount Paid in Words'];
$values = [
    $billmade->payment ?? '3',
    date('d/m/Y', strtotime($billmade->payment_date ?? '2025-07-26')),
    $billmade->reference ?? '',
    $billmade->Tblvendor->display_name ?? 'old company',
    $billmade->payment_mode ?? 'Cash',
    $billmade->paid_through ?? 'Petty Cash',
    convertNumberToWords($billmade->payment_made ?? 50000) . ' Only'
];

$startX = 15;
$startY = 60;
$lineHeight = 7;

for ($i = 0; $i < count($labels); $i++) {
    // Check if we need a new page before adding the next line
    if ($startY + ($i * $lineHeight) > 250) {
        $pdf->AddPage();
        $startY = 30;
        $i = 0; // Reset counter for new page
    }

    $pdf->SetXY($startX, $startY + ($i * $lineHeight));
    $pdf->SetFont('dejavusans', '', 10);
    $pdf->Cell(40, $lineHeight, $labels[$i], 0, 0);

    $pdf->SetFont('dejavusans', 'B', 10);

    if ($labels[$i] === 'Reference Number') {
        // Use MultiCell for Reference Number
        $pdf->MultiCell(70, $lineHeight, ': ' . $values[$i], 0, 1);
    } else {
        // Normal Cell for other fields
        $pdf->Cell(70, $lineHeight, ': ' . $values[$i], 0, 1);
    }
}


// Amount paid box (right side)
$boxX = 140;
$boxY = $startY + 6;
$pdf->SetXY($boxX, $boxY);
$pdf->SetFillColor(200, 200, 200);
$pdf->Rect($boxX, $boxY, 50, 25, 'F');

$pdf->SetTextColor(0, 0, 0);
$pdf->SetFont('dejavusans', 'B', 12);
$pdf->SetXY($boxX, $boxY + 5);
$pdf->Cell(50, 6, 'Amount Paid', 0, 2, 'C', 0, '', 1);
$pdf->SetFont('dejavusans', 'B', 14);
// $pdf->Cell(50, 10, '₹' . number_format($billmade->payment_made, 2), 0, 2, 'C', 0, '', 1);
// $value = is_numeric($billmade->payment_made) ? (float) $billmade->payment_made : 0;
$pdf->Cell(50, 10, '₹' . number_format((float)$billmade->amount_used, 2), 0, 2, 'C', 0, '', 1);

// Paid To section
$paidToY = $startY + (count($labels) * $lineHeight) + 10;
if ($paidToY > 250) {
    $pdf->AddPage();
    $paidToY = 30;
}

$pdf->SetXY(15, $paidToY);
$pdf->SetFont('dejavusans', 'B', 10);
$pdf->Cell(0, 10, 'Paid To', 0, 1);

$pdf->SetFont('dejavusans', '', 10);
$vendor = $billmade->Tblvendor;
$pdf->SetXY(15, $paidToY + 10);
$pdf->MultiCell(90, 5,
    ($vendor->display_name ?? 'old company') . "\n" .
    ($billing->address ?? 'classmate') . "\n" .
    ($billing->pincode ?? '125845') . ' ' . ($billing->state ?? 'Tamil Nadu') . "\n" .
    ($billing->country ?? 'India'),
0, 'L', false);

// Payment for section
$paymentForY = $paidToY + 35;
if ($paymentForY > 250) {
    $pdf->AddPage();
    $paymentForY = 30;
}

$pdf->SetY($paymentForY);
$pdf->SetFont('dejavusans', 'B', 12);
$pdf->SetXY(15, $pdf->GetY());
$pdf->Cell(0, 7, 'Payment for', 0, 1, 'L');
$pdf->SetY($pdf->GetY() + 5);

// Payment details table
$headerY = $pdf->GetY();
if ($headerY > 230) {
    $pdf->AddPage();
    $headerY = 30;
}

$pdf->SetFont('dejavusans', 'B', 10);
$pdf->SetFillColor(220, 220, 220);
$pdf->SetXY(15, $headerY);
$pdf->Cell(40, 8, 'Bill Number', 0, 0, 'C', 1);
$pdf->Cell(40, 8, 'Bill Date', 0, 0, 'C', 1);
$pdf->Cell(50, 8, 'Bill Amount', 0, 0, 'C', 1);
$pdf->Cell(50, 8, 'Payment Amount', 0, 0, 'C', 1);

// Table content
$contentStartY = $headerY + 12;
foreach ($billmade->BillLines as $line) {
    // Check if we need a new page before adding the next line
    if ($contentStartY > 250) {
        $pdf->AddPage();
        $contentStartY = 30;

        // Redraw table header on new page
        $pdf->SetFont('dejavusans', 'B', 10);
        $pdf->SetFillColor(220, 220, 220);
        $pdf->SetXY(15, $contentStartY - 12);
        $pdf->Cell(40, 8, 'Bill Number', 0, 0, 'C', 1);
        $pdf->Cell(40, 8, 'Bill Date', 0, 0, 'C', 1);
        $pdf->Cell(50, 8, 'Bill Amount', 0, 0, 'C', 1);
        $pdf->Cell(50, 8, 'Payment Amount', 0, 0, 'C', 1);

        $contentStartY += 12;
    }

    $pdf->SetFont('dejavusans', '', 10);
    $pdf->SetXY(15, $contentStartY);
    $pdf->Cell(40, 8, $line->bill_number ?? '', 0, 0, 'C', false);
    $pdf->Cell(40, 8, $line->bill_date ?? '', 0, 0, 'C', false);
    $pdf->Cell(50, 8, '₹' . number_format($line->grand_total_amount ?? 0, 2), 0, 0, 'C', false);
    $pdf->Cell(50, 8, '₹' . number_format($line->amount ?? 0, 2), 0, 0, 'C', false);

    // Draw line after each row
    $pdf->Line(15, $contentStartY + 12, 195, $contentStartY + 12);
    $contentStartY += 12;
}

// Authorized signature section
$signatureY = $contentStartY + 15;
if ($signatureY > 250) {
    $pdf->AddPage();
    $signatureY = 30;
}

$pdf->SetXY(15, $signatureY);
$pdf->SetFont('dejavusans', 'B', 10);
$pdf->Cell(60, 8, 'Authorized Signature', 0, 1, 'L');
$pdf->Line(60, $signatureY + 8, 100, $signatureY + 8);

function convertNumberToWords($number) {
    $hyphen      = '-';
    $conjunction = ' and ';
    $separator   = ', ';
    $negative    = 'Negative ';
    $decimal     = ' point ';
    $dictionary  = [
        0 => 'Zero',
        1 => 'One',
        2 => 'Two',
        3 => 'Three',
        4 => 'Four',
        5 => 'Five',
        6 => 'Six',
        7 => 'Seven',
        8 => 'Eight',
        9 => 'Nine',
        10 => 'Ten',
        11 => 'Eleven',
        12 => 'Twelve',
        13 => 'Thirteen',
        14 => 'Fourteen',
        15 => 'Fifteen',
        16 => 'Sixteen',
        17 => 'Seventeen',
        18 => 'Eighteen',
        19 => 'Nineteen',
        20 => 'Twenty',
        30 => 'Thirty',
        40 => 'Forty',
        50 => 'Fifty',
        60 => 'Sixty',
        70 => 'Seventy',
        80 => 'Eighty',
        90 => 'Ninety',
        100 => 'Hundred',
        1000 => 'Thousand',
        100000 => 'Lakh',
        10000000 => 'Crore'
    ];

    if (!is_numeric($number)) {
        return $number;
    }

    if ($number < 0) {
        return $negative . convertNumberToWords(abs($number));
    }

    $string = '';

    foreach ([10000000 => 'Crore', 100000 => 'Lakh', 1000 => 'Thousand', 100 => 'Hundred'] as $value => $word) {
        if (($number / $value) >= 1) {
            $num = floor($number / $value);
            $string .= convertNumberToWords($num) . ' ' . $word . ' ';
            $number %= $value;
        }
    }

    if ($number > 0) {
        if ($number < 21) {
            $string .= $dictionary[$number];
        } else {
            $tens = ((int)($number / 10)) * 10;
            $units = $number % 10;
            $string .= $dictionary[$tens];
            if ($units) {
                $string .= $hyphen . $dictionary[$units];
            }
        }
    }

    return trim($string);
}
?>