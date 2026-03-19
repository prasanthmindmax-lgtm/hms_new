<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Expense Details Export</title>
</head>
<body>
    <h2 style="text-align:center;">Expense Details - {{ strtoupper($type) }}</h2>

    <table border="1" cellspacing="0" cellpadding="5" width="100%">
        <thead>
            <tr style="background:#f2f2f2;">
                <th>#</th>
                <th>Bill Date</th>
                <th>Bill Number</th>
                <th>Vendor Name</th>
                <th>Nature of Account</th>
                <th>Invoice Payable</th>
                <th>TDS</th>
                <th>GST</th>
                <th>Final Payable</th>
            </tr>
        </thead>
        <tbody>
            @php $i = 1; @endphp
            @foreach($details as $bill)
                @foreach($bill->BillLines as $line)
                <tr>
                    <td>{{ $i++ }}</td>
                    <td>{{ $bill->bill_date }}</td>
                    <td>{{ $bill->bill_gen_number }}</td>
                    <td>{{ $bill->Tblvendor->display_name ?? '-' }}</td>
                    <td>{{ $line->account ?? '-' }}</td>
                    <td>{{ number_format($bill->sub_total_amount ?? 0, 2) }}</td>
                    <td>{{ number_format($bill->tds_amount ?? 0, 2) }}</td>
                    <td>{{ number_format($line->gst_amount ?? 0, 2) }}</td>
                    <td>{{ number_format($bill->grand_total_amount ?? 0, 2) }}</td>
                </tr>
                @endforeach
            @endforeach
        </tbody>
        <tfoot>
            <tr style="font-weight:bold; background:#e8f4ff;">
                <td colspan="5" style="text-align:right;">Total</td>
                <td>{{ number_format($totalInvoiceAmount, 2) }}</td>
                <td>{{ number_format($totalTDS, 2) }}</td>
                <td>{{ number_format($totalGST, 2) }}</td>
                <td>{{ number_format($totalFinalAmount, 2) }}</td>
            </tr>
        </tfoot>
    </table>

    <p style="margin-top:20px;">
        Exported on: {{ now()->format('d-m-Y H:i:s') }}
    </p>
</body>
</html>
