@extends('layouts.app')
@section('title', 'Billing')
@section('content')
<div class="page-header mb-4">
    <h2 class="page-title"><i class="ti ti-receipt text-primary me-2"></i>Billing & Invoices</h2>
    <div class="text-muted">Today's Collection: <strong class="text-success">₹{{ number_format($todayRevenue, 2) }}</strong></div>
</div>
<div class="glass-card card">
    <div class="table-responsive">
        <table class="table table-vcenter card-table">
            <thead>
                <tr><th>Invoice</th><th>Patient</th><th>Type</th><th>Total</th><th>Paid</th><th>Due</th><th>Status</th><th>Action</th></tr>
            </thead>
            <tbody>
                @foreach($invoices as $inv)
                    <tr>
                        <td><a href="{{ route('print.invoice', $inv) }}" target="_blank">{{ $inv->invoice_number }}</a></td>
                        <td>{{ $inv->patient->name }}</td>
                        <td><span class="badge bg-secondary">{{ strtoupper($inv->type) }}</span></td>
                        <td>₹{{ number_format($inv->total, 2) }}</td>
                        <td>₹{{ number_format($inv->paid_amount, 2) }}</td>
                        <td>₹{{ number_format($inv->due_amount, 2) }}</td>
                        <td><span class="badge bg-{{ $inv->payment_status === 'paid' ? 'success' : 'warning' }}">{{ $inv->payment_status }}</span></td>
                        <td>
                            @if($inv->payment_status !== 'paid')
                            <form method="POST" action="{{ route('billing.payment', $inv) }}" class="d-flex gap-1">
                                @csrf
                                <input type="number" name="amount" value="{{ $inv->due_amount }}" step="0.01" class="form-control form-control-sm" style="width:90px">
                                <select name="method" class="form-select form-select-sm" style="width:80px">
                                    <option value="cash">Cash</option>
                                    <option value="upi">UPI</option>
                                    <option value="card">Card</option>
                                </select>
                                <button class="btn btn-sm btn-success">Pay</button>
                            </form>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="card-footer">{{ $invoices->links() }}</div>
</div>
@endsection
