@extends('layouts.app')
@section('title', 'Doctor Reports')
@section('breadcrumb')
    <li><a href="{{ route('doctor.dashboard') }}">Doctor</a></li>
    <li>Reports</li>
@endsection
@section('content')
<livewire:doctor.reports />
@endsection
