@extends('layouts.app')
@section('title', 'Hospital Branding')
@push('styles')<link rel="stylesheet" href="{{ asset('css/print-management.css') }}">@endpush
@section('breadcrumb')<li>Admin</li><li>Print Management</li><li>Branding</li>@endsection
@section('content')<livewire:admin.print-branding />@endsection
