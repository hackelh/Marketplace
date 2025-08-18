@extends('layouts.sidebare')

@section('title', 'Historique du Tissu')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('vendeur.gestion-tissus') }}">Gestion des Tissus</a></li>
    <li class="breadcrumb-item active">Historique</li>
@endsection

@section('content')
    @livewire('vendeur.historique-tissu-view', ['tissuId' => $tissuId])
@endsection 