@extends('layouts.app')
@section('title', 'Print Dashboard')
@push('styles')<link rel="stylesheet" href="{{ asset('css/print-management.css') }}">@endpush
@section('breadcrumb')<li>Admin</li><li>Print Management</li><li>Dashboard</li>@endsection
@section('content')<livewire:admin.print-dashboard />@endsection
