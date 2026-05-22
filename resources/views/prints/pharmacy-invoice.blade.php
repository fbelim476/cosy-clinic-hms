<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Pharmacy Invoice {{ $order->order_number }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; max-width: 600px; margin: 0 auto; padding: 15px; }
        .header { text-align: center; border-bottom: 2px solid #333; }
        table { width: 100%; border-collapse: collapse; margin: 10px 0; }
        th, td { border: 1px solid #ccc; padding: 5px; }
        th { background: #f5f5f5; }
        .total { font-weight: bold; font-size: 14px; }
    </style>
</head>
<body onload="window.print()">
    <div class="header">
        <h2>{{ \App\Models\HospitalSetting::get('hospital_name', 'CosyClinic HMS') }}</h2>
        <div>Medical Store / Pharmacy Invoice</div>
        <div>GST: {{ \App\Models\HospitalSetting::get('gst_number', '') }}</div>
    </div>
    <p><strong>Invoice:</strong> {{ $order->order_number }} | <strong>Date:</strong> {{ $order->created_at->format('d/m/Y') }}</p>
    <p><strong>Patient:</strong> {{ $order->patientVisit->patient->name }} ({{ $order->patientVisit->patient->patient_id }})</p>
    <table>
        <thead><tr><th>#</th><th>Medicine</th><th>Qty</th><th>Rate</th><th>GST%</th><th>Amount</th></tr></thead>
        <tbody>
            @foreach($order->items as $i => $item)
                <tr>
                    <td>{{ $i+1 }}</td>
                    <td>{{ $item->medicine_name }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>₹{{ number_format($item->unit_price, 2) }}</td>
                    <td>{{ $item->gst_percent }}%</td>
                    <td>₹{{ number_format($item->total, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr><td colspan="5" class="total">Subtotal</td><td>₹{{ number_format($order->subtotal, 2) }}</td></tr>
            <tr><td colspan="5">Tax</td><td>₹{{ number_format($order->tax, 2) }}</td></tr>
            <tr><td colspan="5" class="total">Grand Total</td><td>₹{{ number_format($order->total, 2) }}</td></tr>
        </tfoot>
    </table>
    <p style="text-align:center">{{ \App\Models\HospitalSetting::get('invoice_footer', 'Thank you!') }}</p>
</body>
</html>
