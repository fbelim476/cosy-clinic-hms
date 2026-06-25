@extends('layouts.app')
@section('title', 'Header Builder')
@push('styles')<link rel="stylesheet" href="{{ asset('css/print-management.css') }}">@endpush
@section('breadcrumb')<li>Admin</li><li>Print Management</li><li>Header Builder</li>@endsection
@section('content')<livewire:admin.print-header-builder />@endsection
