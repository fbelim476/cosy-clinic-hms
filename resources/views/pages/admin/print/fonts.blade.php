@extends('layouts.app')
@section('title', 'Font Library')
@push('styles')<link rel="stylesheet" href="{{ asset('css/print-management.css') }}">@endpush
@section('breadcrumb')<li>Admin</li><li>Print Management</li><li>Fonts</li>@endsection
@section('content')<livewire:admin.print-fonts />@endsection
