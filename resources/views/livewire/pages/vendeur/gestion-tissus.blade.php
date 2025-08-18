@extends('layouts.sidebare')

@section('title', 'Gestion des Tissus')
@section('breadcrumb')
    <li class="breadcrumb-item active">Gestion des Tissus</li>
@endsection

@section('content')
    @livewire('vendeur.gestion-tissus')
@endsection