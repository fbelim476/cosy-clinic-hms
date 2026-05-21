@extends('layouts.app')
@section('title', 'Doctor Workspace')
@section('breadcrumb')
    <li><a href="{{ auth()->user()->dashboardRoute() }}">Home</a></li>
    <li>Consultation</li>
@endsection
@section('content')
<livewire:doctor.queue />
@endsection
