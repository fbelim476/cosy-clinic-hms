@extends('layouts.app')
@section('title', 'Pharmacy POS')
@section('breadcrumb')
    <li><a href="{{ auth()->user()->dashboardRoute() }}">Home</a></li>
    <li>Pharmacy</li>
@endsection
@section('content')
<livewire:pharmacy.queue />
@endsection
