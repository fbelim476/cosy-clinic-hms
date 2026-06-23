@extends('layouts.app')
@section('title', 'Doctor Management')
@section('breadcrumb')
    <li><a href="{{ route('admin.dashboard') }}">Admin</a></li>
    <li>Doctors</li>
@endsection
@section('content')
<livewire:admin.doctors-management />
@endsection
