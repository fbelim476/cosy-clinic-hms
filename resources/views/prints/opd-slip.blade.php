<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>OPD Slip — {{ $visit->visit_number }}</title>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; font-size: 12px; max-width: 400px; margin: 0 auto; padding: 15px; }
        .header { text-align: center; border-bottom: 3px solid #0ea5e9; padding-bottom: 12px; }
        .header h1 { margin: 0; font-size: 17px; color: #0c4a6e; font-weight: 700; }
        .header .sub { font-size: 10px; color: #64748b; margin-top: 4px; }
        .token { font-size: 56px; font-weight: 800; text-align: center; color: #0ea5e9; margin: 18px 0; letter-spacing: -2px; }
        table { width: 100%; border-collapse: collapse; }
        td { padding: 4px 0; vertical-align: top; }
        .qr { text-align: center; margin-top: 10px; }
        @media print { .no-print { display: none; } }
    </style>
</head>
<body onload="window.print()">
    <div class="no-print" style="margin-bottom:10px"><button onclick="window.print()">Print</button> <button onclick="window.close()">Close</button></div>
    <div class="header">
        <h1>{{ \App\Models\HospitalSetting::get('hospital_name', 'ClinicCare HMS') }}</h1>
        <div class="sub">{{ \App\Models\HospitalSetting::get('hospital_address', '') }}</div>
        <div class="sub" style="margin-top:6px;font-weight:600;color:#0ea5e9">OPD REGISTRATION SLIP</div>
    </div>
    <div class="token">TOKEN #{{ $visit->token_number }}</div>
    <table>
        <tr><td><strong>Visit No:</strong></td><td>{{ $visit->visit_number }}</td></tr>
        <tr><td><strong>Date:</strong></td><td>{{ $visit->registered_at?->format('d/m/Y h:i A') }}</td></tr>
        <tr><td><strong>Patient:</strong></td><td>{{ $visit->patient->name }} ({{ $visit->patient->patient_id }})</td></tr>
        <tr><td><strong>Mobile:</strong></td><td>{{ $visit->patient->mobile }}</td></tr>
        <tr><td><strong>Age/Sex:</strong></td><td>{{ $visit->patient->age }} / {{ ucfirst($visit->patient->gender ?? '-') }}</td></tr>
        <tr><td><strong>Department:</strong></td><td>{{ $visit->department?->name ?? 'General OPD' }}</td></tr>
        <tr><td><strong>Doctor:</strong></td><td>{{ $visit->doctor ? 'Dr. '.$visit->doctor->user->name : 'As assigned' }}</td></tr>
        <tr><td><strong>Complaint:</strong></td><td>{{ $visit->chief_complaint }}</td></tr>
        @if($visit->isEmergency())<tr><td colspan="2" style="color:red;font-weight:bold">*** EMERGENCY ***</td></tr>@endif
    </table>
    <div class="qr">{!! $qr !!}</div>
    <p style="text-align:center;font-size:10px;margin-top:10px">Please wait for your token to be called. Keep this slip safe.</p>
</body>
</html>
