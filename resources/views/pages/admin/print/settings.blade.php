@extends('layouts.app')
@section('title', 'Print Settings')
@push('styles')<link rel="stylesheet" href="{{ asset('css/print-management.css') }}">@endpush
@section('breadcrumb')<li>Admin</li><li>Print Management</li><li>Settings</li>@endsection
@section('content')<livewire:admin.print-settings />@endsection
