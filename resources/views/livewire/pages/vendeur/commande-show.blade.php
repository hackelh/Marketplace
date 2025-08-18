@extends('layouts.sidebare')

@section('title', 'Détail Commande')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('vendeur.commandes.en-cours') }}">Commandes</a></li>
    <li class="breadcrumb-item active">{{ $commande->numero_commande }}</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row g-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title mb-0">Commande {{ $commande->numero_commande }}</h5>
                        <small class="text-muted">Créée le {{ $commande->created_at->format('d/m/Y H:i') }}</small>
                    </div>
                    <span class="badge bg-{{ $commande->statut_couleur }}">{{ $commande->statut_libelle }}</span>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-4">
                            <h6 class="fw-bold mb-2">Client</h6>
                            <div class="text-muted">{{ $commande->client->name }}</div>
                            <div class="text-muted">{{ $commande->client->email }}</div>
                            @if($commande->adresse_livraison)
                                <div class="text-muted mt-2"><i class="bi bi-geo"></i> {{ $commande->adresse_livraison }}</div>
                            @endif
                        </div>
                        <div class="col-md-4">
                            <h6 class="fw-bold mb-2">Livraison</h6>
                            <div>Date prévue: {{ optional($commande->date_livraison_prevue)->format('d/m/Y H:i') ?? '—' }}</div>
                            <div>Date effective: {{ optional($commande->date_livraison_effective)->format('d/m/Y H:i') ?? '—' }}</div>
                        </div>
                        <div class="col-md-4">
                            <h6 class="fw-bold mb-2">Paiement</h6>
                            <div>Méthode: {{ $commande->methode_paiement ?? '—' }}</div>
                            <div>Statut: {{ $commande->statut_paiement ?? '—' }}</div>
                            <div class="fw-bold mt-1">Montant total: {{ number_format($commande->montant_total, 0, ',', ' ') }} CFA</div>
                        </div>
                    </div>
                    @if($commande->notes)
                        <div class="mt-3">
                            <h6 class="fw-bold mb-2">Notes</h6>
                            <div class="text-muted">{{ $commande->notes }}</div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h6 class="mb-0">Articles</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped mb-0">
                            <thead>
                                <tr>
                                    <th>Tissu</th>
                                    <th>Quantité</th>
                                    <th>PU</th>
                                    <th>Total</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($commande->details as $detail)
                                    <tr>
                                        <td>
                                            <div class="fw-semibold">{{ $detail->tissu->nom ?? 'Tissu supprimé' }}</div>
                                            @if($detail->notes)
                                                <div class="text-muted small">{{ $detail->notes }}</div>
                                            @endif
                                        </td>
                                        <td>{{ $detail->quantite }}</td>
                                        <td>{{ number_format($detail->prix_unitaire, 0, ',', ' ') }} CFA</td>
                                        <td class="fw-bold">{{ number_format($detail->prix_total, 0, ',', ' ') }} CFA</td>
                                        <td>
                                            @if($detail->tissu)
                                                <a href="{{ route('vendeur.tissu.show', $detail->tissu->id) }}" class="btn btn-sm btn-outline-secondary">Voir tissu</a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 d-flex gap-2">
            <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">Retour</a>
            <a href="{{ route('vendeur.commandes.en-cours') }}" class="btn btn-primary">Liste des commandes</a>
        </div>
    </div>
</div>
@endsection
