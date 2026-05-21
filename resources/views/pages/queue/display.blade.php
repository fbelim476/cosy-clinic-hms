{{--
    IMPORTANT: This page MUST extend layouts.display.
    Using only <livewire:queue.token-display /> without this layout renders
    a bare HTML fragment with no CSS (no <head>, no stylesheets).
--}}
@extends('layouts.display')

@section('content')
    <livewire:queue.token-display />
@endsection
