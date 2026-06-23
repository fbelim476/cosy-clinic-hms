@extends('layouts.app')
@section('title', 'Appointments')
@section('breadcrumb')
    <li><a href="{{ route('reception.dashboard') }}">Reception</a></li>
    <li>Appointments</li>
@endsection
@section('content')
<livewire:reception.appointments />
@endsection
