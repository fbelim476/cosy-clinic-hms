@extends('layouts.app')
@section('title', 'Template Builder')
@push('styles')<link rel="stylesheet" href="{{ asset('css/print-management.css') }}">@endpush
@section('breadcrumb')<li>Admin</li><li>Print Management</li><li>Builder</li>@endsection
@section('content')
@if($template)
    <livewire:admin.print-template-builder :template="$template" />
@else
    <div class="alert alert-warning">Select a template to edit.</div>
@endif
@endsection
