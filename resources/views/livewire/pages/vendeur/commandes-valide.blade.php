@extends('layouts.sidebare')

@section('title', 'Commandes validées')
@section('breadcrumb')
    <li class="breadcrumb-item active">Commandes validées</li>
@endsection

@section('content')
    @livewire('vendeur.commandes-validees')
@endsection