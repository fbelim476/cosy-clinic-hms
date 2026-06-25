@extends('layouts.app')
@section('title', $title ?? 'Print Management')
@section('breadcrumb')
    <li>Admin</li>
    <li>Print Management</li>
    @if(!empty($crumb))<li>{{ $crumb }}</li>@endif
@endsection
@push('styles')
<link rel="stylesheet" href="{{ asset('css/print-management.css') }}">
@endpush
@section('content')
{{ $slot ?? '' }}
@yield('print-content')
@endsection
