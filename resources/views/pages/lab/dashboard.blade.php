@extends('layouts.app')
@section('title', 'Lab Panel')
@section('content')
<div class="page-header mb-4">
    <h2 class="page-title"><i class="ti ti-test-pipe text-primary me-2"></i>Lab Technician Panel</h2>
</div>
<div class="row g-4">
    <div class="col-lg-6">
        <div class="glass-card card">
            <div class="card-header bg-warning-lt"><h3 class="card-title">Pending Tests ({{ $pending->count() }})</h3></div>
            @foreach($pending as $order)
                <div class="card-body border-bottom">
                    <strong>{{ $order->patientVisit->patient->name }}</strong> — {{ $order->labTest->name }}
                    <form method="POST" action="{{ route('lab.complete', $order) }}" enctype="multipart/form-data" class="mt-2">
                        @csrf
                        <textarea name="result_values" class="form-control mb-2" placeholder="Result values..." rows="2"></textarea>
                        <input type="file" name="report" class="form-control mb-2" accept=".pdf,.jpg,.png">
                        <button class="btn btn-sm btn-success">Mark Completed</button>
                    </form>
                </div>
            @endforeach
            @if($pending->isEmpty())<div class="card-body text-muted">No pending tests</div>@endif
        </div>
    </div>
    <div class="col-lg-6">
        <div class="glass-card card">
            <div class="card-header"><h3 class="card-title">Completed Today</h3></div>
            <ul class="list-group list-group-flush">
                @foreach($completed as $order)
                    <li class="list-group-item">{{ $order->patientVisit->patient->name }} — {{ $order->labTest->name }}</li>
                @endforeach
            </ul>
        </div>
    </div>
</div>
@endsection
