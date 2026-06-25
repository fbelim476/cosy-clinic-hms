@extends('layouts.app')
@section('title', 'QR & Barcode')
@push('styles')<link rel="stylesheet" href="{{ asset('css/print-management.css') }}">@endpush
@section('breadcrumb')<li>Admin</li><li>Print Management</li><li>QR & Barcode</li>@endsection
@section('content')<livewire:admin.print-qr-barcode />@endsection
