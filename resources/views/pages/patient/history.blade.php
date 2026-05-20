@extends('layouts.app')
@section('title', 'Patient History — '.$patient->name)
@section('content')
<div class="page-header mb-4">
    <h2 class="page-title">{{ $patient->name }} <small class="text-muted">{{ $patient->patient_id }}</small></h2>
</div>
<div class="row g-4">
    <div class="col-lg-4">
        <div class="glass-card card">
            <div class="card-header"><h3 class="card-title">Profile</h3></div>
            <div class="card-body">
                <p><strong>Mobile:</strong> {{ $patient->mobile }}</p>
                <p><strong>Age/Gender:</strong> {{ $patient->age }} / {{ ucfirst($patient->gender ?? '-') }}</p>
                <p><strong>Blood Group:</strong> {{ $patient->blood_group ?? '-' }}</p>
                <p><strong>Allergies:</strong> {{ $patient->allergies ?? 'None recorded' }}</p>
                <p><strong>Diseases:</strong> {{ $patient->existing_diseases ?? '-' }}</p>
            </div>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="glass-card card">
            <div class="card-header"><h3 class="card-title">Visit Timeline</h3></div>
            <div class="card-body">
                <ul class="timeline">
                    @foreach($patient->visits as $visit)
                        <li class="mb-4 pb-3 border-bottom">
                            <div class="d-flex justify-content-between">
                                <strong>{{ $visit->created_at->format('d M Y') }} — {{ $visit->visit_number }}</strong>
                                <span class="badge {{ $visit->status->badgeClass() }}">{{ $visit->status->label() }}</span>
                            </div>
                            <p class="mb-1">{{ $visit->chief_complaint }}</p>
                            @if($visit->consultation?->diagnosis)
                                <p class="small"><strong>Diagnosis:</strong> {{ $visit->consultation->diagnosis }}</p>
                            @endif
                            @foreach($visit->prescriptions as $rx)
                                <ul class="small mb-0">
                                    @foreach($rx->items as $item)
                                        <li>{{ $item->medicine_name }} — {{ $item->dosage }}</li>
                                    @endforeach
                                </ul>
                            @endforeach
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
