<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statement of Accounts</title>
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
        .header {
            margin-bottom: 20px;
        }
        .vendor-info {
            float: left;
            width: 48%;
        }
        .company-info {
            float: right;
            width: 48%;
            text-align: right;
        }
        .clearfix::after {
            content: "";
            clear: both;
            display: table;
        }
        .statement-title {
            text-align: right;
            margin-top: 30px;
            margin-bottom: 10px;
        }
        .statement-title h3 {
            font-size: 14pt;
            font-weight: bold;
            margin: 0;
        }
        .statement-title p {
            font-size: 11pt;
            margin: 5px 0 0 0;
        }
        .summary-box {
            float: right;
            width: 48%;
            border: 1px solid #000;
            margin-bottom: 20px;
        }
        .summary-header {
            background-color: #f0f0f0;
            font-weight: bold;
            padding: 5px 8px;
            border-bottom: 1px solid #000;
        }
        .summary-row {
            padding: 5px 8px;
            border-bottom: 1px solid #000;
            display: flex;
            justify-content: space-between;
        }
        .summary-row:last-child {
            border-bottom: none;
        }
        .summary-row .label {
            font-weight: <?php echo $balance > 0 ? 'bold' : 'normal'; ?>;
        }
        .summary-row .value {
            font-family: 'dejavusans', sans-serif;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            /* margin-top: 150px; */
            clear: both;
        }
        th {
            background-color: #333333;
            color: white;
            font-weight: bold;
            padding: 8px 5px;
            text-align: center;
        }
        td {
            padding: 8px 5px;
            border-bottom: 1px solid #ddd;
        }
        .amount, .payment, .balance {
            text-align: right;
            font-family: 'dejavusans', sans-serif;
        }
        .type-column {
            text-align: center;
        }
        .balance-due-row {
            margin-top: 10px;
            text-align: right;
            font-weight: bold;
            font-size: 11pt;
        }
        .balance-due-row span {
            font-family: 'dejavusans', sans-serif;
            margin-left: 10px;
        }
        .vendor-address {
            font-size: 9pt;
            line-height: 1.4;
        }
        .company-detail {
            font-size: 9pt;
            margin: 2px 0;
        }
        .company-name {
            font-size: 11pt;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .vendor-name {
            font-size: 12pt;
            font-weight: bold;
            margin-bottom: 5px;
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

    </style>
</head>
<body>
@php
    $defaultLogo = base_path('assets/images/dralogos.png');


    $logoPath =  $defaultLogo;

    $logoBase64 = 'data:image/' . pathinfo($logoPath, PATHINFO_EXTENSION) .
        ';base64,' . base64_encode(file_get_contents($logoPath));
@endphp

<div class="header clearfix">
        <div class="logo-section">
        <img src="{{ $logoBase64 }}" class="logo-image">
        </div>
        <!-- Vendor Info (Left) -->
        <div class="vendor-info">
            <div style="font-weight: bold; margin-bottom: 10px;">To</div>
            <div class="vendor-name">{{ $vendor->display_name ?? 'NADESA SERVICE SATATION' }}</div>
            <div class="vendor-address">
                {{ $billing->address ?? 'null' }}<br>
                {{ $billing->city ?? 'null' }}<br>
                {{ ($billing->state ?? 'null') . ' ' . ($billing->zip_code ?? 'null') }}
            </div>
        </div>
        
        <!-- Company Info (Right) -->
        <div class="company-info">
            <div class="company-name">{{ "Dr.Aravind's IVF Private Limited" }}</div>
            <div class="company-detail">{{  'Tamil Nadu' }}</div>
            <div class="company-detail">{{  'Chennai' }}</div>
            <div class="company-detail">{{ '+91 90 2012 2012' }}</div>
            <div class="company-detail">{{ 'info@draravindsivf.com' }}</div>
            <!-- <div class="company-name">{{ $billing->company_name ?? "Dr.Aravind's IVF Private Limited" }}</div>
            <div class="company-detail">{{ $billing->state ?? 'Tamil Nadu' }}</div>
            <div class="company-detail">{{ $billing->city ?? 'Chennai' }}</div>
            <div class="company-detail">{{ $billing->phone ?? '+91 90 2012 2012' }}</div>
            <div class="company-detail">{{ $billing->email ?? 'info@draravindsivf.com' }}</div> -->
        </div>
    </div>

    <!-- Statement Title -->
    <div class="statement-title">
        <h3>Statement of Accounts</h3>
        <p>{{ $from->format('d/m/Y') }} to {{ $to->format('d/m/Y') }}</p>
    </div>

    <!-- Account Summary Box -->
    <div class="summary-box">
        <div class="summary-header">Account Summary</div>
        <div class="summary-row">
            <span class="label">Billed Amount</span>
            <span class="value"> :  {{ number_format($billed, 2) }}</span>
        </div>
        <div class="summary-row">
            <span class="label">Amount Paid</span>
            <span class="value"> :  {{ number_format($paid, 2) }}</span>
        </div>
        <div class="summary-row">
            <span class="label" style="font-weight: bold;">Balance Due</span>
            <span class="value" style="font-weight: bold;">: {{ number_format($balance, 2) }}</span>
        </div>
    </div>

    <!-- Transaction Table -->
    <table>
        <thead>
            <tr>
                <th style="width: 15%;">Date</th>
                <th style="width: 12%;">Transactions</th>
                <th style="width: 35%;">Details</th>
                <th style="width: 12%;">Amount</th>
                <th style="width: 12%;">Payments</th>
                <th style="width: 14%;">Balance</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transactions as $transaction)
            <tr>
                <td style="text-align: center;">{{ $transaction['date'] }}</td>
                <td style="text-align: center;">{{ $transaction['type'] }}</td>
                <td>{{ $transaction['details'] }}</td>
                <td class="amount">{{ $transaction['amount'] > 0 ? number_format($transaction['amount'], 2) : '' }}</td>
                <td class="payment">{{ $transaction['payment'] > 0 ? number_format($transaction['payment'], 2) : '' }}</td>
                <td class="balance">{{ number_format($transaction['balance'], 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Final Balance Due -->
    <div class="balance-due-row">
        Balance Due <span>:  {{ number_format($balance, 2) }}</span>
    </div>
</body>
</html>