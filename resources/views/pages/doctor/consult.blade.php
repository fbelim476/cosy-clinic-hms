@extends('layouts.app')
@section('title', 'Consultation')
@section('content')
<livewire:doctor.consultation :visit="$visit" />
@endsection
