<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Expense Details - {{ $type }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #000; padding: 6px; text-align: left; }
        th { background-color: #f2f2f2; }
        h2 { text-align: center; margin-bottom: 10px; }
    </style>
</head>
<body>
    <h2>Expense Details Report ({{ $type }})</h2>

    <table>
        <thead>
            <tr>
                <th>Bill No</th>
                <th>Vendor</th>
                <th>Bill Date</th>
                <th>Nature of Account</th>
                <th>Invoice Payable</th>
                <th>TDS</th>
                <th>GST</th>
                <th>Final Payable</th>
            </tr>
        </thead>
        <tbody>
            @foreach($details as $bill)
                @foreach($bill->BillLines as $line)
                    <tr>
                        <td>{{ $bill->bill_gen_number }}</td>
                        <td>{{ $bill->Tblvendor->display_name ?? '—' }}</td>
                        <td>{{ $bill->bill_date }}</td>
                        <td>{{ $line->account }}</td>
                        <td style="text-align:right;">{{ number_format($bill->sub_total_amount, 2) }}</td>
                        <td style="text-align:right;">{{ number_format($line->gst_amount, 2) }}</td>
                        <td style="text-align:right;">{{ number_format($bill->tds_amount, 2) }}</td>
                        <td style="text-align:right;">{{ number_format($bill->grand_total_amount, 2) }}</td>
                    </tr>
                @endforeach
            @endforeach
        </tbody>
    </table>

    <br>
    <table style="width:50%; margin-left:auto;">
        <tr><th>Total Invoice Payable</th><td style="text-align:right;">{{ number_format($totalInvoiceAmount, 2) }}</td></tr>
        <tr><th>Total GST</th><td style="text-align:right;">{{ number_format($totalGST, 2) }}</td></tr>
        <tr><th>Total TDS</th><td style="text-align:right;">{{ number_format($totalTDS, 2) }}</td></tr>
        <tr><th>Total Final Payable</th><td style="text-align:right;">{{ number_format($totalFinalAmount, 2) }}</td></tr>
    </table>
</body>
</html>
