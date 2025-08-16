<div>
    <!--begin::Row-->
    <div class="row">
        <!--begin::Col-->
        <div class="col-lg-3 col-6">
            <!--begin::Small Box Widget 1-->
            <div class="small-box text-bg-info">
                <div class="inner">
                    <h3>{{ $statistiques['total_mouvements'] }}</h3>
                    <p>Total Mouvements</p>
                </div>
                <svg
                    class="small-box-icon"
                    fill="currentColor"
                    viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg"
                    aria-hidden="true"
                >
                    <path
                        d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"
                    ></path>
                </svg>
            </div>
            <!--end::Small Box Widget 1-->
        </div>
        <!--end::Col-->
        
        <!--begin::Col-->
        <div class="col-lg-3 col-6">
            <!--begin::Small Box Widget 2-->
            <div class="small-box text-bg-success">
                <div class="inner">
                    <h3>{{ $statistiques['ajouts'] }}<sup class="fs-5">m</sup></h3>
                    <p>Total Ajouts</p>
                </div>
                <svg
                    class="small-box-icon"
                    fill="currentColor"
                    viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg"
                    aria-hidden="true"
                >
                    <path
                        d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"
                    ></path>
                </svg>
            </div>
            <!--end::Small Box Widget 2-->
        </div>
        <!--end::Col-->
        
        <!--begin::Col-->
        <div class="col-lg-3 col-6">
            <!--begin::Small Box Widget 3-->
            <div class="small-box text-bg-danger">
                <div class="inner">
                    <h3>{{ $statistiques['ventes'] }}<sup class="fs-5">m</sup></h3>
                    <p>Total Ventes</p>
                </div>
                <svg
                    class="small-box-icon"
                    fill="currentColor"
                    viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg"
                    aria-hidden="true"
                >
                    <path
                        d="M7 11V7a5 5 0 0110 0v4h2a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2h2zm8-4v4H9V7a3 3 0 016 0z"
                    ></path>
                </svg>
            </div>
            <!--end::Small Box Widget 3-->
        </div>
        <!--end::Col-->
        
        <!--begin::Col-->
        <div class="col-lg-3 col-6">
            <!--begin::Small Box Widget 4-->
            <div class="small-box text-bg-warning">
                <div class="inner">
                    <h3>{{ $statistiques['ajustements'] }}<sup class="fs-5">m</sup></h3>
                    <p>Ajustements</p>
                </div>
                <svg
                    class="small-box-icon"
                    fill="currentColor"
                    viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg"
                    aria-hidden="true"
                >
                    <path
                        d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"
                    ></path>
                </svg>
            </div>
            <!--end::Small Box Widget 4-->
        </div>
        <!--end::Col-->
    </div>
    <!--end::Row-->

    <!--begin::Row-->
    <div class="row">
        <!--begin::Col-->
        <div class="col-12">
            <!--begin::Card-->
            <div class="card">
                <!--begin::Card Header-->
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="bi bi-clock-history me-2"></i>
                        Historique des Mouvements - {{ $tissu->nom }} ({{ $tissu->couleur }})
                    </h3>
                    <div class="card-tools">
                        <span class="badge text-bg-primary">Stock actuel: {{ $tissu->stock }} mètres</span>
                    </div>
                </div>
                <!--end::Card Header-->
                
                <!--begin::Card Body-->
                <div class="card-body">
                    <!--begin::Filters-->
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <label class="form-label">Type de mouvement</label>
                            <select wire:model.live="typeFilter" class="form-select">
                                <option value="">Tous les types</option>
                                @foreach($typesMouvement as $key => $libelle)
                                    <option value="{{ $key }}">{{ $libelle }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Date début</label>
                            <input type="date" wire:model.live="dateDebut" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Date fin</label>
                            <input type="date" wire:model.live="dateFin" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">&nbsp;</label>
                            <button wire:click="$set('typeFilter', '')" 
                                    wire:click="$set('dateDebut', '')" 
                                    wire:click="$set('dateFin', '')"
                                    class="btn btn-secondary w-100">
                                <i class="bi bi-arrow-clockwise"></i> Réinitialiser
                            </button>
                        </div>
                    </div>
                    <!--end::Filters-->
                    
                    <!--begin::Table-->
                    @if($historique->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Type</th>
                                        <th>Mouvement</th>
                                        <th>Stock</th>
                                        <th>Utilisateur</th>
                                        <th>Détails</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($historique as $mouvement)
                                        <tr>
                                            <td>
                                                <i class="bi bi-calendar3 me-1"></i>
                                                {{ $mouvement->created_at->format('d/m/Y H:i') }}
                                            </td>
                                            <td>
                                                <span class="badge text-bg-{{ $mouvement->type_couleur }}">
                                                    <i class="bi {{ $mouvement->type_icone }} me-1"></i>
                                                    {{ $mouvement->type_libelle }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="text-{{ $mouvement->quantite_mouvement > 0 ? 'success' : 'danger' }}">
                                                    <i class="bi bi-arrow-{{ $mouvement->quantite_mouvement > 0 ? 'up' : 'down' }} me-1"></i>
                                                    {{ $mouvement->quantite_mouvement > 0 ? '+' : '' }}{{ $mouvement->quantite_mouvement }} m
                                                </span>
                                                @if($mouvement->motif)
                                                    <br><small class="text-muted">{{ $mouvement->motif }}</small>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge text-bg-secondary">
                                                    {{ $mouvement->quantite_avant }} → {{ $mouvement->quantite_apres }} m
                                                </span>
                                            </td>
                                            <td>
                                                <i class="bi bi-person me-1"></i>
                                                {{ $mouvement->utilisateur->name }}
                                            </td>
                                            <td>
                                                @if($mouvement->notes)
                                                    <small class="text-muted">{{ Str::limit($mouvement->notes, 50) }}</small>
                                                @endif
                                                @if($mouvement->reference)
                                                    <br><small class="text-info">Ref: {{ $mouvement->reference }}</small>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <!--begin::Pagination-->
                        <div class="d-flex justify-content-center mt-3">
                            {{ $historique->links() }}
                        </div>
                        <!--end::Pagination-->
                    @else
                        <!--begin::Empty State-->
                        <div class="text-center py-5">
                            <i class="bi bi-clock-history text-muted" style="font-size: 4rem;"></i>
                            <h4 class="text-muted mt-3">Aucun mouvement</h4>
                            <p class="text-muted">Aucun mouvement de stock enregistré pour ce tissu.</p>
                        </div>
                        <!--end::Empty State-->
                    @endif
                    <!--end::Table-->
                </div>
                <!--end::Card Body-->
                
                <!--begin::Card Footer-->
                <div class="card-footer">
                    <a href="{{ route('vendeur.gestion-tissus') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Retour à la gestion
                    </a>
                </div>
                <!--end::Card Footer-->
            </div>
            <!--end::Card-->
        </div>
        <!--end::Col-->
    </div>
    <!--end::Row-->
</div>
