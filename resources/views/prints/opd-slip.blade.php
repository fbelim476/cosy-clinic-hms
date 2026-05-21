@extends('layouts.print')

@section('title', 'OPD Slip #' . $visit->token_number)

@push('print-styles')
<style>
    @page { size: A5 portrait; margin: 8mm; }
    @media print {
        html, body { width: 80mm; max-width: 80mm; margin: 0; padding: 0; }
        .no-print { display: none !important; }
    }
    * { box-sizing: border-box; }
    body.opd-slip {
        font-family: 'Segoe UI', Inter, Arial, sans-serif;
        font-size: 11px;
        color: #0f172a;
        margin: 0 auto;
        padding: 12px;
        max-width: 400px;
        background: #fff;
    }
    body.opd-slip.embed { padding: 8px; }
    .opd-toolbar {
        display: flex; gap: 8px; margin-bottom: 12px;
    }
    .opd-toolbar button {
        padding: 8px 16px; border-radius: 8px; border: none; cursor: pointer;
        font-weight: 600; font-size: 13px;
    }
    .btn-print { background: linear-gradient(135deg, #0ea5e9, #06b6d4); color: #fff; }
    .btn-close { background: #e2e8f0; color: #334155; }
    .opd-header {
        text-align: center;
        border-bottom: 3px solid #0ea5e9;
        padding-bottom: 10px;
        margin-bottom: 8px;
    }
    .opd-header h1 { margin: 0; font-size: 16px; color: #0c4a6e; font-weight: 800; }
    .opd-header .addr { font-size: 9px; color: #64748b; margin-top: 4px; line-height: 1.4; }
    .opd-header .slip-type {
        display: inline-block; margin-top: 8px; padding: 4px 12px;
        background: linear-gradient(135deg, #e0f2fe, #ecfeff);
        color: #0369a1; font-size: 10px; font-weight: 700;
        letter-spacing: 0.15em; border-radius: 4px;
    }
    .opd-token-wrap {
        text-align: center;
        margin: 14px 0;
        padding: 12px;
        background: linear-gradient(160deg, #f0f9ff 0%, #ecfeff 100%);
        border: 2px dashed #0ea5e9;
        border-radius: 12px;
    }
    .opd-token-label { font-size: 10px; letter-spacing: 0.3em; color: #64748b; font-weight: 600; }
    .opd-token {
        font-size: 52px; font-weight: 900; line-height: 1;
        background: linear-gradient(180deg, #0284c7, #0ea5e9);
        -webkit-background-clip: text; -webkit-text-fill-color: transparent;
        background-clip: text;
        margin: 4px 0;
    }
    .opd-token.emergency { color: #dc2626; -webkit-text-fill-color: #dc2626; }
    .opd-meta { width: 100%; border-collapse: collapse; font-size: 10px; }
    .opd-meta td { padding: 3px 0; vertical-align: top; }
    .opd-meta td:first-child { width: 38%; color: #64748b; font-weight: 600; }
    .opd-qr { text-align: center; margin: 10px 0 6px; }
    .opd-qr svg, .opd-qr img { max-width: 90px; height: auto; }
    .opd-footer {
        text-align: center; font-size: 9px; color: #64748b;
        border-top: 1px dashed #cbd5e1; padding-top: 8px; margin-top: 8px;
    }
    .opd-er {
        text-align: center; color: #fff; background: #dc2626;
        font-weight: 800; padding: 6px; border-radius: 6px; margin: 8px 0;
        font-size: 11px; letter-spacing: 0.1em;
    }
</style>
@endpush

@section('body-class', 'opd-slip' . ($embed ?? false ? ' embed' : ''))

@section('content')
    @unless($embed ?? false)
        <div class="opd-toolbar no-print">
            <button type="button" class="btn-print" onclick="window.print()">🖨 Print Slip</button>
            <button type="button" class="btn-close" onclick="window.close()">Close</button>
        </div>
    @endunless

    <div class="opd-header">
        <h1>{{ \App\Models\HospitalSetting::get('hospital_name', 'ClinicCare HMS') }}</h1>
        <div class="addr">{{ \App\Models\HospitalSetting::get('hospital_address', '') }}</div>
        <div class="slip-type">OPD REGISTRATION SLIP</div>
    </div>

    @if($visit->isEmergency())
        <div class="opd-er">⚠ EMERGENCY PATIENT</div>
    @endif

    <div class="opd-token-wrap">
        <div class="opd-token-label">YOUR TOKEN</div>
        <div class="opd-token {{ $visit->isEmergency() ? 'emergency' : '' }}">#{{ $visit->token_number }}</div>
    </div>

    <table class="opd-meta">
        <tr><td>Visit No</td><td>{{ $visit->visit_number }}</td></tr>
        <tr><td>Date/Time</td><td>{{ $visit->registered_at?->format('d/m/Y h:i A') }}</td></tr>
        <tr><td>Patient</td><td><strong>{{ $visit->patient->name }}</strong><br><small>{{ $visit->patient->patient_id }}</small></td></tr>
        <tr><td>Mobile</td><td>{{ $visit->patient->mobile }}</td></tr>
        <tr><td>Age / Sex</td><td>{{ $visit->patient->age ?? '—' }} / {{ ucfirst($visit->patient->gender ?? '-') }}</td></tr>
        <tr><td>Department</td><td>{{ $visit->department?->name ?? 'General OPD' }}</td></tr>
        <tr><td>Doctor</td><td>{{ $visit->doctor ? 'Dr. '.$visit->doctor->user->name : 'As assigned' }}</td></tr>
        @if($visit->chief_complaint)
            <tr><td>Complaint</td><td>{{ $visit->chief_complaint }}</td></tr>
        @endif
    </table>

    <div class="opd-qr">{!! $qr !!}</div>

    <div class="opd-footer">
        Please wait in the waiting area until your token is called.<br>
        Keep this slip for reference.
    </div>
@endsection

@push('print-scripts')
@if(!($embed ?? false))
<script>window.addEventListener('load', () => { setTimeout(() => window.print(), 400); });</script>
@endif
@endpush
