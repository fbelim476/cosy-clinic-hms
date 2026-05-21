@extends('layouts.app')
@section('title', 'Patient Registration')
@section('breadcrumb')
    <li><a href="{{ route('reception.dashboard') }}">Reception</a></li>
    <li>Register</li>
@endsection
@section('content')
<livewire:reception.patient-registration />
@endsection
