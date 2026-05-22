<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Prescription — {{ $visit->visit_number }}</title>
    <style>
        body { font-family: 'Times New Roman', serif; font-size: 13px; max-width: 700px; margin: 0 auto; padding: 20px; }
        .rx { font-size: 28px; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #333; padding: 6px; text-align: left; }
        .header { border-bottom: 2px solid #000; padding-bottom: 10px; }
    </style>
</head>
<body onload="window.print()">
    <div class="header">
        <h2 style="margin:0">{{ \App\Models\HospitalSetting::get('prescription_header', 'CosyClinic HMS Prescription') }}</h2>
        <p>Dr. {{ $visit->doctor?->user?->name }} — {{ $visit->doctor?->specialization }}</p>
    </div>
    <p><strong>Patient:</strong> {{ $visit->patient->name }} | <strong>ID:</strong> {{ $visit->patient->patient_id }} | <strong>Date:</strong> {{ now()->format('d/m/Y') }}</p>
    @if($visit->consultation?->diagnosis)<p><strong>Diagnosis:</strong> {{ $visit->consultation->diagnosis }}</p>@endif
    <p class="rx">℞</p>
    @foreach($visit->prescriptions as $rx)
        <table>
            <thead><tr><th>Medicine</th><th>Dosage</th><th>Frequency</th><th>Days</th><th>Qty</th></tr></thead>
            <tbody>
                @foreach($rx->items as $item)
                    <tr>
                        <td>{{ $item->medicine_name }}</td>
                        <td>{{ $item->dosage }}</td>
                        <td>{{ ($item->morning?'M-':'') }}{{ ($item->afternoon?'A-':'') }}{{ ($item->night?'N':'') }} {{ $item->food_timing ? '('.$item->food_timing.' food)' : '' }}</td>
                        <td>{{ $item->days }}</td>
                        <td>{{ $item->quantity }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        @if($rx->instructions)<p><strong>Instructions:</strong> {{ $rx->instructions }}</p>@endif
    @endforeach
    @if($visit->consultation?->medical_advice)<p><strong>Advice:</strong> {{ $visit->consultation->medical_advice }}</p>@endif
    @if($visit->consultation?->follow_up_date)<p><strong>Follow-up:</strong> {{ $visit->consultation->follow_up_date->format('d/m/Y') }}</p>@endif
    <p style="margin-top:40px;text-align:right">____________________<br>Doctor Signature</p>
</body>
</html>
