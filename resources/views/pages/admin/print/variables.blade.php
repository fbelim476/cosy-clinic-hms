@extends('layouts.app')
@section('title', 'Print Variables')
@push('styles')<link rel="stylesheet" href="{{ asset('css/print-management.css') }}">@endpush
@section('breadcrumb')<li>Admin</li><li>Print Management</li><li>Variables</li>@endsection
@section('content')<livewire:admin.print-variables />@endsection
