<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Patient Card — {{ $patient->patient_id }}</title>
    <style>
        body { font-family: Arial; margin: 0; padding: 10px; }
        .card { width: 340px; border: 2px solid #206bc4; border-radius: 8px; padding: 15px; }
        .logo { color: #206bc4; font-weight: bold; font-size: 14px; }
        .pid { font-size: 18px; font-weight: bold; }
        .qr { float: right; }
    </style>
</head>
<body onload="window.print()">
    <div class="card">
        <div class="qr">{!! $qr !!}</div>
        <div class="logo">CosyClinic HMS</div>
        <div class="pid">{{ $patient->patient_id }}</div>
        <p><strong>{{ $patient->name }}</strong></p>
        <p>{{ $patient->mobile }} | {{ $patient->age }} / {{ ucfirst($patient->gender ?? '-') }}</p>
        <p>{{ $patient->blood_group }} | {{ $patient->city }}</p>
    </div>
</body>
</html>
