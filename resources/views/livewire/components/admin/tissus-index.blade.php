<div>
  <div class="card mb-3">
    <div class="card-header d-flex flex-column flex-md-row gap-2 align-items-md-center justify-content-between">
      <div class="d-flex gap-2 flex-wrap">
        <input type="text" class="form-control" style="min-width:240px" placeholder="Rechercher (nom, couleur, description)" wire:model.live.debounce.300ms="search">
        <select class="form-select" wire:model.live="categorie">
          <option value="">Toutes catégories</option>
          @foreach($categories as $cat)
            <option value="{{ $cat['id'] }}">{{ $cat['name'] }}</option>
          @endforeach
        </select>
        <select class="form-select" wire:model.live="disponible">
          <option value="">Disponibilité</option>
          <option value="1">Disponible</option>
          <option value="0">Indisponible</option>
        </select>
        <input type="number" step="0.01" class="form-control" style="max-width:140px" placeholder="Prix min" wire:model.live="prixMin">
        <input type="number" step="0.01" class="form-control" style="max-width:140px" placeholder="Prix max" wire:model.live="prixMax">
      </div>
    </div>
  </div>

  <div class="card">
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-striped table-hover mb-0">
          <thead class="table-light">
            <tr>
              <th>Nom</th>
              <th style="width:120px">Prix</th>
              <th style="width:110px">Stock</th>
              <th>Couleur</th>
              <th>Catégorie</th>
              <th>Vendeur</th>
              <th style="width:120px">Dispo</th>
              <th style="width:160px">Ajouté le</th>
            </tr>
          </thead>
          <tbody>
            @forelse($tissus as $t)
              <tr>
                <td class="fw-semibold">{{ $t->nom }}</td>
                <td>{{ number_format((float)$t->prix, 2, ',', ' ') }} FCFA</td>
                <td>{{ $t->stock }}</td>
                <td>{{ $t->couleur ?? '-' }}</td>
                <td>{{ $t->categorie?->name ?? '-' }}</td>
                <td>{{ $t->vendeur?->name ?? '-' }}</td>
                <td>
                  <span class="badge text-bg-{{ $t->en_stock ? 'success' : 'secondary' }}">{{ $t->en_stock ? 'En stock' : 'Rupture' }}</span>
                </td>
                <td>{{ $t->created_at?->format('d/m/Y H:i') }}</td>
              </tr>
            @empty
              <tr>
                <td colspan="8" class="text-center py-4">Aucun tissu trouvé.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
    <div class="card-footer d-flex justify-content-end">
      {{ $tissus->onEachSide(1)->links('pagination::bootstrap-5') }}
    </div>
  </div>
</div>
