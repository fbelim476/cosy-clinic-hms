<!DOCTYPE html>
<html>
<head><meta charset="utf-8"><title>Invoice {{ $invoice->invoice_number }}</title>
<style>body{font-family:Arial;font-size:12px;max-width:600px;margin:0 auto;padding:15px}table{width:100%;border-collapse:collapse}th,td{border:1px solid #ccc;padding:5px}</style>
</head>
<body onload="window.print()">
<h2 style="text-align:center">{{ \App\Models\HospitalSetting::get('hospital_name') }}</h2>
<p><strong>Invoice:</strong> {{ $invoice->invoice_number }} | <strong>Date:</strong> {{ $invoice->created_at->format('d/m/Y') }}</p>
<p><strong>Patient:</strong> {{ $invoice->patient->name }}</p>
<table>
<thead><tr><th>Description</th><th>Qty</th><th>Rate</th><th>Total</th></tr></thead>
<tbody>@foreach($invoice->items as $item)<tr><td>{{ $item->description }}</td><td>{{ $item->quantity }}</td><td>₹{{ $item->unit_price }}</td><td>₹{{ $item->total }}</td></tr>@endforeach</tbody>
<tfoot>
<tr><th colspan="3">Grand Total</th><th>₹{{ number_format($invoice->total, 2) }}</th></tr>
<tr><th colspan="3">Paid</th><th>₹{{ number_format($invoice->paid_amount, 2) }}</th></tr>
<tr><th colspan="3">Due</th><th>₹{{ number_format($invoice->due_amount, 2) }}</th></tr>
</tfoot>
</table>
</body>
</html>
