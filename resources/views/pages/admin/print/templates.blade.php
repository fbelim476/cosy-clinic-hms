@extends('layouts.app')
@section('title', 'Print Templates')
@push('styles')<link rel="stylesheet" href="{{ asset('css/print-management.css') }}">@endpush
@section('breadcrumb')<li>Admin</li><li>Print Management</li><li>Templates</li>@endsection
@section('content')<livewire:admin.print-templates />@endsection
