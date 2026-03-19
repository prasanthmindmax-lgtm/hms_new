<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Purchase Order</title>
    <style>
        body { font-family: Arial, sans-serif; color: #000; }
        .header { background: #004080; color: #fff; padding: 10px; text-align: right; }
        .logo { float: left; }
        .po-title { font-size: 20px; font-weight: bold; }
        .section { border: 1px solid #004080; margin-top: 10px; padding: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #004080; padding: 8px; text-align: left; }
        th { background: #004080; color: white; }
        .totals { margin-top: 15px; text-align: right; }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">
            <img src="https://draravinds.com/hms/assets/images/dralogos.png"  alt="Logo">
        </div>
        <div class="po-title">PURCHASE ORDER</div>
    </div>

    <div class="section">
        <strong>Date:</strong> {{ \Carbon\Carbon::parse($purchase->created_at)->format('d/m/Y') }} <br>
        <strong>Purchase Order No:</strong> {{ $purchase->purchase_order_number }} <br>
        <strong>Customer No:</strong> {{ "Aravind's IVF" }}
    </div>

    <div class="section">
        <strong>Vendor:</strong><br>
        {{ $purchase->Tblvendor->display_name ?? '' }} <br>
        {{ $purchase->TblBilling->address ?? '' }} <br>
        {{ $purchase->Tblvendor->mobile ?? '' }}
    </div>

    <table>
        <thead>
            <tr>
                <th>Item</th>
                <th>Qty</th>
                <th>Unit Price</th>
                <th>GST Amount</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($purchase->BillLines as $line)
            <tr>
                <td>{{ $line->item_details }}</td>
                <td>{{ $line->quantity }}</td>
                <td>${{ number_format($line->rate, 2) }}</td>
                <td>${{ number_format($line->gst_amount, 2) }}</td>
                <td>${{ number_format($line->amount, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totals">
        <p><strong>Subtotal:</strong> ${{ number_format($purchase->sub_total_amount ?? 0, 2) }}</p>
        <p><strong>Tax ({{ $purchase->tax_rate ?? 0 }}%):</strong> ${{ number_format($purchase->tax_amount ?? 0, 2) }}</p>
        <p><strong>Discount:</strong> ${{ number_format($purchase->discount_amount ?? 0, 2) }}</p>
        <h3>Total: ${{ number_format($purchase->grand_total_amount ?? 0, 2) }}</h3>
    </div>

    <p>Remarks / Instructions: {{ $purchase->remarks ?? 'N/A' }}</p>

    <p style="margin-top:20px;">Regards,<br>Purchase Department</p>
</body>
</html>
