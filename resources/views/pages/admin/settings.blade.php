@extends('layouts.app')
@section('title', 'Hospital Settings')
@section('breadcrumb')
    <li><a href="{{ route('admin.dashboard') }}">Admin</a></li>
    <li>Settings</li>
@endsection
@section('content')
<x-ui.page-header title="Hospital Settings" subtitle="Branding, print headers, and clinic configuration" icon="ti-settings" />

<x-ui.card title="General Configuration">
    <form method="POST" action="{{ route('admin.settings.update') }}">
        @csrf
        <div class="row g-3">
            @foreach([
                'hospital_name' => 'Hospital Name',
                'hospital_address' => 'Address',
                'hospital_phone' => 'Phone',
                'gst_number' => 'GST Number',
                'prescription_header' => 'Prescription Header',
                'invoice_footer' => 'Invoice Footer',
            ] as $key => $label)
                <div class="col-md-6">
                    <label class="cc-form-label">{{ $label }}</label>
                    <input type="text" name="{{ $key }}" value="{{ $settings[$key] ?? '' }}" class="form-control">
                </div>
            @endforeach
        </div>
        <div class="cc-sticky-actions mt-4 rounded">
            <button type="submit" class="btn btn-cc-primary"><i class="ti ti-device-floppy me-1"></i> Save Settings</button>
        </div>
    </form>
</x-ui.card>
@endsection
