<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Purchase Order - {{ $purchase->purchase_gen_order ?? '000082' }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        @page {
            margin: 0;
            size: A4;
        }

        body {
              font-family: "DejaVu Sans", sans-serif;
              font-size: 10px;
              line-height: 1.2;
              color: #000;
              background: #fff;
              padding: 10mm 15mm;
          }


        /* ========== LOGO SECTION ========== */
        .logo-section {
            margin-bottom: 15px;
            height: 15mm;
        }

        .logo-image {
            height: 15mm;
            max-width: 60mm;
            object-fit: contain;
        }

        /* ========== TOP SECTION ========== */
        .top-section {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }

        .left-info {
            display: table-cell;
            width: 65%;
            vertical-align: top;
            padding-right: 10mm;
        }

        .right-info {
            display: table-cell;
            width: 30%;
            vertical-align: top;
            text-align: right;
            page-break-inside: avoid;
        }

        .company-name {
            font-weight: bold;
            font-size: 15px;
            margin-bottom: 2px;
        }

        .company-details {
            font-size: 12px;
            line-height: 1.3;
        }

        .vendor-section {
            margin-top: 15px;
        }

        .section-label {
            font-weight: bolder;
            margin-bottom: 2px;
            font-size: 15px;
        }

        .vendor-name {
            font-weight: bold;
            margin: 2px 0;
            font-size: 15px;
        }

        .vendor-details {
            font-size: 12px;
            line-height: 1.3;
            width: 90mm;
        }

        .delivery-section {
            margin-top: 15px;
        }

        /* ========== RIGHT SIDE INFO ========== */
        .quotation-title-main {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 3px;
            color: #000;
        }

        .quotation-number {
            font-size: 14px;
            text-align: right;
            margin: 2px;
            font-weight: normal;
        }

        .balance-label {
            font-size: 15px;
            margin-bottom: 2px;
            font-weight: normal;
        }

        .balance-amount {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 15px;
            color: #000;
        }

        .order-info-table {
            width: 100%;
            font-size: 12px;
            margin-top: 5px;
        }

        .order-info-table td {
            padding: 2px 0;
        }

        .order-info-table td:first-child {
            text-align: left;
            padding-right: 5px;
            white-space: nowrap;
        }

        .order-info-table td:last-child {
            text-align: right;
        }

        /* ========== SUBJECT ========== */
        .subject-section {
            font-size: 13px;
            margin: 15px 0 10px 0;
            font-weight: normal;
        }

        /* ========== ITEMS TABLE ========== */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0 15px 0;
            font-size: 9px;
        }

        .items-table thead {
            background-color: #3c3c3c;
            color: #fff;
        }

        .items-table th {
            padding: 2px;
            text-align: center;
            font-size: 9px;
            font-weight: bold;
            border: none;
            height: 6mm;
        }

        .items-table tbody td {
            padding: 6px 4px;
            font-size: 12px;
            text-align: center;
            border-bottom: 1px solid #e5e5e5;
            vertical-align: top;
            line-height: 1.2;
        }

        .items-table tbody tr:last-child td {
            border-bottom: 1px solid #e5e5e5;
        }

        .items-table td.number {
            text-align: center;
            width: 10mm;
        }

        .items-table td.description {
            text-align: left;
            width: 45mm;
            padding-left: 3px;
        }

        .items-table td.customer {
            text-align: left;
            width: 25mm;
            padding-left: 3px;
        }

        .items-table td.account {
            width: 25mm;
            text-align: center;
            font-size: 10px;
        }

        .items-table td.qty {
            width: 15mm;
            text-align: center;
        }

        .items-table td.rate {
            width: 15mm;
            text-align: center;
        }

        .items-table td.gst {
            width: 15mm;
            text-align: center;
        }

        .items-table td.amount {
            width: 15mm;
            text-align: center;
            padding-right: 5px;
        }

        .customer-flag {
            font-size: 9px;
            margin-right: 1px;
        }

        /* ========== SUMMARY SECTION ========== */
        /* .summary-section {
            width: 100%;
            margin-top: 20px;
            margin-bottom: 30px;
            display: flex;
            justify-content: end;
            align-content: end;
        }

        .summary-box {

            float: right;
            width: 60mm;
            font-size: 10px;
        } */
         .summary-section {
            width: 100%;
            margin-top: 20px;
            margin-bottom: 10px;
            text-align: right; /* Add this */
        }

        .summary-box {
            display: inline-block; /* Change from float to inline-block */
            width: 60mm;
            font-size: 10px;
            text-align: left; /* Reset text alignment inside the box */
            /* Remove float: right */
        }

        .summary-row {
            display: table;
            width: 100%;
            padding: 3px 0;
            min-height: 5mm;
        }

        .summary-label {
            font-size: 13px;
            font-weight: bold;
            display: table-cell;
            text-align: left;
            width: 60%;
            padding-right: 5px;
            vertical-align: middle;
        }

        .summary-value {
            display: table-cell;
            text-align: right;
            font-size: 12px;
            width: 40%;
            vertical-align: middle;
        }

        .summary-row.total-row {
            font-weight: bold;
            font-size: 10px;
            padding-top: 6px;
        }

        .summary-row.payment-row {
            color: #ff0000;
            font-weight: normal;
        }

        .balance-due-final {
            background-color: #f0f0f0;
            padding: 8px 10px;
            text-align: center;
            font-weight: bold;
            margin-top: 10px;
            font-size: 13px;
            height: 8mm;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* ========== SIGNATURE ========== */
        .signature-section {
            clear: both;
            margin-top: 20px;
            font-size: 9px;
            position: relative;
        }

        .signature-text {
            font-size: 13px;
            font-weight: bold;
            position: absolute;
            left: 0;
            top: 0;
        }

        .signature-line {
            display: inline-block;
            width: 50mm;
            border-bottom: 1px solid #000;
            margin-left: 43mm;
            vertical-align: bottom;
            height: 5px;
            margin-top: 20px;
        }

            /* ========== NOTES SECTION ========== */
          .notes-section {
              margin-top: 20px;
              font-size: 12px;
              width:50%;
          }

          .notes-label {
              font-weight: bold;
              margin-bottom: 5px;
              font-size: 13px;
          }

          .notes-content {
                padding: 8px;
                width: 180mm;
                font-size: 12px;
                line-height: 1.4;
                white-space: pre-wrap;   /* ⭐ KEY FIX */
                word-break: break-word;
            }

          .sub_total{
            font-weight: bold;
          }
        /* ========== PRINT OPTIMIZATION ========== */
        @media print {
            body {
                padding: 10mm 15mm;
            }

            .logo-image {
                height: 25mm;
            }

            .items-table th,
            .items-table td {
                padding: 2mm 1mm;
            }

            .summary-row {
                padding: 1.5mm 0;
            }

            .balance-due-final {
                padding: 2mm;
            }
        }
        /* .last_section{
            page-break-inside: avoid;
        } */
    </style>
</head>
<body>
   @php
    $defaultLogo = base_path('assets/images/dralogos.png');

    $company = $purchase->TblCompany; // may be null

    $companyLogo = ($company && $company->logo_upload)
        ? public_path('uploads/vendor/company/' . $company->logo_upload)
        : null;

    $logoPath = ($companyLogo && file_exists($companyLogo))
        ? $companyLogo
        : $defaultLogo;

    $logoBase64 = 'data:image/' . pathinfo($logoPath, PATHINFO_EXTENSION) .
        ';base64,' . base64_encode(file_get_contents($logoPath));
@endphp

<div class="logo-section">
<img src="{{ $logoBase64 }}" class="logo-image">
</div>
  <!-- @php
        $logoPath = base_path('assets/images/dralogos.png');
        $logoBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath));
        // dd($logoBase64 )
    @endphp


    <div class="logo-section">
    <img src="{{ $logoBase64 }}" class="logo-image">
    </div>
    <div class="logo-section">
        <img src="{{ asset('assets/images/dralogos.png') }}"
             alt="Dr. Aravind's IVF Logo"
             class="logo-image"
             onerror="this.style.display='none'">
    </div>  -->

    <!-- Top Section: Left and Right Info -->
    <div class="top-section">
        <!-- Left Column -->
        <div class="left-info">
            <div class="company-name">{{ $purchase->TblCompany->company_name ?? "Dr.Aravind's IVF Private Limited" }}</div>
            <div class="company-details">
                {{ $purchase->TblCompany->state ?? 'Tamil Nadu' }}<br>
                {{ $purchase->TblCompany->country ?? 'India' }}<br>
                {{ $purchase->TblCompany->phone ?? '+91 90 2012 2012' }}<br>
                {{ $purchase->TblCompany->email ?? 'info@draravindsivf.com' }}
            </div>

            <div class="vendor-section">
                <div class="section-label">Vendor Address</div>
                <div class="vendor-name">{{ $purchase->Tblvendor->display_name ?? 'SAFEMAX' }}</div>
                <div class="vendor-details">
                    {{ $purchase->TblBilling->address ?? '-' }}<br>
                    {{ $purchase->TblBilling->city ?? 'Gurugram' }}<br>
                    {{ $purchase->TblBilling->zip_code ?? '122002' }} {{ $purchase->TblBilling->state ?? 'Harayana' }}<br>
                    {{ $purchase->TblBilling->country ?? 'India' }}
                </div>
            </div>

            <div class="delivery-section">
                <div class="section-label">Delivery To:</div>
                <div class="vendor-details">{{ $purchase->delivery_address ?? '-' }}</div>
            </div>
        </div>

        <!-- Right Column -->
        <div class="right-info">
            <div class="quotation-title-main">Purchase Order</div>
            <div class="quotation-number">{{ $purchase->purchase_gen_order ?? '-' }}</div>
            <div class="quotation-number">{{ $purchase->purchase_order_number ?? '-' }}</div>
            <div class="balance-label">Balance Due</div>
            <div class="balance-amount">₹{{ number_format($purchase->balance_amount ?? 50000.00, 2) }}</div>

            <table class="order-info-table">
                <tr>
                    <td>Order Number :</td>
                    <td>{{ $purchase->order_number ?? '-' }}</td>
                </tr>
                <tr>
                    <td>Purchase Date :</td>
                    <td>{{ $purchase->bill_date ?? '-' }}</td>
                </tr>
                <tr>
                    <td>Due Date :</td>
                    <td>{{ $purchase->due_date ?? '-' }}</td>
                </tr>
                <tr>
                    <td>Terms :</td>
                    <td>{{ $purchase->payment_terms ?? '-' }}</td>
                </tr>
            </table>
        </div>
    </div>

    <!-- Subject -->
    <div class="subject-section">
        Subject : {{ $purchase->subject ?? '' }}
    </div>

    <!-- Items Table -->
    <table class="items-table">
        <thead>
            <tr>
                <th class="number">#</th>
                <th class="description">Item & Description</th>
                <!-- <th class="customer">Customer Details</th>
                <th class="account">Account</th> -->
                <th class="qty">Qty</th>
                <th class="rate">Rate</th>
                <!-- <th class="gst">GST</th> -->
                <th class="amount">Amount</th>
            </tr>
        </thead>
        <tbody>
            @if(isset($purchase->BillLines) && count($purchase->BillLines) > 0)
                @foreach($purchase->BillLines as $index => $line)
                    <tr>
                        <td class="number">{{ $index + 1 }}</td>
                        <td class="description">{{ $line->item_details ?? '4 KG ABC TYPE F/EXT REFILLING' }}</td>
                        <!-- <td class="customer">
                            ⚑ {{ $line->customer ?? "Aravind's IVF" }}<br>
                            Non-Billable
                        </td>
                        <td class="account">{{ $line->account ?? '' }}</td> -->
                        <td class="qty">{{ number_format($line->quantity ?? 1.00, 2) }}</td>
                        <td class="rate">{{ number_format($line->rate ?? 944.00, 2) }}</td>
                        <!-- <td class="gst">{{ number_format($line->gst_amount ?? 0.00, 2) }}</td> -->
                        <td class="amount">{{ number_format($line->amount ?? 944.00, 2) }}</td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td class="number">1</td>
                    <td class="description">4 KG ABC TYPE F/EXT REFILLING</td>
                    <td class="customer">
                        <span class="customer-flag">⚑</span> Aravind's IVF<br>
                        Non-Billable
                    </td>
                    <td class="account"></td>
                    <td class="qty">1.00</td>
                    <td class="rate">944.00</td>
                    <td class="gst">0.00</td>
                    <td class="amount">944.00</td>
                </tr>
            @endif
        </tbody>
    </table>

    <!-- Summary Section -->
    <div class="summary-section">
        <div class="summary-box">
        @php
            $gstSummary = [];

            if(isset($purchase->BillLines) && count($purchase->BillLines) > 0) {
                foreach ($purchase->BillLines as $line) {

                    if (!is_numeric($line->gst_rate) || $line->gst_rate <= 0) continue;

                    $gstType = strtoupper(trim($line->gst_type ?? 'GST'));

                    if ($gstType === 'IGST') {
                        // IGST — full rate; amount stored in gst_amount column
                        if (is_numeric($line->gst_amount) && $line->gst_amount > 0) {
                            $key = "IGST_{$line->gst_rate}";
                            if (!isset($gstSummary[$key])) {
                                $gstSummary[$key] = ['type' => 'IGST', 'rate' => $line->gst_rate, 'amount' => 0];
                            }
                            $gstSummary[$key]['amount'] += $line->gst_amount;
                        }
                    } else {
                        // GST — split into CGST (half) and SGST (half)
                        $halfRate = $line->gst_rate / 2;

                        if (is_numeric($line->cgst_amount) && $line->cgst_amount > 0) {
                            $key = "CGST_{$halfRate}";
                            if (!isset($gstSummary[$key])) {
                                $gstSummary[$key] = ['type' => 'CGST', 'rate' => $halfRate, 'amount' => 0];
                            }
                            $gstSummary[$key]['amount'] += $line->cgst_amount;
                        }

                        if (is_numeric($line->sgst_amount) && $line->sgst_amount > 0) {
                            $key = "SGST_{$halfRate}";
                            if (!isset($gstSummary[$key])) {
                                $gstSummary[$key] = ['type' => 'SGST', 'rate' => $halfRate, 'amount' => 0];
                            }
                            $gstSummary[$key]['amount'] += $line->sgst_amount;
                        }
                    }
                }
            }
        @endphp

            <div class="summary-row">
                <div class="summary-label">Sub Total</div>
                <div class="summary-value sub_total">₹{{ number_format($purchase->sub_total_amount ?? 944.00, 2) }}</div>
            </div>

            @if(count($gstSummary) > 0)
                @foreach($gstSummary as $entry)
                    <div class="summary-row">
                        <div class="summary-label">{{ $entry['type'] }} {{ number_format($entry['rate'], 2) }}%</div>
                        <div class="summary-value">₹{{ number_format($entry['amount'], 2) }}</div>
                    </div>
                @endforeach
            @endif

            <div class="summary-row">
                <div class="summary-label">Discount(-)</div>
                <div class="summary-value">₹{{ number_format($purchase->discount_amount ?? 0.00, 2) }}</div>
            </div>

            <div class="summary-row">
                <div class="summary-label">TDS(-)</div>
                <div class="summary-value">₹{{ number_format($purchase->tax_amount ?? 0.00, 2) }}</div>
            </div>

            <div class="summary-row total-row">
                <div class="summary-label">TOTAL</div>
                <div class="summary-value">₹{{ number_format($purchase->grand_total_amount ?? 944, 0) }}</div>
            </div>

            @if(isset($purchase->export_name) && $purchase->export_name !== null)
                <div class="summary-row">
                    <div class="summary-label">{{ $purchase->export_name }}</div>
                    <div class="summary-value">₹{{ number_format($purchase->export_amount ?? 0.00, 2) }}</div>
                </div>
            @endif

            <div class="summary-row payment-row">
                <div class="summary-label">Payments Made</div>
                <div class="summary-value">(-) ₹{{ number_format($purchase->partially_payment ?? 0.00, 2) }}</div>
            </div>

            <div class="balance-due-final">
                Balance Due : ₹{{ number_format($purchase->balance_amount ?? 944.00, 2) }}
            </div>
        </div>
    </div>
    <div class="last_section">
        <!-- Notes Section -->
        <div class="notes-section">
            <div class="notes-label">Notes:</div>
            <div class="notes-content">
                {{ $purchase->note ?? '---' }}
            </div>
        </div>

        <!-- Signature -->
        <div class="signature-section">
            <span class="signature-text">Authorized Signature</span>
            <span class="signature-line"></span>
        </div>
    </div>
</body>
</html>