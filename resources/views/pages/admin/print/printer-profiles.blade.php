@extends('layouts.app')
@section('title', 'Printer Profiles')
@push('styles')<link rel="stylesheet" href="{{ asset('css/print-management.css') }}">@endpush
@section('breadcrumb')<li>Admin</li><li>Print Management</li><li>Printer Profiles</li>@endsection
@section('content')<livewire:admin.print-printer-profiles />@endsection
