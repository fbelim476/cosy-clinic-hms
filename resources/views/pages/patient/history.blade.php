@extends('layouts.app')
@section('title', $patient->name)
@section('breadcrumb')
    <li><a href="{{ route('reception.patients') }}">Patients</a></li>
    <li>{{ $patient->name }}</li>
@endsection
@section('content')
<x-ui.page-header :title="$patient->name" :subtitle="$patient->patient_id . ' · ' . $patient->mobile" icon="ti-user">
    <x-slot:actions>
        <a href="{{ route('print.patient-card', $patient) }}" target="_blank" class="btn btn-outline-primary btn-sm"><i class="ti ti-id"></i> Card</a>
        <a href="{{ route('reception.register') }}" class="btn btn-cc-primary btn-sm"><i class="ti ti-plus"></i> New Visit</a>
    </x-slot:actions>
</x-ui.page-header>

<div class="row g-4">
    <div class="col-lg-4">
        <x-ui.card title="Profile">
            <dl class="row g-2 mb-0 small">
                <dt class="col-5 text-muted">Blood Group</dt><dd class="col-7">{{ $patient->blood_group ?? '—' }}</dd>
                <dt class="col-5 text-muted">Age / Gender</dt><dd class="col-7">{{ $patient->age }} / {{ ucfirst($patient->gender ?? '—') }}</dd>
                <dt class="col-5 text-muted">Allergies</dt><dd class="col-7">{{ $patient->allergies ?? 'None' }}</dd>
                <dt class="col-5 text-muted">Conditions</dt><dd class="col-7">{{ $patient->existing_diseases ?? '—' }}</dd>
            </dl>
        </x-ui.card>
    </div>
    <div class="col-lg-8">
        <x-ui.card title="Medical Timeline">
            @foreach($patient->visits as $visit)
                <div class="timeline-item">
                    <div class="d-flex justify-content-between">
                        <strong>{{ $visit->created_at->format('d M Y') }}</strong>
                        <span class="badge {{ $visit->status->badgeClass() }}">{{ $visit->status->label() }}</span>
                    </div>
                    <p class="small mb-1 text-muted">{{ $visit->visit_number }} — {{ $visit->chief_complaint }}</p>
                    @if($visit->consultation?->diagnosis)
                        <p class="small mb-0"><strong>Diagnosis:</strong> {{ $visit->consultation->diagnosis }}</p>
                    @endif
                </div>
            @endforeach
        </x-ui.card>
    </div>
</div>
@endsection
