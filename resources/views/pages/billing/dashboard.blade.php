@extends('layouts.app')
@section('title', 'Billing & Payments')
@section('breadcrumb')
    <li><a href="{{ auth()->user()->dashboardRoute() }}">Home</a></li>
    <li>Billing</li>
@endsection
@section('content')
<x-ui.page-header title="Billing & Invoices" subtitle="Today's collection: ₹{{ number_format($todayRevenue, 2) }}" icon="ti-receipt-2" :live="true" />

<div class="row g-3 mb-4">
    <div class="col-md-4">
        <x-ui.stat-card label="Today's Collection" :value="number_format($todayRevenue, 2)" prefix="₹" icon="ti-currency-rupee" variant="gradient" />
    </div>
    <div class="col-md-4">
        <x-ui.stat-card label="Total Invoices" :value="$invoices->total()" icon="ti-file-invoice" />
    </div>
    <div class="col-md-4">
        <x-ui.stat-card label="Pending Payment" :value="$invoices->where('payment_status', '!=', 'paid')->count()" icon="ti-clock-exclamation" :danger="true" />
    </div>
</div>

<x-ui.card title="Invoice Ledger" :padding="false">
    <x-ui.table>
        <x-slot:head>
            <tr>
                <th>Invoice</th><th>Patient</th><th>Type</th><th>Total</th><th>Paid</th><th>Due</th><th>Status</th><th class="text-end">Action</th>
            </tr>
        </x-slot:head>
        @forelse($invoices as $inv)
            <tr>
                <td><a href="{{ route('print.invoice', $inv) }}" target="_blank" class="fw-semibold">{{ $inv->invoice_number }}</a></td>
                <td>{{ $inv->patient->name }}</td>
                <td><span class="badge bg-azure-lt">{{ strtoupper($inv->type) }}</span></td>
                <td class="fw-semibold">₹{{ number_format($inv->total, 2) }}</td>
                <td class="text-success">₹{{ number_format($inv->paid_amount, 2) }}</td>
                <td>₹{{ number_format($inv->due_amount, 2) }}</td>
                <td>
                    <span class="badge bg-{{ $inv->payment_status === 'paid' ? 'success' : ($inv->payment_status === 'partial' ? 'warning' : 'secondary') }}">
                        {{ ucfirst($inv->payment_status) }}
                    </span>
                </td>
                <td class="text-end">
                    @if($inv->payment_status !== 'paid')
                        <form method="POST" action="{{ route('billing.payment', $inv) }}" class="d-inline-flex gap-1 flex-wrap justify-content-end">
                            @csrf
                            <input type="number" name="amount" value="{{ $inv->due_amount }}" step="0.01" class="form-control form-control-sm" style="width:100px">
                            <select name="method" class="form-select form-select-sm" style="width:90px">
                                <option value="cash">Cash</option>
                                <option value="upi">UPI</option>
                                <option value="card">Card</option>
                            </select>
                            <button class="btn btn-sm btn-cc-primary">Pay</button>
                        </form>
                    @else
                        <span class="text-muted small">Settled</span>
                    @endif
                </td>
            </tr>
        @empty
            <tr><td colspan="8"><x-ui.empty-state title="No invoices" message="Pharmacy and OPD bills will appear here." /></td></tr>
        @endforelse
    </x-ui.table>
    <x-slot:footer>{{ $invoices->links() }}</x-slot:footer>
</x-ui.card>
@endsection
