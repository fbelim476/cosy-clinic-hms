@extends('layouts.app')
@section('title', 'Settings')
@section('content')
<div class="page-header mb-4"><h2 class="page-title">Hospital Settings</h2></div>
<form method="POST" action="{{ route('admin.settings.update') }}">
    @csrf
    <div class="glass-card card">
        <div class="card-body row g-3">
            @foreach(['hospital_name'=>'Hospital Name','hospital_address'=>'Address','hospital_phone'=>'Phone','gst_number'=>'GST Number','prescription_header'=>'Prescription Header','invoice_footer'=>'Invoice Footer'] as $key => $label)
                <div class="col-md-6">
                    <label class="form-label">{{ $label }}</label>
                    <input type="text" name="{{ $key }}" value="{{ $settings[$key] ?? '' }}" class="form-control">
                </div>
            @endforeach
        </div>
        <div class="card-footer"><button class="btn btn-primary">Save Settings</button></div>
    </div>
</form>
@endsection
