@extends('layouts.app')
@section('title', 'Medicine Inventory')
@section('breadcrumb')
    <li><a href="{{ route('admin.dashboard') }}">Admin</a></li>
    <li>Medicines</li>
@endsection
@section('content')
    <livewire:admin.medicines-inventory />
@endsection
