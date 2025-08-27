<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice #{{ $invoice->id }}</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 12px; color: #333; margin: 0; padding: 20px; }
        .header, .footer { width: 100%; text-align: center; }
        .header { border-bottom: 2px solid #444; padding-bottom: 10px; margin-bottom: 20px; }
        .header h2 { margin: 0; color: #2c3e50; }
        .company-info { text-align: left; }
        .invoice-info { text-align: right; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        table, th, td { border: 1px solid #444; }
        th, td { padding: 8px; text-align: left; }
        th { background: #f2f2f2; }
        .summary { margin-top: 20px; width: 100%; }
        .summary td { padding: 6px; text-align: right; }
        .summary .label { font-weight: bold; background: #f9f9f9; }
        .summary .grand { font-size: 14px; font-weight: bold; background: #e8e8e8; }
        .footer { position: fixed; bottom: 20px; left: 0; right: 0; font-size: 11px; border-top: 1px solid #444; padding-top: 10px; }
        .signature { margin-top: 40px; text-align: right; }
    </style>
</head>
<body>
    <!-- 🔹 Header -->
    <div class="header">
        <table width="100%">
            <tr>
                <td class="company-info">
                    <h2>💊 Your Pharmacy</h2>
                    <p>123 Main Street, Chattogram, Bangladesh</p>
                    <p>📞 +880 123 456 789 | ✉️ info@pharmacy.com</p>
                </td>
                <td class="invoice-info">
                    <h3>Invoice</h3>
                    <p><b>ID:</b> #{{ $invoice->id }}</p>
                    <p><b>Date:</b> {{ $invoice->invoice_date }}</p>
                </td>
            </tr>
        </table>
    </div>

    <!-- 🔹 Customer Info -->
    <p><b>Customer:</b> {{ $invoice->customer->name }}</p>
    <p><b>Phone:</b> {{ $invoice->customer->phone1 }}{{ $invoice->customer->phone2 ? ', '.$invoice->customer->phone2 : '' }}</p>
    <p><b>Notes:</b> {{ $invoice->notes ?? '-' }}</p>

    <!-- 🔹 Items -->
    <table>
        <thead>
            <tr>
                <th>Medicine</th>
                <th>Price</th>
                <th>Qty</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->items as $item)
                <tr>
                    <td>{{ $item->medicine->name }}</td>
                    <td>${{ number_format($item->price, 2) }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>${{ number_format($item->subtotal, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- 🔹 Totals -->
    <table class="summary">
        <tr>
            <td class="label">Subtotal</td>
            <td>${{ number_format($invoice->total, 2) }}</td>
        </tr>

        @if($invoice->discount > 0)
        <tr>
            <td class="label">Discount ({{ $invoice->discount }}%)</td>
            <td>- ${{ number_format(($invoice->total * $invoice->discount) / 100, 2) }}</td>
        </tr>
        @endif

        @if($invoice->tax > 0)
        <tr>
            <td class="label">Tax ({{ $invoice->tax }}%)</td>
            <td>+ ${{ number_format((($invoice->total - ($invoice->total * $invoice->discount / 100)) * $invoice->tax) / 100, 2) }}</td>
        </tr>
        @endif

        <tr class="grand">
            <td>Grand Total</td>
            <td>${{ number_format($invoice->grand_total, 2) }}</td>
        </tr>
    </table>

    <!-- 🔹 Signature -->
    <div class="signature">
        <p>__________________________</p>
        <p>Authorized Signature</p>
    </div>

    <!-- 🔹 Footer -->
    <div class="footer">
        💊 Thank you for choosing <b>Your Pharmacy</b>. Stay healthy & safe!
    </div>
</body>
</html>
