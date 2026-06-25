@extends('layouts.app')
@section('title', 'Paper Sizes')
@push('styles')<link rel="stylesheet" href="{{ asset('css/print-management.css') }}">@endpush
@section('breadcrumb')<li>Admin</li><li>Print Management</li><li>Paper Sizes</li>@endsection
@section('content')<livewire:admin.print-paper-sizes />@endsection
