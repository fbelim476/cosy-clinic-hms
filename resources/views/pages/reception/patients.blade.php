@extends('layouts.app')
@section('title', 'Patient Search')
@section('content')
<div class="page-header mb-4">
    <h2 class="page-title"><i class="ti ti-search text-primary me-2"></i>Patient Search</h2>
</div>
<form method="GET" class="glass-card card mb-3">
    <div class="card-body">
        <div class="input-group">
            <input type="text" name="q" value="{{ request('q') }}" class="form-control form-control-lg" placeholder="Search by name, mobile, patient ID...">
            <button class="btn btn-primary">Search</button>
        </div>
    </div>
</form>
<div class="glass-card card">
    <div class="table-responsive">
        <table class="table table-vcenter card-table">
            <thead><tr><th>Patient ID</th><th>Name</th><th>Mobile</th><th>Age</th><th>Gender</th><th></th></tr></thead>
            <tbody>
                @forelse($results as $p)
                    <tr>
                        <td><code>{{ $p->patient_id }}</code></td>
                        <td>{{ $p->name }}</td>
                        <td>{{ $p->mobile }}</td>
                        <td>{{ $p->age }}</td>
                        <td>{{ ucfirst($p->gender ?? '-') }}</td>
                        <td>
                            <a href="{{ route('reception.patients.show', $p) }}" class="btn btn-sm btn-primary">History</a>
                            <a href="{{ route('print.patient-card', $p) }}" target="_blank" class="btn btn-sm btn-outline-secondary"><i class="ti ti-id"></i></a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center text-muted">No patients found</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
