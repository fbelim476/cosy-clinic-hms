@extends('layouts.app')
@section('title', 'Laboratory')
@section('breadcrumb')
    <li><a href="{{ auth()->user()->dashboardRoute() }}">Home</a></li>
    <li>Lab</li>
@endsection
@section('content')
<x-ui.page-header title="Laboratory" subtitle="{{ $pending->count() }} pending tests today" icon="ti-test-pipe-2" :live="true" />

<div class="row g-4">
    <div class="col-lg-6">
        <x-ui.card title="Pending Tests" subtitle="{{ $pending->count() }} awaiting results">
            @forelse($pending as $order)
                <div class="border-bottom pb-3 mb-3" style="border-color:var(--cc-glass-border)!important">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <strong>{{ $order->patientVisit->patient->name }}</strong>
                            <div class="small text-muted">{{ $order->labTest->name }}</div>
                        </div>
                        <span class="badge bg-warning-lt">Pending</span>
                    </div>
                    <form method="POST" action="{{ route('lab.complete', $order) }}" enctype="multipart/form-data">
                        @csrf
                        <textarea name="result_values" class="form-control form-control-sm mb-2" rows="2" placeholder="Result values (e.g. Hb: 12.5)"></textarea>
                        <div class="d-flex gap-2 flex-wrap">
                            <input type="file" name="report" class="form-control form-control-sm flex-grow-1" accept=".pdf,.jpg,.png">
                            <button class="btn btn-sm btn-cc-primary">Complete</button>
                        </div>
                    </form>
                </div>
            @empty
                <x-ui.empty-state icon="ti-test-pipe-off" title="All caught up" message="No pending lab orders." />
            @endforelse
        </x-ui.card>
    </div>
    <div class="col-lg-6">
        <x-ui.card title="Completed Today">
            <x-ui.table>
                <x-slot:head><tr><th>Patient</th><th>Test</th><th>Time</th></tr></x-slot:head>
                @foreach($completed as $order)
                    <tr>
                        <td>{{ $order->patientVisit->patient->name }}</td>
                        <td>{{ $order->labTest->name }}</td>
                        <td class="text-muted small">{{ $order->completed_at?->format('h:i A') }}</td>
                    </tr>
                @endforeach
            </x-ui.table>
        </x-ui.card>
    </div>
</div>
@endsection
