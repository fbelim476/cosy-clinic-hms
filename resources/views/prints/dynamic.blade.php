<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Print' }}</title>
    @if(!empty($paperCss))
        <style>{!! $paperCss !!}</style>
    @endif
    <style>
        .pt-toolbar { display:flex; gap:8px; padding:12px; background:#f8fafc; border-bottom:1px solid #e2e8f0; }
        .pt-toolbar button { padding:8px 16px; border-radius:8px; border:none; cursor:pointer; font-weight:600; font-size:13px; }
        .pt-btn-print { background:linear-gradient(135deg,#0ea5e9,#06b6d4); color:#fff; }
        .pt-btn-pdf { background:#1e293b; color:#fff; }
        .pt-btn-close { background:#e2e8f0; color:#334155; }
        @media print { .no-print { display:none !important; } }
        body.embed .pt-toolbar { display:none; }
    </style>
</head>
<body class="{{ ($embed ?? false) ? 'embed' : '' }}">
    @unless($embed ?? false)
        <div class="pt-toolbar no-print">
            <button type="button" class="pt-btn-print" onclick="window.print()">Print</button>
            @if(!empty($pdfUrl))
                <a href="{{ $pdfUrl }}" class="pt-btn-pdf" style="text-decoration:none;display:inline-flex;align-items:center;padding:8px 16px;border-radius:8px;">Download PDF</a>
            @endif
            <button type="button" class="pt-btn-close" onclick="window.close()">Close</button>
        </div>
    @endunless

    {!! $html !!}

    @if(($autoPrint ?? false) && !($embed ?? false))
        <script>window.addEventListener('load',()=>setTimeout(()=>window.print(),400));</script>
    @endif
</body>
</html>
