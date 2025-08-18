<div>
  <!-- Statistiques (AdminLTE small-box) -->
  <div class="row g-3">
    <div class="col-lg-3 col-6">
      <div class="small-box bg-primary">
        <div class="inner">
          <h3>{{ $total }}</h3>
          <p>Total Tissus</p>
        </div>
        <div class="icon">
          <i class="bi bi-grid-3x3-gap-fill"></i>
        </div>
      </div>
    </div>
    <div class="col-lg-3 col-6">
      <div class="small-box bg-success">
        <div class="inner">
          <h3>{{ $available }}</h3>
          <p>Disponibles</p>
        </div>
        <div class="icon">
          <i class="bi bi-check-circle-fill"></i>
        </div>
      </div>
    </div>
    <div class="col-lg-3 col-6">
      <div class="small-box bg-warning">
        <div class="inner">
          <h3>{{ $stockSum }}m</h3>
          <p>Stock Total</p>
        </div>
        <div class="icon">
          <i class="bi bi-box-seam"></i>
        </div>
      </div>
    </div>
    <div class="col-lg-3 col-6">
      <div class="small-box bg-indigo">
        <div class="inner">
          <h3 class="mb-0">{{ number_format($stockValue, 0, ',', ' ') }}</h3>
          <p class="mb-0">Valeur Stock (FCFA)</p>
        </div>
        <div class="icon">
          <i class="bi bi-cash-coin"></i>
        </div>
      </div>
    </div>
  </div>

  @if (session()->has('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      <i class="bi bi-check-circle me-2"></i> {{ session('success') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  @endif

  <!-- Filtres (compacts et ergonomiques) -->
  <div class="card card-outline card-primary mb-3">
    <div class="card-header d-flex align-items-center justify-content-between">
      <h3 class="card-title mb-0"><i class="bi bi-funnel-fill me-2"></i>Filtres</h3>
      <div class="card-tools d-flex align-items-center ms-auto">
        <div class="me-2 text-muted small d-none d-md-block">{{ $tissus->total() }} résultats</div>
        <a href="{{ route('vendeur.ajouter-tissu') }}" class="btn btn-primary btn-sm" data-bs-toggle="tooltip" title="Ajouter un tissu">
          Ajouter un tissu
        </a>
        <button type="button" class="btn btn-outline-secondary btn-sm ms-2" data-bs-toggle="tooltip" title="Réinitialiser"
                wire:click="$set('search',''); $set('categorie',''); $set('disponible',''); $set('prixMin', null); $set('prixMax', null)">
          <i class="bi bi-arrow-counterclockwise"></i>
        </button>
      </div>
    </div>
    <div class="card-body py-3">
      <div class="row g-2 align-items-end">
        <div class="col-lg-4 col-md-6">
          <label class="visually-hidden">Recherche</label>
          <div class="input-group input-group-sm">
            <span class="input-group-text"><i class="bi bi-search"></i></span>
            <input type="text" class="form-control" placeholder="Nom, couleur, description" wire:model.debounce.300ms="search">
          </div>
        </div>
        <div class="col-lg-3 col-md-6">
          <label class="visually-hidden">Catégorie</label>
          <div class="input-group input-group-sm">
            <span class="input-group-text"><i class="bi bi-tags"></i></span>
            <select class="form-select" wire:model="categorie">
              <option value="">Toutes catégories</option>
              @foreach($categories as $categorie)
                <option value="{{ $categorie->id }}">{{ $categorie->nom }}</option>
              @endforeach
            </select>
          </div>
        </div>
        <div class="col-lg-2 col-md-4">
          <label class="visually-hidden">Disponibilité</label>
          <div class="input-group input-group-sm">
            <span class="input-group-text"><i class="bi bi-box"></i></span>
            <select class="form-select" wire:model="disponible">
              <option value="">Toutes</option>
              <option value="1">Disponible</option>
              <option value="0">Indisponible</option>
            </select>
          </div>
        </div>
        <div class="col-lg-3 col-md-8">
          <div class="d-flex">
            <div class="input-group input-group-sm me-2">
              <span class="input-group-text">Min</span>
              <input type="number" class="form-control" placeholder="0" wire:model.lazy="prixMin" min="0" step="100">
            </div>
            <div class="input-group input-group-sm">
              <span class="input-group-text">Max</span>
              <input type="number" class="form-control" placeholder="100000" wire:model.lazy="prixMax" min="0" step="100">
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Tableau -->
  <div class="card">
    <div class="card-header">
      <h3 class="card-title mb-0"><i class="bi bi-table me-2"></i>Liste des Tissus</h3>
    </div>
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-sm table-striped table-hover mb-0 align-middle">
          <thead class="table-light">
            <tr>
              <th>Nom</th>
              <th class="text-nowrap" style="width:130px">Prix</th>
              <th class="text-nowrap" style="width:120px">Stock</th>
              <th>Couleur</th>
              <th>Catégorie</th>
              <th class="text-end" style="width:170px">Actions</th>
            </tr>
          </thead>
          <tbody>
            @forelse($tissus as $tissu)
              <tr>
                <td class="fw-semibold">{{ $tissu->nom }}</td>
                <td class="text-nowrap">{{ number_format((float)$tissu->prix, 0, ',', ' ') }} FCFA</td>
                <td class="text-nowrap">
                  @if($tissu->stock > 10)
                    <span class="badge text-bg-success">{{ $tissu->stock }}m</span>
                  @elseif($tissu->stock > 0)
                    <span class="badge text-bg-warning">{{ $tissu->stock }}m</span>
                  @else
                    <span class="badge text-bg-danger">{{ $tissu->stock }}m</span>
                  @endif
                </td>
                <td>{{ $tissu->couleur ?? '-' }}</td>
                <td>
                  @if($tissu->categorie)
                    <span class="badge text-bg-info">{{ $tissu->categorie->nom }}</span>
                  @else
                    <span class="text-muted">-</span>
                  @endif
                </td>
                <td class="text-end">
                  <div class="btn-group btn-group-sm" role="group" aria-label="Actions">
                    <a href="{{ route('vendeur.historique-tissu', ['id' => $tissu->id]) }}" class="btn btn-outline-secondary" data-bs-toggle="tooltip" title="Historique">
                      <i class="bi bi-clock-history"></i>
                    </a>
                    <a href="{{ route('vendeur.tissu.edit', $tissu->id) }}" class="btn btn-outline-primary" data-bs-toggle="tooltip" title="Modifier">
                      <i class="bi bi-pencil-square"></i>
                    </a>
                    <button type="button" class="btn btn-outline-danger" data-bs-toggle="tooltip" title="Supprimer"
                            onclick="if(!confirm('Supprimer ce tissu ? Cette action est irréversible.')) return false;"
                            wire:click="supprimer({{ $tissu->id }})">
                      <i class="bi bi-trash"></i>
                    </button>
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="6" class="text-center py-5">
                  <i class="bi bi-box2-open fs-2 text-muted d-block mb-2"></i>
                  <div class="text-muted">Aucun tissu trouvé avec ces filtres.</div>
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
    <div class="card-footer clearfix">
      <div class="float-end">
        {{ $tissus->onEachSide(1)->links('pagination::bootstrap-5') }}
      </div>
    </div>
  </div>
</div>
