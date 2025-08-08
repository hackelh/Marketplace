@extends('layouts.sidebare')

@section('title', 'Dashboard Vendeur')
@section('breadcrumb', 'Dashboard')

@section('content')
    @include('livewire.components.vendeur.dashboard')
@endsection 