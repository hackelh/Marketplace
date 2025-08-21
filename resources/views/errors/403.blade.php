@extends('layouts.adminlte')

@section('title', 'Accès refusé')
@section('breadcrumb', 'Erreur 403')

@section('content')
  <div class="alert alert-danger">
    <i class="bi bi-x-octagon me-1"></i> Accès non autorisé.
  </div>
@endsection