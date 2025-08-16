@extends('layouts.adminlte')

@section('title', 'Commandes en cours')
@section('breadcrumb')
    <li class="breadcrumb-item active">Commandes en cours</li>
@endsection

@section('content')
    @livewire('vendeur.commandes-en-cours')
@endsection