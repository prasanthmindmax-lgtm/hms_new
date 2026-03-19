<?php
// vendor/statementprint.blade.php
use Carbon\Carbon;

// Assuming $vendor, $billing, $transactions, $billed, $paid, $balance are passed from controller

$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);
$pdf->SetMargins(15, 15, 15);
$pdf->AddPage();
$pdf->SetFont('helvetica', '', 10);
$pdf->SetTextColor(0, 0, 0);

// ================= Vendor Info (Left) =================
$pdf->SetFont('helvetica', 'B', 10);
$pdf->SetXY(15, 15);
$pdf->Cell(0, 0, 'To', 0, 1);

$pdf->SetFont('helvetica', 'B', 12);
$pdf->SetXY(15, 22);
$pdf->Cell(0, 0, $vendor->display_name ?? 'NADESA SERVICE SATATION', 0, 1);

$pdf->SetFont('helvetica', '', 10);
$pdf->SetXY(15, 30);
$pdf->MultiCell(90, 5,
    ($billing->address ?? 'null') . "\n" .
    ($billing->city ?? 'null') . "\n" .
    (($billing->state ?? 'null') . ' ' . ($billing->zip_code ?? 'null')),
    0, 'L', 0, 1
);

// ================= Company Info (Right) =================
$pdf->SetFont('helvetica', 'B', 11);
$pdf->SetXY(120, 15);
$pdf->Cell(80, 0, $billing->company_name ?? "Aravind's IVF", 0, 1, 'R');

$pdf->SetFont('helvetica', '', 9);
$pdf->SetXY(120, 22);
$pdf->Cell(80, 0, $billing->state ?? 'Karnataka', 0, 1, 'R');

$pdf->SetXY(120, 28);
$pdf->Cell(80, 0, $billing->city ?? 'chennai', 0, 1, 'R');

$pdf->SetXY(120, 34);
$pdf->Cell(80, 0, $billing->phone ?? '6541651651', 0, 1, 'R');

$pdf->SetXY(120, 40);
$pdf->Cell(80, 0, $billing->email ?? 'info@draravindsivf.com', 0, 1, 'R');

// ================= Statement Title =================
$pdf->SetFont('helvetica', 'B', 12);
$pdf->SetXY(120, 55);
$pdf->Cell(80, 0, 'Statement of Accounts', 0, 1, 'R');

// ================= Account Summary Box =================
$pdf->SetFillColor(240, 240, 240);
$pdf->SetXY(120, 72);
$pdf->SetFont('helvetica', 'B', 10);
$pdf->Cell(80, 7, 'Account Summary', 1, 1, 'L', 1);

$pdf->SetFont('helvetica', '', 9);
$pdf->SetXY(120, 79);
$pdf->Cell(40, 7, 'Billed Amount', 1, 0, 'L');
$pdf->Cell(40, 7, '₹ ' . number_format($billed, 2), 1, 1, 'R');

$pdf->SetXY(120, 86);
$pdf->Cell(40, 7, 'Amount Paid', 1, 0, 'L');
$pdf->Cell(40, 7, '₹ ' . number_format($paid, 2), 1, 1, 'R');

$pdf->SetXY(120, 93);
$pdf->SetFont('helvetica', 'B', 9);
$pdf->Cell(40, 7, 'Balance Due', 1, 0, 'L');
$pdf->Cell(40, 7, '₹ ' . number_format($balance, 2), 1, 1, 'R');

// ================= Transaction Table =================
$pdf->SetY(110);
$pdf->SetFont('helvetica', 'B', 9);
$pdf->SetFillColor(51, 51, 51);   // Black header background
$pdf->SetTextColor(255, 255, 255); // White text

$col1 = 28; $col2 = 25; $col3 = 55; $col4 = 25; $col5 = 25; $col6 = 30;

// Table Header
$pdf->Cell($col1, 8, 'Date', 0, 0, 'C', 1);
$pdf->Cell($col2, 8, 'Transactions', 0, 0, 'C', 1);
$pdf->Cell($col3, 8, 'Details', 0, 0, 'C', 1);
$pdf->Cell($col4, 8, 'Amount', 0, 0, 'C', 1);
$pdf->Cell($col5, 8, 'Payments', 0, 0, 'C', 1);
$pdf->Cell($col6, 8, 'Balance', 0, 1, 'C', 1);

// Reset font and text color for rows
$pdf->SetFont('helvetica', '', 9);
$pdf->SetTextColor(0, 0, 0);

// Table Rows (white background only)
foreach($transactions as $transaction) {
    $pdf->Cell($col1, 8, $transaction['date'], 'B', 0, 'C');
    $pdf->Cell($col2, 8, $transaction['type'], 'B', 0, 'C');
    $pdf->Cell($col3, 8, $transaction['details'], 'B', 0, 'L');
    $pdf->Cell($col4, 8, $transaction['amount'] > 0 ? number_format($transaction['amount'], 2) : '', 'B', 0, 'R');
    $pdf->Cell($col5, 8, $transaction['payment'] > 0 ? number_format($transaction['payment'], 2) : '', 'B', 0, 'R');
    $pdf->Cell($col6, 8, number_format($transaction['balance'], 2), 'B', 1, 'R');
}

// ================= Final Balance Due =================
$pdf->Ln(3);
$pdf->SetFont('helvetica', 'B', 10);

// Empty space across table before Balance Due
$pdf->Cell($col1 + $col2 + $col3 + $col4 + $col5, 8, 'Balance Due', 0, 0, 'R');
$pdf->Cell($col6, 8, '₹ ' . number_format($balance, 2), 0, 1, 'R');
// Output PDF
$pdf->Output('statement.pdf', 'I');
?>