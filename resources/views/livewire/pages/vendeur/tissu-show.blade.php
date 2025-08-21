@extends('layouts.sidebare')

@section('title', 'Détails du Tissu')
@section('breadcrumb', 'Détails du Tissu')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Détails du Tissu</h3>
                    <div class="card-tools">
                        <a href="{{ route('vendeur.gestion-tissus') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Retour
                        </a>
                        <a href="{{ route('vendeur.tissu.edit', $tissu->id) }}" class="btn btn-warning">
                            <i class="bi bi-pencil"></i> Modifier
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            @if($tissu->image)
                                <img src="{{ asset('storage/' . $tissu->image) }}" alt="{{ $tissu->nom }}" class="img-fluid rounded">
                            @else
                                <div class="bg-secondary text-white d-flex align-items-center justify-content-center rounded" style="height: 300px;">
                                    <i class="bi bi-image" style="font-size: 4rem;"></i>
                                </div>
                            @endif
                        </div>
                        <div class="col-md-8">
                            <h4>{{ $tissu->nom }}</h4>
                            <p class="text-muted">{{ $tissu->description }}</p>
                            
                            <div class="row mt-4">
                                <div class="col-md-6">
                                    <h5>Informations générales</h5>
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>Prix :</strong></td>
                                            <td>{{ number_format($tissu->prix, 2) }} CFA</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Stock :</strong></td>
                                            <td>
                                                @if($tissu->stock > 10)
                                                    <span class="badge bg-success">{{ $tissu->stock }} mètres</span>
                                                @elseif($tissu->stock > 0)
                                                    <span class="badge bg-warning">{{ $tissu->stock }} mètres</span>
                                                @else
                                                    <span class="badge bg-danger">{{ $tissu->stock }} mètres</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            {{-- Couleur supprimée --}}
                                        </tr>
                                        <tr>
                                            <td><strong>Catégorie :</strong></td>
                                            <td>
                                                @if($tissu->categorie)
                                                    <span class="badge" style="background-color: {{ $tissu->categorie->couleur_hex ?? '#007bff' }}; color: white;">
                                                        {{ $tissu->categorie->nom }}
                                                    </span>
                                                @else
                                                    <span class="text-muted">Aucune catégorie</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Disponible :</strong></td>
                                            <td>
                                                @if($tissu->disponible)
                                                    <span class="badge bg-success">Oui</span>
                                                @else
                                                    <span class="badge bg-danger">Non</span>
                                                @endif
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <h5>Détails techniques</h5>
                                    <table class="table table-borderless">
                                        @if($tissu->origine)
                                        <tr>
                                            <td><strong>Origine :</strong></td>
                                            <td>{{ $tissu->origine }}</td>
                                        </tr>
                                        @endif
                                        @if($tissu->composition)
                                        <tr>
                                            <td><strong>Composition :</strong></td>
                                            <td>{{ $tissu->composition }}</td>
                                        </tr>
                                        @endif
                                        <tr>
                                            <td><strong>Date d'ajout :</strong></td>
                                            <td>{{ $tissu->created_at->format('d/m/Y H:i') }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Dernière modification :</strong></td>
                                            <td>{{ $tissu->updated_at->format('d/m/Y H:i') }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 