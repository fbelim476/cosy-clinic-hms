@extends('layouts.app')
@section('title', 'Import / Export')
@push('styles')<link rel="stylesheet" href="{{ asset('css/print-management.css') }}">@endpush
@section('breadcrumb')<li>Admin</li><li>Print Management</li><li>Import / Export</li>@endsection
@section('content')<livewire:admin.print-import-export />@endsection
