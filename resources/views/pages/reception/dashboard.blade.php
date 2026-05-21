@extends('layouts.app')
@section('title', 'Reception Queue')
@section('breadcrumb')
    <li><a href="{{ auth()->user()->dashboardRoute() }}">Home</a></li>
    <li>Reception</li>
@endsection
@section('content')
<livewire:reception.dashboard />
@endsection
