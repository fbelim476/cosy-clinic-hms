@extends('layouts.app')
@section('title', 'Patient Directory')
@section('breadcrumb')
    <li><a href="{{ route('reception.dashboard') }}">Reception</a></li>
    <li>Patients</li>
@endsection
@section('content')
<x-ui.page-header title="Patient Search" subtitle="Find by name, mobile, or patient ID" icon="ti-search">
    <x-slot:actions>
        <a href="{{ route('reception.register') }}" class="btn btn-cc-primary"><i class="ti ti-user-plus me-1"></i> New Patient</a>
    </x-slot:actions>
</x-ui.page-header>

<x-ui.card :padding="false">
    <div class="p-3 border-bottom" style="border-color:var(--cc-glass-border)!important">
        <form method="GET" class="cc-search" style="max-width:100%">
            <i class="ti ti-search"></i>
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Search patients..." autofocus>
        </form>
    </div>
    <x-ui.table>
        <x-slot:head>
            <tr><th>Patient ID</th><th>Name</th><th>Mobile</th><th>Age</th><th>Gender</th><th class="text-end">Actions</th></tr>
        </x-slot:head>
        @forelse($results as $p)
            <tr>
                <td><code class="fw-semibold">{{ $p->patient_id }}</code></td>
                <td class="fw-semibold">{{ $p->name }}</td>
                <td>{{ $p->mobile }}</td>
                <td>{{ $p->age ?? '—' }}</td>
                <td>{{ ucfirst($p->gender ?? '—') }}</td>
                <td class="text-end">
                    <a href="{{ route('reception.patients.show', $p) }}" class="btn btn-sm btn-primary">History</a>
                    <a href="{{ route('print.patient-card', $p) }}" target="_blank" class="btn btn-sm btn-ghost-secondary btn-icon"><i class="ti ti-id"></i></a>
                </td>
            </tr>
        @empty
            <tr><td colspan="6"><x-ui.empty-state title="No patients found" message="Try a different search or register a new patient." /></td></tr>
        @endforelse
    </x-ui.table>
</x-ui.card>
@endsection
